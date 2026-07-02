<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Registration;
use App\Models\Refund;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;

class RefundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('Seeder blocked in production environment.');
            return;
        }

        // Truncate existing refunds
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Refund::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get some registrations
        $registrations = Registration::with(['event', 'ticketCategory'])->take(15)->get();

        if ($registrations->isEmpty()) {
            return;
        }

        $reasons = [
            'Bentrok jadwal kuliah / kerja mendadak',
            'Ada acara keluarga di luar kota',
            'Kondisi kesehatan tidak memungkinkan (sakit)',
            'Salah memilih kategori tiket saat check-out',
            'Rencana perjalanan dibatalkan',
            'Keperluan mendesak yang tidak bisa ditunda'
        ];

        $notes = [
            'Mohon diproses secepatnya, terima kasih.',
            'Kirim refund ke rekening BCA 123456789 a.n. Rifqy.',
            'Lampiran surat sakit menyusul jika diperlukan.',
            'Maaf atas ketidaknyamanannya.',
            'Semoga bisa bergabung di event JoyVent berikutnya.',
            null
        ];

        $statuses = ['pending', 'approved', 'rejected', 'pending', 'approved', 'rejected', 'pending', 'approved', 'rejected', 'pending'];

        // Seed 10 refunds
        $count = 0;
        foreach ($registrations as $index => $reg) {
            if ($count >= 10) break;

            $status = $statuses[$count];

            // Create Refund
            Refund::create([
                'registration_id' => $reg->id,
                'reason' => $reasons[array_rand($reasons)],
                'additional_notes' => $notes[array_rand($notes)],
                'status' => $status,
                'created_at' => now()->subDays(rand(1, 5))->subHours(rand(1, 12)),
            ]);

            // Apply side-effects for Approved refunds
            if ($status === 'approved') {
                $reg->update([
                    'status' => 'cancelled'
                ]);

                // Free seat if layout is active and seat_number exists
                if ($reg->event->has_seat_layout && $reg->seat_number) {
                    Seat::where('event_id', $reg->event_id)
                        ->where('seat_number', $reg->seat_number)
                        ->update(['status' => 'available']);
                }
            } elseif ($status === 'rejected') {
                $reg->update([
                    'status' => 'confirmed'
                ]);
            } else { // pending
                $reg->update([
                    'status' => 'confirmed' // ticket remains active while pending
                ]);
            }

            $count++;
        }
    }
}
