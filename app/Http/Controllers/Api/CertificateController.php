<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Registration;
use App\Models\Certificate;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    use \App\Traits\GeneratesCertificateImage;

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
        $certificate = null;
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($registration, &$certificate) {
                $certificate = Certificate::create([
                    'registration_id' => $registration->id,
                    'certificate_code' => 'CERT-' . strtoupper(uniqid()),
                    'issued_at' => now()
                ]);

                // Generate real certificate PNG image using Trait
                $success = $this->generateCertificateImage($certificate, $registration);

                if (!$success) {
                    throw new \Exception("Gagal menghasilkan berkas gambar sertifikat.");
                }
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("API certificate generation failed for registration ID {$registration->id}: " . $e->getMessage());
            return response()->json([
                'message' => 'Gagal membuat sertifikat: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Certificate berhasil dibuat',
            'data' => $certificate->fresh()
        ]);
    }
 
    public function myCertificates(Request $request)
    {
        $userId = $request->user()->id;
 
        $certificates = Certificate::with(['registration.event', 'registration.user'])
            ->whereHas('registration', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
 
        return response()->json([
            'success' => true,
            'data' => $certificates
        ]);
    }

    public function download(Certificate $certificate)
    {
        $userId = auth()->id();
        \Illuminate\Support\Facades\Log::info('Download Certificate - Step 1: Start download request', [
            'auth_user_id' => $userId,
            'certificate_id' => $certificate->id,
            'certificate_code' => $certificate->certificate_code,
        ]);

        // 1. Authorization: Only the owner of the certificate can download
        if ($userId !== $certificate->registration->user_id) {
            \Illuminate\Support\Facades\Log::warning('Download Certificate - Step 1a: Authorization failed', [
                'auth_user_id' => $userId,
                'owner_user_id' => $certificate->registration->user_id,
            ]);
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke sertifikat ini.'
            ], 403);
        }
        \Illuminate\Support\Facades\Log::info('Download Certificate - Step 2: Authorization check passed');

        // 2. Check if file path exists in database and on physical storage
        $relativePath = $certificate->certificate_file;
        if (empty($relativePath)) {
            \Illuminate\Support\Facades\Log::warning('Download Certificate - Step 2a: DB certificate_file path is empty');
            return response()->json([
                'message' => 'Certificate has not been generated yet.'
            ], 409);
        }

        $physicalPath = public_path('storage/' . $relativePath);
        \Illuminate\Support\Facades\Log::info('Download Certificate - Step 3: Checking physical file path', [
            'relative_path' => $relativePath,
            'physical_path' => $physicalPath,
            'exists' => File::exists($physicalPath),
        ]);

        if (!File::exists($physicalPath)) {
            \Illuminate\Support\Facades\Log::warning('Download Certificate - Step 3a: Physical certificate file does not exist on disk', [
                'physical_path' => $physicalPath,
            ]);
            return response()->json([
                'message' => 'Certificate has not been generated yet.'
            ], 409);
        }
        \Illuminate\Support\Facades\Log::info('Download Certificate - Step 4: Physical file verification passed');

        // 3. Fallback DomPDF logic
        // Try absolute local path first. If it fails, fallback to Base64 data URI transparently.
        $imageSrc = $physicalPath;
        try {
            // Test if we can read the file
            if (!is_readable($physicalPath)) {
                throw new \Exception("File is not readable");
            }
            \Illuminate\Support\Facades\Log::info('Download Certificate - Step 5: Physical file is readable');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Download Certificate - Step 5a: Physical file not readable, falling back to base64', [
                'error' => $e->getMessage(),
            ]);
            $imageData = base64_encode(file_get_contents($physicalPath));
            $imageSrc = 'data:image/png;base64,' . $imageData;
        }

        \Illuminate\Support\Facades\Log::info('Download Certificate - Step 6: Rendering PDF via DomPDF');
        try {
            // Try rendering using the absolute local file path
            $pdf = Pdf::loadView('api.certificates.pdf', ['imageSrc' => $imageSrc]);
            // Call output() to trigger actual rendering so we can catch chroot/file resolution errors
            $pdf->output();
            \Illuminate\Support\Facades\Log::info('Download Certificate - Step 7: PDF output generation succeeded (Standard path)');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Download Certificate - Step 7a: PDF generation failed using standard path. Trying Base64 rendering fallback.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Fallback to Base64
            try {
                $imageData = base64_encode(file_get_contents($physicalPath));
                $base64Src = 'data:image/png;base64,' . $imageData;
                $pdf = Pdf::loadView('api.certificates.pdf', ['imageSrc' => $base64Src]);
                $pdf->output();
                \Illuminate\Support\Facades\Log::info('Download Certificate - Step 8: PDF output generation succeeded (Base64 fallback path)');
            } catch (\Throwable $ex) {
                \Illuminate\Support\Facades\Log::error('Download Certificate - Step 8a: PDF generation failed completely', [
                    'error' => $ex->getMessage(),
                    'trace' => $ex->getTraceAsString(),
                ]);
                return response()->json([
                    'message' => 'Gagal merender file sertifikat.',
                    'error' => $ex->getMessage()
                ], 500);
            }
        }

        // 4. Return PDF download response with deskriptif filename: JoyVent_Certificate_{certificate_code}.pdf
        $filename = 'JoyVent_Certificate_' . $certificate->certificate_code . '.pdf';
        \Illuminate\Support\Facades\Log::info('Download Certificate - Step 9: Returning PDF file download response', [
            'filename' => $filename,
        ]);
        
        return $pdf->stream($filename);
    }
}