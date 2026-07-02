<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Refund;
use App\Models\Seat;
use App\Models\Notification;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $event;
    protected $registration;
    protected $seat;

    protected function setUp(): void
    {
        parent::setUp();

        // 0. Truncate tables for a clean slate
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Refund::truncate();
        Registration::truncate();
        Seat::truncate();
        TicketCategory::truncate();
        Event::truncate();
        User::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 1. Create admin user
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // 2. Create normal user (participant)
        $participant = User::create([
            'name' => 'Participant Test',
            'email' => 'participant@test.com',
            'role' => 'participant',
            'password' => bcrypt('password'),
        ]);

        // 3. Create Event
        $this->event = Event::create([
            'name' => 'Test Summit 2026',
            'category' => 'Business',
            'location' => 'JCC',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'capacity' => 100,
            'is_configured' => true,
            'has_seat_layout' => true,
        ]);

        // 4. Create Ticket Category
        $ticketCategory = TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'VIP',
            'price' => 500000,
            'quota' => 10,
        ]);

        // 5. Create Seat
        $this->seat = Seat::create([
            'event_id' => $this->event->id,
            'seat_number' => 'A1',
            'row' => 1,
            'column' => 1,
            'status' => 'booked',
        ]);

        // 6. Create Registration
        $this->registration = Registration::create([
            'user_id' => $participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $ticketCategory->id,
            'seat_number' => 'A1',
            'qr_code' => 'QR-TEST',
            'status' => 'confirmed',
        ]);
    }

    public function test_admin_can_access_ongoing_event_tab_refunds()
    {
        // Change event dates so it is ongoing (relative to current time)
        $this->event->update([
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d')
        ]);

        // Create a refund
        $refund = Refund::create([
            'registration_id' => $this->registration->id,
            'reason' => 'Schedule conflict',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/events/{$this->event->id}/ongoing?tab=refunds");

        $response->assertStatus(200);
        $response->assertSee('Schedule conflict');
        $response->assertSee('Pending Refunds');
    }

    public function test_admin_can_approve_refund()
    {
        // Set up the registration to be active and paid before approval
        $this->registration->update([
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        $refund = Refund::create([
            'registration_id' => $this->registration->id,
            'reason' => 'Conflict',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/refunds/{$refund->id}/approve");

        $response->assertRedirect();
        
        // Assert refund is approved
        $this->assertEquals('approved', $refund->fresh()->status);

        // Assert registration status fields are synchronized and cancelled
        $freshRegistration = $this->registration->fresh();
        $this->assertEquals('cancelled', $freshRegistration->status);
        $this->assertEquals('cancelled', $freshRegistration->registration_status);
        $this->assertEquals('failed', $freshRegistration->payment_status);

        // Assert seat is available
        $this->assertEquals('available', $this->seat->fresh()->status);

        // Assert notification sync works
        $notif = Notification::where('source_key', 'refund_approved_' . $refund->id)->first();
        $this->assertNotNull($notif);
        $this->assertEquals('refunds', $notif->type);
    }

    public function test_admin_can_reject_refund()
    {
        $refund = Refund::create([
            'registration_id' => $this->registration->id,
            'reason' => 'Conflict',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/refunds/{$refund->id}/reject");

        $response->assertRedirect();
        
        // Assert refund is rejected
        $this->assertEquals('rejected', $refund->fresh()->status);

        // Assert registration is still confirmed
        $this->assertEquals('confirmed', $this->registration->fresh()->status);

        // Assert seat remains booked
        $this->assertEquals('booked', $this->seat->fresh()->status);

        // Assert notification sync works
        $notif = Notification::where('source_key', 'refund_rejected_' . $refund->id)->first();
        $this->assertNotNull($notif);
    }
}
