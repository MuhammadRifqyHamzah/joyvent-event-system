<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registration;

class RegistrationController extends Controller
{
    public function index()
    {
        $registrations = Registration::with([
            'user',
            'event',
            'ticketCategory',
            'refund'
        ])->latest()->get();

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

            $registration = Registration::create([
                'user_id' => auth()->id(),
                'event_id' => $request->event_id,
                'ticket_category_id' => $request->ticket_category_id,
                'seat_number' => $seatNumber,
                'qr_code' => 'QR-' . strtoupper(uniqid()),
                'is_checked_in' => false,
                'status' => 'pending'
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

        return response()->json([
            'message' => 'Detail Registration',
            'data' => $registration
        ]);
    }

    public function update(Request $request, string $id)
    {
        $registration = Registration::findOrFail($id);
        $oldStatus = $registration->status;

        $registration->update($request->all());
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
        $request->validate([
            'reason' => 'required|string',
        ]);

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
}