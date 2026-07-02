<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;
    protected $event;
    protected $ticketCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create participant user
        $this->user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'role' => 'participant',
            'password' => bcrypt('password'),
        ]);

        // Create admin user for administrative update testing
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // 2. Create Event
        $this->event = Event::create([
            'name' => 'Concert JoyVent 2026',
            'category' => 'Entertainment',
            'location' => 'Jakarta',
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(6)->format('Y-m-d'),
            'start_time' => '19:00:00',
            'end_time' => '23:00:00',
            'capacity' => 500,
            'is_configured' => true,
            'has_seat_layout' => false,
        ]);

        // 3. Create Ticket Category
        $this->ticketCategory = TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'VIP Class',
            'price' => 1500000,
            'quota' => 50,
        ]);
    }

    /**
     * Test registration creation saves payment info and handles expiration configuration.
     */
    public function test_registration_creation_saves_payment_foundation_fields()
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/registrations', [
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'payment_method' => 'qris',
            'payment_gateway' => 'xendit',
            'payment_notes' => 'Testing payment creation notes'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.registration_status', 'active')
            ->assertJsonPath('data.payment_status', 'pending')
            ->assertJsonPath('data.payment_method', 'qris')
            ->assertJsonPath('data.payment_gateway', 'xendit')
            ->assertJsonPath('data.payment_notes', 'Testing payment creation notes')
            ->assertJsonPath('data.payment_amount', '1500000.00');

        $registration = Registration::first();
        $this->assertNotNull($registration->payment_expired_at);
        
        // Expiration should be roughly 60 minutes from now (based on config fallback)
        $diffInMinutes = $registration->payment_expired_at->diffInMinutes(now());
        $this->assertEqualsWithDelta(60, $diffInMinutes, 1.0);
    }

    /**
     * Test mapping old status to new fields explicitly in RegistrationController@update
     */
    public function test_patch_status_confirmed_syncs_new_payment_fields()
    {
        $registration = Registration::create([
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-123',
            'status' => 'pending',
            'registration_status' => 'active',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')->patchJson("/api/registrations/{$registration->id}", [
            'status' => 'confirmed'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.registration_status', 'active')
            ->assertJsonPath('data.payment_status', 'paid');

        $registration->refresh();
        $this->assertNotNull($registration->paid_at);
        $this->assertTrue($registration->isPaid());
        $this->assertEquals('active', $registration->registration_status);
    }

    /**
     * Test mapping old status to new fields for cancellation
     */
    public function test_patch_status_cancelled_syncs_new_fields()
    {
        $registration = Registration::create([
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-456',
            'status' => 'pending',
            'registration_status' => 'active',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')->patchJson("/api/registrations/{$registration->id}", [
            'status' => 'cancelled'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.registration_status', 'cancelled')
            ->assertJsonPath('data.payment_status', 'failed');
    }

    /**
     * Test mapping new fields to old status in RegistrationController@update
     */
    public function test_patch_new_fields_syncs_old_status()
    {
        $registration = Registration::create([
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-789',
            'status' => 'pending',
            'registration_status' => 'active',
            'payment_status' => 'pending',
        ]);

        // Client sets payment_status = paid
        $response = $this->actingAs($this->admin, 'sanctum')->patchJson("/api/registrations/{$registration->id}", [
            'payment_status' => 'paid',
            'payment_reference' => 'TX-MOCK-789'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.payment_reference', 'TX-MOCK-789');

        // Client sets registration_status = cancelled
        $response = $this->actingAs($this->admin, 'sanctum')->patchJson("/api/registrations/{$registration->id}", [
            'registration_status' => 'cancelled',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.registration_status', 'cancelled');
    }
}
