<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Registration;
use App\Models\Certificate;

class CertificateController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:registrations,id'
        ]);

        $registration = Registration::findOrFail(
            $request->registration_id
        );

        // Cek apakah participant hadir
        if (!$registration->is_checked_in) {

            return response()->json([
                'message' => 'Participant belum check-in'
            ], 400);
        }

        // Cek apakah certificate sudah dibuat
        $existingCertificate = Certificate::where(
            'registration_id',
            $registration->id
        )->first();

        if ($existingCertificate) {

            return response()->json([
                'message' => 'Certificate sudah dibuat',
                'data' => $existingCertificate
            ]);
        }

        // Generate certificate
        $certificate = Certificate::create([
            'registration_id' => $registration->id,

            'certificate_code' =>
                'CERT-' . strtoupper(uniqid()),

            'issued_at' => now()
        ]);

        return response()->json([
            'message' => 'Certificate berhasil dibuat',
            'data' => $certificate
        ]);
    }
 
    public function myCertificates(Request $request)
    {
        $userId = $request->user()->id;
 
        $certificates = Certificate::with(['registration.event'])
            ->whereHas('registration', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
 
        return response()->json([
            'success' => true,
            'data' => $certificates
        ]);
    }
}