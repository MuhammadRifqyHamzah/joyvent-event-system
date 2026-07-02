<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RegenerateBrokenCertificates extends Command
{
    use \App\Traits\GeneratesCertificateImage;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:regenerate-broken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate visual PNG image files and repair certificate_file columns for broken/NULL records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Scanning database for broken or ungenerated certificates...");

        // 1. Get certificates with NULL paths or invalid extensions (e.g. seeded .pdf files)
        $brokenDbCertificates = Certificate::whereNull('certificate_file')
            ->orWhere('certificate_file', 'not like', '%.png')
            ->get();

        // 2. Get certificates with valid PNG paths in DB but missing physical files on disk
        $missingPhysicalCertificates = Certificate::whereNotNull('certificate_file')
            ->where('certificate_file', 'like', '%.png')
            ->get()
            ->filter(function ($cert) {
                return !File::exists(public_path('storage/' . $cert->certificate_file));
            });

        // Combine into a single collection (removing duplicates by key)
        $toRepair = $brokenDbCertificates->concat($missingPhysicalCertificates)->unique('id');

        $totalCount = $toRepair->count();
        if ($totalCount === 0) {
            $this->info("💡 All certificates are in a valid state. No repair needed!");
            return Command::SUCCESS;
        }

        $this->info("Found {$totalCount} broken certificate(s) that need regeneration.");
        $repairedCount = 0;
        $failedCount = 0;

        foreach ($toRepair as $certificate) {
            $registration = $certificate->registration;
            if (!$registration) {
                $this->error("❌ Skipping Certificate ID {$certificate->id} (Code: {$certificate->certificate_code}): No associated registration found.");
                Log::error("Artisan certificates:regenerate-broken skipped certificate ID {$certificate->id}: Associated registration not found.");
                $failedCount++;
                continue;
            }

            try {
                DB::transaction(function () use ($certificate, $registration, &$repairedCount) {
                    $originalFile = $certificate->certificate_file;
                    
                    // Call trait method to render PNG and update DB
                    $success = $this->generateCertificateImage($certificate, $registration);

                    if (!$success) {
                        throw new \Exception("GD image rendering engine failed.");
                    }

                    $repairedCount++;
                    $this->info("✔ Repaired Certificate ID {$certificate->id} (Code: {$certificate->certificate_code}): DB updated from '{$originalFile}' to '{$certificate->certificate_file}'");
                });
            } catch (\Throwable $e) {
                $failedCount++;
                $this->error("❌ Failed to repair Certificate ID {$certificate->id} (Code: {$certificate->certificate_code}): " . $e->getMessage());
                Log::error("Artisan certificates:regenerate-broken failed for certificate ID {$certificate->id}: " . $e->getMessage());
            }
        }

        $this->info("--- Execution Summary ---");
        $this->info("Successfully repaired: {$repairedCount}");
        if ($failedCount > 0) {
            $this->warn("Failed repairs: {$failedCount}");
        }

        return Command::SUCCESS;
    }
}
