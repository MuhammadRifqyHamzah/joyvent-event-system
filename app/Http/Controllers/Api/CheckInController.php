<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registration;
use Carbon\Carbon;

class CheckInController extends Controller
{
    public function checkIn(Request $request)
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

        $request->validate([
            'qr_code' => 'required'
        ]);

        $registration = Registration::with('refund')
            ->where('qr_code', $request->qr_code)
            ->first();

        if (!$registration) {

            return response()->json([
                'message' => 'QR Code tidak valid'
            ], 404);
        }

        // Production Hardening Validation
        if ($registration->payment_status !== 'paid') {
            return response()->json([
                'message' => 'Check-in gagal: tiket belum dibayar.'
            ], 400);
        }

        if ($registration->registration_status !== 'active') {
            return response()->json([
                'message' => 'Check-in gagal: tiket sudah tidak aktif.'
            ], 400);
        }

        if ($registration->status === 'cancelled') {
            return response()->json([
                'message' => 'Check-in gagal: tiket telah dibatalkan.'
            ], 400);
        }

        if (
            $registration->refund &&
            in_array(
                $registration->refund->status,
                ['pending', 'approved']
            )
        ) {
            return response()->json([
                'message' => 'Check-in gagal: tiket sedang atau sudah direfund.'
            ], 400);
        }

        if ($registration->is_checked_in) {

            return response()->json([
                'message' => 'Peserta sudah check-in sebelumnya'
            ], 400);
        }

        $registration->update([
            'is_checked_in' => true,
            'checked_in_at' => Carbon::now()
        ]);

        return response()->json([
            'message' => 'Check-in berhasil',
            'data' => $registration
        ]);
    }
}