<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Registration;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;

class ExpirePendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire pending payments and release seats automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredRegistrations = Registration::where('payment_status', 'pending')
            ->whereNotNull('payment_expired_at')
            ->where('payment_expired_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredRegistrations as $registration) {

            DB::transaction(function () use ($registration, &$count) {

                $registration->update([
                    'status' => 'cancelled',
                    'registration_status' => 'cancelled',
                    'payment_status' => 'expired',
                ]);

                if ($registration->seat_number) {
                    Seat::where('event_id', $registration->event_id)
                        ->where('seat_number', $registration->seat_number)
                        ->update([
                            'status' => 'available'
                        ]);
                }

                $count++;
            });
        }

        $this->info("Expired {$count} pending registration(s).");

        return Command::SUCCESS;
    }
}