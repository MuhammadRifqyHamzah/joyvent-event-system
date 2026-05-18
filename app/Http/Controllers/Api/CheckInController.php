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
        $request->validate([
            'qr_code' => 'required'
        ]);

        $registration = Registration::where(
            'qr_code',
            $request->qr_code
        )->first();

        if (!$registration) {

            return response()->json([
                'message' => 'QR Code tidak valid'
            ], 404);
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