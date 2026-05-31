<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\Registration;
use App\Models\Seat;
use App\Models\Certificate;
use App\Models\LuckyDrawWinner;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Truncate existing data to prevent integrity constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LuckyDrawWinner::truncate();
        Certificate::truncate();
        Seat::truncate();
        Registration::truncate();
        TicketCategory::truncate();
        Event::truncate();
        
        // Hapus akun selain admin agar admin yang ada (default & custom) tetap aman
        User::where('role', '!=', 'admin')->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Note: Admin Seeder sekarang dipanggil secara terpisah dan di-preserve.
        // Tidak membuat ulang / menimpa akun admin di sini.

        // 2. Seed 200 Participant Users
        $participants = [];
        for ($i = 1; $i <= 200; $i++) {
            $participants[] = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'participant',
                'phone' => fake()->phoneNumber(),
                'email_verified_at' => now(),
            ]);
        }

        // 3. Detailed Event Data (10 Events)
        $eventData = [
            // A. ON-GOING (3 Events)
            [
                'name' => 'Tech Innovation Summit 2026',
                'category' => 'Business',
                'location' => 'Jakarta Convention Center (JCC)',
                'start_date' => '2026-05-28',
                'end_date' => '2026-06-03',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'status' => 'open',
                'has_certificate' => true,
                'has_lucky_draw' => true,
                'has_seat_layout' => true,
                'prize_name' => 'MacBook Pro M3',
                'prize_description' => 'Grand Prize untuk pemenang Lucky Draw utama',
                'winner_count' => 2,
                'organizer_name' => 'JoyVent Tech Division',
                'certificate_title' => 'Certificate of Tech Innovation Excellence',
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 150000, 'quota' => 100],
                    ['name' => 'VIP', 'price' => 350000, 'quota' => 50],
                    ['name' => 'VVIP', 'price' => 1000000, 'quota' => 20]
                ]
            ],
            [
                'name' => 'Digital Marketing Bootcamp',
                'category' => 'Education',
                'location' => 'WeWork Coworking Space, Kuningan',
                'start_date' => '2026-05-29',
                'end_date' => '2026-06-02',
                'start_time' => '09:00:00',
                'end_time' => '16:00:00',
                'status' => 'open',
                'has_certificate' => true,
                'has_lucky_draw' => false,
                'has_seat_layout' => false,
                'organizer_name' => 'JoyVent Marketing Hub',
                'certificate_title' => 'Digital Marketing Excellence Certification',
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 80000, 'quota' => 150]
                ]
            ],
            [
                'name' => 'AI & Machine Learning Conference',
                'category' => 'Education',
                'location' => 'Auditorium Universitas Indonesia',
                'start_date' => '2026-05-28',
                'end_date' => '2026-06-01',
                'start_time' => '08:30:00',
                'end_time' => '17:30:00',
                'status' => 'finished',
                'has_certificate' => false,
                'has_lucky_draw' => false,
                'has_seat_layout' => true,
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 120000, 'quota' => 80],
                    ['name' => 'VIP', 'price' => 250000, 'quota' => 40]
                ]
            ],
            // B. UPCOMING 31 MEI 2026 (4 Events)
            [
                'name' => 'Startup Pitch Competition',
                'category' => 'Business',
                'location' => 'Telkom Hub Auditorium',
                'start_date' => '2026-05-31',
                'end_date' => '2026-05-31',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'status' => 'open',
                'has_certificate' => false,
                'has_lucky_draw' => true,
                'has_seat_layout' => false,
                'prize_name' => 'Funding IDR 10M',
                'prize_description' => 'Seed funding untuk juara pertama kompetisi pitch',
                'winner_count' => 1,
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 100000, 'quota' => 80],
                    ['name' => 'VIP', 'price' => 300000, 'quota' => 30],
                    ['name' => 'VVIP', 'price' => 800000, 'quota' => 15]
                ]
            ],
            [
                'name' => 'UI/UX Design Workshop',
                'category' => 'Education',
                'location' => 'Binus University Kampus Anggrek',
                'start_date' => '2026-05-31',
                'end_date' => '2026-05-31',
                'start_time' => '10:00:00',
                'end_time' => '15:00:00',
                'status' => 'finished',
                'has_certificate' => false,
                'has_lucky_draw' => false,
                'has_seat_layout' => false,
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 50000, 'quota' => 60]
                ]
            ],
            [
                'name' => 'Cyber Security Seminar',
                'category' => 'Education',
                'location' => 'Hotel Mulia Senayan',
                'start_date' => '2026-05-31',
                'end_date' => '2026-05-31',
                'start_time' => '13:00:00',
                'end_time' => '16:30:00',
                'status' => 'open',
                'has_certificate' => true,
                'has_lucky_draw' => true,
                'has_seat_layout' => true,
                'prize_name' => 'Flipper Zero',
                'prize_description' => 'Geek tool gratis untuk peserta seminar terpilih',
                'winner_count' => 1,
                'organizer_name' => 'Cyber Joy Sec',
                'certificate_title' => 'Seminar on Defensive Security',
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 75000, 'quota' => 120],
                    ['name' => 'VIP', 'price' => 200000, 'quota' => 30]
                ]
            ],
            [
                'name' => 'Data Science Meetup',
                'category' => 'Community',
                'location' => 'Kopi Kalyan Space, Blok M',
                'start_date' => '2026-05-31',
                'end_date' => '2026-05-31',
                'start_time' => '18:00:00',
                'end_time' => '21:00:00',
                'status' => 'open',
                'has_certificate' => true,
                'has_lucky_draw' => false,
                'has_seat_layout' => false,
                'organizer_name' => 'Indo Data Community',
                'certificate_title' => 'Data Meetup Attendance Certificate',
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 60000, 'quota' => 50],
                    ['name' => 'VIP', 'price' => 150000, 'quota' => 20],
                    ['name' => 'VVIP', 'price' => 500000, 'quota' => 10]
                ]
            ],
            // C. UPCOMING 1 JUNI 2026 (3 Events)
            [
                'name' => 'Mobile Development Conference',
                'category' => 'Entertainment',
                'location' => 'Ice BSD Hall 5',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-01',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'status' => 'open',
                'has_certificate' => false,
                'has_lucky_draw' => false,
                'has_seat_layout' => true,
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 90000, 'quota' => 100]
                ]
            ],
            [
                'name' => 'Creative Business Forum',
                'category' => 'Business',
                'location' => 'Grand Hyatt Ballroom',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-01',
                'start_time' => '10:00:00',
                'end_time' => '16:00:00',
                'status' => 'open',
                'has_certificate' => false,
                'has_lucky_draw' => true,
                'has_seat_layout' => false,
                'prize_name' => 'Sony WH-1000XM5',
                'prize_description' => 'Noise Cancelling Headphones',
                'winner_count' => 1,
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 110000, 'quota' => 90],
                    ['name' => 'VIP', 'price' => 280000, 'quota' => 40]
                ]
            ],
            [
                'name' => 'National Education Expo',
                'category' => 'Education',
                'location' => 'Balai Kartini',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-01',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'status' => 'open',
                'has_certificate' => false,
                'has_lucky_draw' => false,
                'has_seat_layout' => false,
                'is_configured' => true,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 60000, 'quota' => 150],
                    ['name' => 'VIP', 'price' => 220000, 'quota' => 50],
                    ['name' => 'VVIP', 'price' => 500000, 'quota' => 20]
                ]
            ]
        ];

        foreach ($eventData as $eventInfo) {
            // Calculate capacity dynamically
            $capacity = collect($eventInfo['tickets'])->sum('quota');

            // Create Event
            $event = Event::create([
                'name' => $eventInfo['name'],
                'category' => $eventInfo['category'],
                'location' => $eventInfo['location'],
                'start_date' => $eventInfo['start_date'],
                'end_date' => $eventInfo['end_date'],
                'start_time' => $eventInfo['start_time'],
                'end_time' => $eventInfo['end_time'],
                'status' => $eventInfo['status'],
                'capacity' => $capacity,
                'has_certificate' => $eventInfo['has_certificate'],
                'has_lucky_draw' => $eventInfo['has_lucky_draw'],
                'has_seat_layout' => $eventInfo['has_seat_layout'],
                'prize_name' => $eventInfo['prize_name'] ?? null,
                'prize_description' => $eventInfo['prize_description'] ?? null,
                'winner_count' => $eventInfo['winner_count'] ?? null,
                'organizer_name' => $eventInfo['organizer_name'] ?? null,
                'certificate_title' => $eventInfo['certificate_title'] ?? null,
                'is_configured' => $eventInfo['is_configured'],
            ]);

            // Create Ticket Categories
            $ticketCategories = [];
            $seatLayoutConfig = [];
            foreach ($eventInfo['tickets'] as $ticket) {
                $category = TicketCategory::create([
                    'event_id' => $event->id,
                    'name' => $ticket['name'],
                    'price' => $ticket['price'],
                    'quota' => $ticket['quota'],
                    'description' => $ticket['name'] . ' ticket category for ' . $event->name,
                    'is_active' => true,
                ]);
                $ticketCategories[] = $category;

                // Build layout pattern e.g., "A1-A10, B1-B10, C1-C10"
                if ($eventInfo['has_seat_layout']) {
                    $seatLayoutConfig[$category->id] = 'A1-A10, B1-B10, C1-C10';
                }
            }

            // If has seat layout, update event with the configuration
            if ($eventInfo['has_seat_layout']) {
                $event->update([
                    'seat_layout' => json_encode($seatLayoutConfig)
                ]);
            }

            // Create Seats in DB if has_seat_layout is enabled
            $seats = [];
            if ($event->has_seat_layout) {
                $rows = ['A' => 1, 'B' => 2, 'C' => 3];
                foreach ($rows as $rowLetter => $rowVal) {
                    for ($col = 1; $col <= 10; $col++) {
                        $seats[] = Seat::create([
                            'event_id' => $event->id,
                            'seat_number' => $rowLetter . $col,
                            'row' => $rowVal,
                            'column' => $col,
                            'status' => 'available',
                        ]);
                    }
                }
            }

            // Generate Registrations for this event
            // Randomly select 15-25 unique users from our 200 participants
            $numRegistrations = rand(15, 25);
            $eventParticipants = collect($participants)->random($numRegistrations);

            // Determine if the event is currently active/ongoing (today is 2026-05-30)
            $isOngoing = ($event->start_date <= '2026-05-30' && $event->end_date >= '2026-05-30');
            $checkInRatio = $isOngoing ? (rand(60, 90) / 100) : 0;

            foreach ($eventParticipants->values() as $index => $participant) {
                // Randomly assign a ticket category
                $ticketCategory = $ticketCategories[array_rand($ticketCategories)];

                // Set status: 80% confirmed, 15% pending, 5% cancelled
                $statusRand = rand(1, 100);
                if ($statusRand <= 80) {
                    $status = 'confirmed';
                } elseif ($statusRand <= 95) {
                    $status = 'pending';
                } else {
                    $status = 'cancelled';
                }

                // Check-in status
                $isCheckedIn = false;
                $checkedInAt = null;
                if ($status === 'confirmed') {
                    if ($index / $numRegistrations < $checkInRatio) {
                        $isCheckedIn = true;
                        // Set check-in time randomly during the first hours of the event
                        $checkedInAt = Carbon::parse($event->start_date . ' ' . $event->start_time)->addMinutes(rand(10, 180));
                    }
                }

                // Seat assignment if layout is enabled and status is confirmed
                $assignedSeatNumber = null;
                if ($event->has_seat_layout && $status === 'confirmed') {
                    // Pick the next available seat
                    $availableSeat = collect($seats)->first(function ($s) {
                        return $s->status === 'available';
                    });
                    if ($availableSeat) {
                        $assignedSeatNumber = $availableSeat->seat_number;
                        $availableSeat->status = 'booked';
                        $availableSeat->save();
                    }
                }

                // Create Registration record
                $registration = Registration::create([
                    'user_id' => $participant->id,
                    'event_id' => $event->id,
                    'ticket_category_id' => $ticketCategory->id,
                    'seat_number' => $assignedSeatNumber,
                    'qr_code' => 'QR-' . strtoupper(Str::random(10)),
                    'is_checked_in' => $isCheckedIn,
                    'checked_in_at' => $checkedInAt,
                    'status' => $status,
                ]);

                // Generate Certificate if checked in and certificate is enabled
                if ($event->has_certificate && $isCheckedIn) {
                    // Seed certificates for ~80% of checked-in participants, leaving 20% "pending"
                    if (rand(1, 10) <= 8) {
                        Certificate::create([
                            'registration_id' => $registration->id,
                            'certificate_code' => 'CERT-' . strtoupper(Str::random(8)),
                            'certificate_file' => 'certificate_' . $registration->id . '.pdf',
                            'is_valid' => true,
                        ]);
                    }
                }
            }

            // Generate Lucky Draw Winners if lucky draw is enabled
            if ($event->has_lucky_draw) {
                // Get checked-in registrations
                $checkedInRegistrations = Registration::where('event_id', $event->id)
                    ->where('is_checked_in', true)
                    ->get();

                if ($checkedInRegistrations->isNotEmpty()) {
                    $maxWinners = $event->winner_count ?? 1;
                    $winnerCount = min($maxWinners, $checkedInRegistrations->count());
                    $winners = $checkedInRegistrations->random($winnerCount);

                    foreach ($winners as $w) {
                        LuckyDrawWinner::create([
                            'event_id' => $event->id,
                            'registration_id' => $w->id,
                            'prize_name' => $event->prize_name ?? 'Mystery Gift',
                            'won_at' => Carbon::parse($event->start_date . ' ' . $event->start_time)->addHours(2),
                        ]);
                    }
                }
            }
        }
    }
}
