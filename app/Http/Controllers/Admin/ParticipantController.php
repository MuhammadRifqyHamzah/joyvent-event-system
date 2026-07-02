<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Event;
 
class ParticipantController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST PARTICIPANTS
    |--------------------------------------------------------------------------
    */
 
    public function index(Event $event, Request $request)
    {
        return redirect()->route('admin.events.show', ['event' => $event->id, 'tab' => 'participants']);
    }
 
    /*
    |--------------------------------------------------------------------------
    | TOGGLE MANUAL CHECK-IN
    |--------------------------------------------------------------------------
    */
 
    public function toggleCheckIn(Registration $registration)
    {
        $registration->load('refund');

        if ($registration->is_checked_in) {
            $registration->is_checked_in = 0;
            $registration->checked_in_at = null;
            $message = 'Presensi peserta ' . $registration->user->name . ' berhasil dibatalkan! ⏳';
        } else {
            // Validasi keamanan check-in manual
            if ($registration->payment_status !== 'paid') {
                return redirect()
                    ->back()
                    ->with('error', 'Check-in manual gagal: tiket belum dibayar.');
            }

            if ($registration->registration_status !== 'active') {
                return redirect()
                    ->back()
                    ->with('error', 'Check-in manual gagal: tiket sudah tidak aktif.');
            }

            if ($registration->status === 'cancelled') {
                return redirect()
                    ->back()
                    ->with('error', 'Check-in manual gagal: tiket telah dibatalkan.');
            }

            if ($registration->refund && in_array($registration->refund->status, ['pending', 'approved'])) {
                return redirect()
                    ->back()
                    ->with('error', 'Check-in manual gagal: tiket sedang atau sudah direfund.');
            }

            $registration->is_checked_in = 1;
            $registration->checked_in_at = now();
            $message = 'Check-in peserta ' . $registration->user->name . ' berhasil! 🏆';
        }

        $registration->save();

        return redirect()
            ->back()
            ->with('success', $message);
    }
 
    /*
    |--------------------------------------------------------------------------
    | RESET ALL CHECK-INS FOR EVENT
    |--------------------------------------------------------------------------
    */
    public function resetCheckIn(Event $event)
    {
        Registration::where('event_id', $event->id)->update([
            'is_checked_in' => 0,
            'checked_in_at' => null,
        ]);
 
        return redirect()
            ->back()
            ->with('success', 'Data kehadiran seluruh peserta berhasil di-reset! ↺');
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM MANUAL PAYMENT
    |--------------------------------------------------------------------------
    */
    public function confirmPayment(Registration $registration)
    {
        $registration->load(['user', 'event']);

        $event = $registration->event;
        if (!$event || $event->calculated_status !== 'upcoming') {
            return redirect()
                ->back()
                ->with('error', 'Konfirmasi pembayaran gagal: Event sudah berlangsung atau telah selesai.');
        }

        if ($registration->payment_status === 'paid') {
            return redirect()
                ->back()
                ->with('info', 'Pembayaran peserta ' . ($registration->user->name ?? 'Guest') . ' sudah dikonfirmasi sebelumnya.');
        }

        if ($registration->payment_status !== 'waiting_verification' || empty($registration->payment_proof)) {
            return redirect()
                ->back()
                ->with('error', 'Konfirmasi pembayaran gagal: Status pembayaran harus waiting_verification dan bukti pembayaran harus ada.');
        }

        $registration->update([
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_verified_by' => auth()->id(),
            'payment_verified_at' => now(),
            'payment_rejection_reason' => null, // Reset rejection reason if approved
        ]);

        // Sync notifications
        \App\Models\Notification::checkTableAndSync();

        return redirect()
            ->back()
            ->with('success', 'Pembayaran peserta ' . ($registration->user->name ?? 'Guest') . ' berhasil dikonfirmasi! 💳');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT MANUAL PAYMENT
    |--------------------------------------------------------------------------
    |
    */
    public function rejectPayment(Registration $registration, Request $request)
    {
        $registration->load(['user', 'event']);

        $event = $registration->event;
        if (!$event || $event->calculated_status !== 'upcoming') {
            return redirect()
                ->back()
                ->with('error', 'Penolakan pembayaran gagal: Event sudah berlangsung atau telah selesai.');
        }

        if ($registration->payment_status !== 'waiting_verification' || empty($registration->payment_proof)) {
            return redirect()
                ->back()
                ->with('error', 'Penolakan pembayaran gagal: Pembayaran tidak berada dalam status menunggu verifikasi atau bukti pembayaran kosong.');
        }

        $request->validate([
            'payment_rejection_reason' => 'required|string|max:255',
        ]);

        $registration->update([
            'status' => 'pending', // Kompatibilitas dengan mobile app lama
            'registration_status' => 'active',
            'payment_status' => 'rejected',
            'payment_rejection_reason' => $request->payment_rejection_reason,
            'payment_rejected_by' => auth()->id(),
            'payment_rejected_at' => now(),
        ]);

        // Sync notifications
        \App\Models\Notification::checkTableAndSync();

        return redirect()
            ->back()
            ->with('success', 'Pembayaran peserta ' . ($registration->user->name ?? 'Guest') . ' berhasil ditolak dengan alasan: ' . $request->payment_rejection_reason);
    }
}
