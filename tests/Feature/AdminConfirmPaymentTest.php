<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConfirmPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $participant;
    protected $event;
    protected $ticketCategory;
    protected $registration;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::create([
            'name' => 'Admin JoyVent',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Create Participant User
        $this->participant = User::create([
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'role' => 'participant',
            'password' => bcrypt('password'),
        ]);

        // Create Event
        $this->event = Event::create([
            'name' => 'JoyVent Tech Summit 2026',
            'category' => 'Education',
            'location' => 'Bandung',
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(11)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        // Create Ticket Category
        $this->ticketCategory = TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Gold Ticket',
            'price' => 500000,
            'quota' => 20,
        ]);

        // Create a Pending Registration
        $this->registration = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-TEST-123456',
            'status' => 'pending',
            'registration_status' => 'active', // Mobile booking creates it active, but payment status is pending
            'payment_status' => 'pending',
            'payment_amount' => 500000,
            'payment_method' => 'transfer',
            'payment_gateway' => 'manual',
        ]);
    }

    /**
     * Test admin cannot confirm a pending payment successfully without proof.
     */
    public function test_admin_cannot_confirm_pending_payment()
    {
        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/confirm-payment");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->registration->refresh();

        $this->assertEquals('pending', $this->registration->status);
        $this->assertEquals('pending', $this->registration->payment_status);
        $this->assertNull($this->registration->paid_at);
    }

    /**
     * Test admin can confirm a waiting_verification payment with proof successfully.
     */
    public function test_admin_can_confirm_waiting_verification_payment_with_proof()
    {
        $this->registration->update([
            'payment_status' => 'waiting_verification',
            'payment_proof' => 'proofs/test_proof.jpg',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/confirm-payment");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->registration->refresh();

        $this->assertEquals('confirmed', $this->registration->status);
        $this->assertEquals('active', $this->registration->registration_status);
        $this->assertEquals('paid', $this->registration->payment_status);
        $this->assertNotNull($this->registration->paid_at);
    }

    /**
     * Test admin confirming an already paid registration does not update but gives info.
     */
    public function test_admin_confirming_already_paid_payment_redirects_with_info()
    {
        // Set to paid first
        $this->registration->update([
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
            'paid_at' => now()->subHour(),
        ]);

        $originalPaidAt = $this->registration->paid_at;

        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/confirm-payment");

        $response->assertRedirect();
        $response->assertSessionHas('info');

        $this->registration->refresh();
        $this->assertEquals($originalPaidAt->toDateTimeString(), $this->registration->paid_at->toDateTimeString());
    }

    /**
     * Test non-admin cannot confirm payment.
     */
    public function test_non_admin_cannot_confirm_payment()
    {
        $response = $this->actingAs($this->participant)->postJson("/admin/participants/{$this->registration->id}/confirm-payment");

        // Assuming admin.role middleware throws 403 for JSON requests
        $response->assertStatus(403);

        $this->registration->refresh();
        $this->assertEquals('pending', $this->registration->payment_status);
    }

    /**
     * Test admin cannot confirm payment for an event that is ongoing or finished.
     */
    public function test_admin_cannot_confirm_payment_for_ongoing_or_finished_event()
    {
        // Change event to ongoing (starts 5 days ago, ends 5 days from now)
        $this->event->update([
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $this->assertEquals('ongoing', $this->event->fresh()->calculated_status);

        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/confirm-payment");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->registration->refresh();
        $this->assertEquals('pending', $this->registration->payment_status);
    }
}
