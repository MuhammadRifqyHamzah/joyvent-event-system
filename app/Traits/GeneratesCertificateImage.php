<?php

namespace App\Traits;

use App\Models\Certificate;
use App\Models\Registration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

trait GeneratesCertificateImage
{
    /**
     * Generate a real certificate image (PNG) based on template and write text.
     *
     * @param Certificate $certificate
     * @param Registration $registration
     * @return bool True on success, false on failure
     */
    private function generateCertificateImage(Certificate $certificate, Registration $registration)
    {
        $event = $registration->event;
        if (!$event) {
            Log::error("Certificate image generation failed: Event registration model has no associated event.", [
                'registration_id' => $registration->id,
                'certificate_id' => $certificate->id,
            ]);
            return false;
        }

        $user = $registration->user;
        $userName = $user ? $user->name : 'Participant';
        $certificateCode = $certificate->certificate_code;

        // Determine template path
        $templateName = $event->certificate_template;
        $templatePath = null;

        if (!empty($templateName)) {
            $templatePath = public_path('storage/certificates/templates/' . $templateName);
        }

        // Fallback search template_{eventId}.* if not recorded in database
        if (empty($templatePath) || !File::exists($templatePath)) {
            $templateDirectory = public_path('storage/certificates/templates');
            if (File::exists($templateDirectory)) {
                $files = File::files($templateDirectory);
                foreach ($files as $file) {
                    if (str_starts_with($file->getFilename(), 'template_' . $event->id . '.')) {
                        $templatePath = $file->getRealPath();
                        break;
                    }
                }
            }
        }

        // Jika template tidak ditemukan, proses tidak boleh crash
        if (empty($templatePath) || !File::exists($templatePath)) {
            Log::warning("Certificate image generation: Template background image not found on disk. Gracefully bypassing image rendering.", [
                'event_id' => $event->id,
                'template_name' => $templateName,
                'searched_path' => $templatePath,
            ]);
            return true;
        }

        // Load template image based on actual MIME type (supporting files with incorrect extensions)
        $imageInfo = @getimagesize($templatePath);
        if (!$imageInfo || empty($imageInfo['mime'])) {
            Log::error("Certificate image generation failed: Unable to detect template MIME type.", [
                'template_path' => $templatePath,
            ]);
            return false;
        }

        $mimeType = $imageInfo['mime'];
        switch ($mimeType) {
            case 'image/png':
                $image = @imagecreatefrompng($templatePath);
                break;
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($templatePath);
                break;
            default:
                Log::error("Certificate image generation failed: Unsupported template MIME type.", [
                    'template_path' => $templatePath,
                    'mime_type' => $mimeType,
                ]);
                return false;
        }

        if (!$image) {
            Log::error("Certificate image generation failed: GD Library failed to load image from template.", [
                'template_path' => $templatePath,
                'mime_type' => $mimeType,
            ]);
            return false;
        }

        // Determine image dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // Find available font path
        $fontPath = resource_path('fonts/Roboto-VariableFont_wdth,wght.ttf');

        // Jika font tidak tersedia di sistem, hindari crash
        if (!File::exists($fontPath)) {
            imagedestroy($image);
            Log::error("Certificate image generation failed: TrueType font file not found.", [
                'font_path' => $fontPath,
            ]);
            return false;
        }

        // Allocate premium text color (Charcoal: RGB 33, 37, 41)
        $textColor = imagecolorallocate($image, 33, 37, 41);
        if ($textColor === false) {
            $textColor = imagecolorallocate($image, 0, 0, 0);
        }

        // Calculate proportional font size depending on image dimensions
        $nameFontSize = max(18, intval($height * 0.045));
        $codeFontSize = max(10, intval($height * 0.02));

        // 1. Write Recipient Name (Horizontal Center)
        $nameBBox = imagettfbbox($nameFontSize, 0, $fontPath, $userName);
        if ($nameBBox !== false) {
            $nameWidth = abs($nameBBox[2] - $nameBBox[0]);
            $nameX = intval(($width - $nameWidth) / 2);
            $nameY = intval($height * 0.52); // ~52% height from top
            @imagettftext($image, $nameFontSize, 0, $nameX, $nameY, $textColor, $fontPath, $userName);
        } else {
            Log::warning("Certificate image generation: imagettfbbox failed to calculate bounding box for participant name.", [
                'name' => $userName,
            ]);
        }

        // 2. Write Certificate Code (Horizontal Center near bottom)
        $codeBBox = imagettfbbox($codeFontSize, 0, $fontPath, $certificateCode);
        if ($codeBBox !== false) {
            $codeWidth = abs($codeBBox[2] - $codeBBox[0]);
            $codeX = intval(($width - $codeWidth) / 2);
            $codeY = intval($height * 0.85); // ~85% height from top
            @imagettftext($image, $codeFontSize, 0, $codeX, $codeY, $textColor, $fontPath, $certificateCode);
        } else {
            Log::warning("Certificate image generation: imagettfbbox failed to calculate bounding box for certificate code.", [
                'code' => $certificateCode,
            ]);
        }

        // Create generated directory if not exists
        $generatedDirectory = public_path('storage/certificates/generated');

        if (!File::exists($generatedDirectory)) {
            try {
                if (!File::makeDirectory($generatedDirectory, 0755, true)) {
                    throw new \Exception("File::makeDirectory returned false");
                }
            } catch (\Throwable $e) {
                imagedestroy($image);
                Log::error("Certificate image generation failed: Unable to create target directory.", [
                    'directory' => $generatedDirectory,
                    'error' => $e->getMessage(),
                ]);
                return false;
            }
        }

        // Save certificate file as PNG using certificate_code
        $outputFilename = $certificateCode . '.png';
        $outputPath = $generatedDirectory . '/' . $outputFilename;

        $saved = @imagepng($image, $outputPath);
        imagedestroy($image);

        if (!$saved) {
            Log::error("Certificate image generation failed: imagepng() failed to save image file.", [
                'output_path' => $outputPath,
            ]);
            return false;
        }

        // Update certificate_file path in DB - relative path WITHOUT storage/ prefix
        $certificate->certificate_file = 'certificates/generated/' . $outputFilename;
        $certificate->save();

        return true;
    }
}
