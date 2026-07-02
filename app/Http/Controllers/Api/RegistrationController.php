<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registration;

class RegistrationController extends Controller
{
    public function index()
    {
        $query = Registration::with([
            'user',
            'event',
            'ticketCategory',
            'refund'
        ]);

        $user = auth()->user();
        if ($user && $user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $registrations = $query->latest()->get();

        return response()->json([
            'message' => 'List Registrations',
            'data' => $registrations
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'seat_id' => 'nullable|integer',
            'seat_number' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
            'payment_gateway' => 'nullable|string|max:50',
            'payment_notes' => 'nullable|string',
        ]);

        $event = \App\Models\Event::findOrFail($request->event_id);
        $registration = null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $event, &$registration) {
            $seatId = $request->seat_id;
            $seatNumber = $request->seat_number;

            if ($event->has_seat_layout) {
                if (!$seatId && !$seatNumber) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'seat_id' => ['Pilihan kursi wajib diisi untuk event ini.'],
                    ]);
                }

                $query = \App\Models\Seat::where('event_id', $request->event_id)->lockForUpdate();

                if ($seatId) {
                    $seat = $query->where('id', $seatId)->first();
                } else {
                    $seat = $query->where('seat_number', $seatNumber)->first();
                }

                if (!$seat) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'seat_id' => ['Kursi tidak ditemukan untuk event ini.'],
                    ]);
                }

                if ($seat->ticket_category_id != $request->ticket_category_id) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'seat_id' => ['Kategori kursi tidak sesuai dengan kategori tiket yang dipilih.'],
                    ]);
                }

                if ($seat->status !== 'available') {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'seat_id' => ['Kursi sudah dibooking oleh pengguna lain.'],
                    ]);
                }

                // Lock the seat status to booked
                $seat->update(['status' => 'booked']);
                $seatNumber = $seat->seat_number; // Use official seat number
            }

            // Lock the ticket category record to prevent concurrent booking race conditions
            $ticketCategory = \App\Models\TicketCategory::where('id', $request->ticket_category_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Validate that the ticket category still has remaining quota
            if ($ticketCategory->remaining <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'ticket_category_id' => ['Kuota tiket untuk kategori ini sudah habis.'],
                ]);
            }

            $paymentAmount = $ticketCategory->price;

            // Baca waktu kedaluwarsa dari file config/payment.php (default: 60 menit)
            $expirationMinutes = config('payment.expiration_minutes', 60);

            $registration = Registration::create([
                'user_id' => auth()->id(),
                'event_id' => $request->event_id,
                'ticket_category_id' => $request->ticket_category_id,
                'seat_number' => $seatNumber,
                'qr_code' => 'QR-' . strtoupper(uniqid()),
                'is_checked_in' => false,
                'status' => 'pending', // Kompatibilitas dengan mobile app lama
                'registration_status' => 'active',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_amount' => $paymentAmount,
                'payment_gateway' => $request->payment_gateway,
                'payment_notes' => $request->payment_notes,
                'payment_expired_at' => now()->addMinutes($expirationMinutes)
            ]);
        });

        return response()->json([
            'message' => 'Registration created successfully',
            'data' => $registration
        ]);
    }

    public function show(string $id)
    {
        $registration = Registration::with([
            'user',
            'event',
            'ticketCategory',
            'refund'
        ])->findOrFail($id);

        if (!$this->checkOwnership($registration)) {
            return response()->json([
                'message' => 'Forbidden. You do not own this registration.'
            ], 403);
        }

        return response()->json([
            'message' => 'Detail Registration',
            'data' => $registration
        ]);
    }

    public function update(Request $request, string $id)
    {
        $registration = Registration::findOrFail($id);

        if (!$this->checkOwnership($registration)) {
            return response()->json([
                'message' => 'Forbidden. You do not own this registration.'
            ], 403);
        }

        $oldStatus = $registration->status;

        $user = auth()->user();
        if ($user && $user->role !== 'admin') {
            $forbiddenFields = [
                'payment_status',
                'status',
                'registration_status',
                'paid_at',
                'payment_amount',
                'payment_gateway',
            ];

            foreach ($forbiddenFields as $field) {
                if ($request->has($field)) {
                    return response()->json([
                        'message' => "Forbidden. Participant cannot modify the '{$field}' field."
                    ], 403);
                }
            }

            $allowedFields = ['payment_method', 'payment_reference', 'payment_notes'];
            $data = $request->only($allowedFields);

            if (empty($data)) {
                return response()->json([
                    'message' => 'Forbidden. No editable fields provided or update is unauthorized.'
                ], 403);
            }
        } else {
            $data = $request->all();
        }

        // TODO / SECURITY NOTE FOR PRODUCTION RELEASE:
        // Saat mengintegrasikan Payment Gateway di fase berikutnya, perubahan status pembayaran
        // (khususnya transisi payment_status = paid) TIDAK BOLEH diizinkan berasal dari request client biasa.
        // Perubahan tersebut harus divalidasi dengan ketat agar HANYA bisa dilakukan oleh:
        // 1. Webhook callback payment gateway resmi (Midtrans/Xendit/Duitku) dengan verifikasi signature key yang valid.
        // 2. Administrator/Organizer yang terautentikasi dan memiliki hak akses (role: admin/organizer).

        // SINKRONISASI EKSPLISIT 1: Dari status lama ke kolom status baru
        if ($request->has('status')) {
            $status = $request->status;
            if ($status === 'confirmed') {
                $data['registration_status'] = 'active';
                $data['payment_status'] = 'paid';
                if (empty($registration->paid_at)) {
                    $data['paid_at'] = now();
                }
            } elseif ($status === 'cancelled') {
                $data['registration_status'] = 'cancelled';
                $data['payment_status'] = 'failed';
            } elseif ($status === 'pending') {
                $data['registration_status'] = 'active';
                if ($registration->payment_status === 'paid') {
                    $data['payment_status'] = 'pending';
                }
            }
        }

        // SINKRONISASI EKSPLISIT 2: Dari kolom status baru ke status lama
        if (isset($data['registration_status']) || isset($data['payment_status'])) {
            $regStatus = $data['registration_status'] ?? $registration->registration_status;
            $payStatus = $data['payment_status'] ?? $registration->payment_status;

            if ($regStatus === 'cancelled') {
                $data['status'] = 'cancelled';
            } elseif ($payStatus === 'paid') {
                $data['status'] = 'confirmed';
                if (empty($registration->paid_at)) {
                    $data['paid_at'] = now();
                }
            } else {
                $data['status'] = 'pending';
            }
        }

        $registration->update($data);
        $newStatus = $registration->status;

        // Release seat if registration status changed to cancelled
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled' && $registration->seat_number) {
            \App\Models\Seat::where('event_id', $registration->event_id)
                ->where('seat_number', $registration->seat_number)
                ->update(['status' => 'available']);
        }

        // Re-lock seat if registration status changes back from cancelled to pending/confirmed
        if ($newStatus !== 'cancelled' && $oldStatus === 'cancelled' && $registration->seat_number) {
            \App\Models\Seat::where('event_id', $registration->event_id)
                ->where('seat_number', $registration->seat_number)
                ->update(['status' => 'booked']);
        }

        return response()->json([
            'message' => 'Registration updated successfully',
            'data' => $registration
        ]);
    }

    public function destroy(string $id)
    {
        $registration = Registration::findOrFail($id);

        if (!$this->checkOwnership($registration)) {
            return response()->json([
                'message' => 'Forbidden. You do not own this registration.'
            ], 403);
        }

        if ($registration->seat_number) {
            \App\Models\Seat::where('event_id', $registration->event_id)
                ->where('seat_number', $registration->seat_number)
                ->update(['status' => 'available']);
        }

        $registration->delete();

        return response()->json([
            'message' => 'Registration deleted successfully'
        ]);
    }

    public function requestRefund(Request $request, Registration $registration)
    {
        if (!$this->checkOwnership($registration)) {
            return response()->json([
                'message' => 'Forbidden. You do not own this registration.'
            ], 403);
        }

        $request->validate([
            'reason' => 'required|string',
        ]);

        // Rule #1 — Payment Validation
        if ($registration->payment_status !== 'paid') {
            return response()->json([
                'message' => 'Tiket yang belum dibayar tidak dapat direfund.'
            ], 422);
        }

        // Rule #2 — Check-In Validation
        if ($registration->is_checked_in === true) {
            return response()->json([
                'message' => 'Tiket yang sudah digunakan untuk check-in tidak dapat direfund.'
            ], 422);
        }

        // Rule #3 — Registration Status Validation
        if ($registration->registration_status !== 'active') {
            return response()->json([
                'message' => 'Tiket sudah tidak aktif dan tidak dapat direfund.'
            ], 422);
        }

        // Rule #4 — Cancelled Validation
        if ($registration->status === 'cancelled') {
            return response()->json([
                'message' => 'Tiket yang telah dibatalkan tidak dapat direfund.'
            ], 422);
        }

        if ($registration->refund) {
            return response()->json([
                'message' => 'Permintaan refund untuk tiket ini sudah diajukan sebelumnya.'
            ], 422);
        }

        $refund = \App\Models\Refund::create([
            'registration_id' => $registration->id,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Refund request submitted successfully',
            'data' => $refund
        ]);
    }

    // TEMPORARY QA FEATURE
    // REMOVE AFTER REAL PAYMENT GATEWAY IMPLEMENTATION
    public function simulatePayment(Registration $registration)
    {
        if (!$this->checkOwnership($registration)) {
            return response()->json([
                'message' => 'Forbidden. You do not own this registration.'
            ], 403);
        }

        if ($registration->payment_status === 'paid') {
            return response()->json([
                'message' => 'Registration already paid.',
                'data' => $registration
            ]);
        }

        $registration->update([
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json([
            'message' => 'Payment simulated successfully.',
            'data' => $registration->fresh()
        ]);
    }

    public function getPaymentSettings()
    {
        $qrisImage = \App\Models\Setting::getValue('payment_qris_image');
        $qrisImageUrl = $qrisImage ? \Illuminate\Support\Facades\Storage::disk('public')->url($qrisImage) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'qris_image_url' => $qrisImageUrl,
                'bank_name' => \App\Models\Setting::getValue('payment_bank_name', 'Bank BCA'),
                'bank_account_number' => \App\Models\Setting::getValue('payment_bank_account_number', '126 1234 5678 9101'),
                'bank_account_name' => \App\Models\Setting::getValue('payment_bank_account_name', 'JoyVent Organizer'),
                'payment_instruction' => \App\Models\Setting::getValue('payment_instruction', 'Silakan transfer sesuai nominal yang tertera. Setelah pembayaran berhasil, unggah bukti pembayaran. Verifikasi maksimal 1x24 jam.'),
                'payment_contact' => \App\Models\Setting::getValue('payment_contact', '08123456789'),
            ]
        ]);
    }

    public function uploadPaymentProof(Registration $registration, Request $request)
    {
        if (!$this->checkOwnership($registration)) {
            return response()->json([
                'message' => 'Forbidden. You do not own this registration.'
            ], 403);
        }

        // Pengecekan status alur: Hanya status 'pending' atau 'rejected' yang diperbolehkan mengunggah bukti
        if (!in_array($registration->payment_status, ['pending', 'rejected'])) {
            return response()->json([
                'message' => 'Bukti transfer tidak dapat diunggah ulang karena status pembayaran sedang diverifikasi atau sudah lunas.'
            ], 422);
        }

        $request->validate([
            'payment_proof' => 'required|file|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $file = $request->file('payment_proof');
        $extension = $file->extension();
        $filename = 'PAY-' . $registration->id . '-' . time() . '.' . $extension;
        $fileSize = $file->getSize();

        $path = \Illuminate\Support\Facades\DB::transaction(function () use ($registration, $file, $filename, $fileSize) {
            // Pendekatan Overwrite: Hapus berkas bukti transfer lama jika ada di storage
            if ($registration->payment_proof) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($registration->payment_proof);
            }

            // Simpan file baru
            $storedPath = $file->storeAs('payment_proofs', $filename, 'public');

            // Update data registrasi, mereset seluruh jejak audit sebelumnya
            $registration->update([
                'payment_proof' => $storedPath,
                'payment_proof_uploaded_at' => now(),
                'payment_proof_size' => $fileSize,
                'payment_rejection_reason' => null,
                'payment_verified_by' => null,
                'payment_verified_at' => null,
                'payment_rejected_by' => null,
                'payment_rejected_at' => null,
                'payment_status' => 'waiting_verification',
                'status' => 'pending', // Menjaga kompatibilitas status lama
            ]);

            return $storedPath;
        });

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi dari admin.',
            'data' => [
                'registration_id' => $registration->id,
                'payment_status' => $registration->payment_status,
                'payment_proof_url' => \Illuminate\Support\Facades\Storage::disk('public')->url($path),
                'uploaded_at' => $registration->payment_proof_uploaded_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * Check if the authenticated user owns the registration, or is an admin.
     *
     * @param Registration $registration
     * @return bool
     */
    private function checkOwnership(Registration $registration): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        if ($user->role === 'admin') {
            return true;
        }
        return $registration->user_id === $user->id;
    }
}