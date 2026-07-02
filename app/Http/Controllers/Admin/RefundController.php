<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Seat;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    /**
     * Approve the refund request.
     */
    public function approve(Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Refund ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Update Refund status
            $refund->update([
                'status' => 'approved'
            ]);

            // Update Registration status to 'cancelled'
            $registration = $refund->registration;
            $registration->update([
                'status' => 'cancelled',
                'registration_status' => 'cancelled',
                'payment_status' => 'failed',
            ]);

            // Release Seat if event uses seats and seat_number exists
            if ($registration->event->has_seat_layout && $registration->seat_number) {
                $seat = Seat::where('event_id', $registration->event_id)
                    ->where('seat_number', $registration->seat_number)
                    ->first();
                if ($seat) {
                    $seat->update([
                        'status' => 'available'
                    ]);
                }
            }

            // Sync Notification table (will auto-create approved notification)
            Notification::checkTableAndSync();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Refund berhasil disetujui dan tiket dibatalkan! 🟢');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }

    /**
     * Reject the refund request.
     */
    public function reject(Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Refund ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Update Refund status
            $refund->update([
                'status' => 'rejected'
            ]);

            // Sync Notification table (will auto-create rejected notification)
            Notification::checkTableAndSync();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Refund ditolak dan tiket tetap aktif! 🔴');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }
}
