<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketQuotaValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        TicketCategory::truncate();
        Event::truncate();
        User::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $this->event = Event::create([
            'name' => 'JoyVent Conference 2026',
            'category' => 'Business',
            'location' => 'JCC Jakarta',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-12',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'capacity' => 100,
            'is_configured' => true,
        ]);
    }

    public function test_create_ticket_within_capacity_limit_succeeds()
    {
        $response = $this->actingAs($this->admin)->post("/admin/events/{$this->event->id}/tickets", [
            'name' => 'VIP Class',
            'price' => 500000,
            'quota' => 50,
            'description' => 'VIP ticket benefit'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ticket_categories', [
            'event_id' => $this->event->id,
            'name' => 'VIP Class',
            'quota' => 50
        ]);
    }

    public function test_create_ticket_exceeding_capacity_limit_fails()
    {
        // First ticket quota = 60
        $this->actingAs($this->admin)->post("/admin/events/{$this->event->id}/tickets", [
            'name' => 'VIP Class',
            'price' => 500000,
            'quota' => 60,
        ]);

        // Second ticket quota = 50. Total = 110 > 100. Should fail.
        $response = $this->actingAs($this->admin)->post("/admin/events/{$this->event->id}/tickets", [
            'name' => 'Regular Class',
            'price' => 150000,
            'quota' => 50,
        ]);

        $response->assertSessionHasErrors('quota');
        $this->assertDatabaseMissing('ticket_categories', [
            'name' => 'Regular Class'
        ]);
    }

    public function test_create_ticket_via_api_exceeding_capacity_limit_fails()
    {
        // First ticket quota = 80
        $this->actingAs($this->admin)->post("/admin/events/{$this->event->id}/tickets", [
            'name' => 'VIP Class',
            'price' => 500000,
            'quota' => 80,
        ]);

        // API store request with header Accept json
        $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/ticket-categories", [
            'event_id' => $this->event->id,
            'name' => 'Regular Class',
            'price' => 150000,
            'quota' => 30, // 80 + 30 = 110 > 100
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('quota');
    }

    public function test_update_ticket_via_api_exceeding_capacity_limit_fails()
    {
        $vip = TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'VIP Class',
            'price' => 500000,
            'quota' => 50,
        ]);

        $regular = TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Regular Class',
            'price' => 150000,
            'quota' => 50,
        ]);

        // Edit VIP to quota = 70. Total = 70 + 50 = 120 > 100. Should fail.
        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/ticket-categories/{$vip->id}", [
            'quota' => 70
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('quota');
        $this->assertEquals(50, $vip->fresh()->quota);
    }

    /**
     * Test registration fails when ticket category quota is exceeded.
     */
    public function test_registration_fails_when_ticket_quota_is_exceeded()
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        $ticketCat = TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Limited Class',
            'price' => 100000,
            'quota' => 1,
        ]);

        // First user registers -> succeeds
        $response = $this->actingAs($user1, 'sanctum')->postJson('/api/registrations', [
            'event_id' => $this->event->id,
            'ticket_category_id' => $ticketCat->id,
        ]);
        $response->assertStatus(200);

        // Second user registers -> fails (quota exceeded)
        $response = $this->actingAs($user2, 'sanctum')->postJson('/api/registrations', [
            'event_id' => $this->event->id,
            'ticket_category_id' => $ticketCat->id,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('ticket_category_id');
    }

    /**
     * Test registration succeeds when cancelled registration frees quota.
     */
    public function test_registration_succeeds_when_cancelled_registration_frees_quota()
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        $ticketCat = TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Limited Class',
            'price' => 100000,
            'quota' => 1,
        ]);

        // First user registers -> succeeds
        $response = $this->actingAs($user1, 'sanctum')->postJson('/api/registrations', [
            'event_id' => $this->event->id,
            'ticket_category_id' => $ticketCat->id,
        ]);
        $response->assertStatus(200);
        $regId = $response->json('data.id');

        // Second user registers -> fails
        $response = $this->actingAs($user2, 'sanctum')->postJson('/api/registrations', [
            'event_id' => $this->event->id,
            'ticket_category_id' => $ticketCat->id,
        ]);
        $response->assertStatus(422);

        // Cancel first registration
        $registration = Registration::findOrFail($regId);
        // We use admin to update registration to cancelled
        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/registrations/{$regId}", [
            'status' => 'cancelled'
        ]);
        $response->assertStatus(200);

        // Second user registers again -> succeeds now
        $response = $this->actingAs($user2, 'sanctum')->postJson('/api/registrations', [
            'event_id' => $this->event->id,
            'ticket_category_id' => $ticketCat->id,
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test registration quota check on seated layout events.
     */
    public function test_registration_quota_seated_layout()
    {
        $user = User::create([
            'name' => 'Seated User',
            'email' => 'seated_user@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create a seated event
        $seatedEvent = Event::create([
            'name' => 'Seated Event 2026',
            'category' => 'Business',
            'location' => 'JCC Jakarta',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-12',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'capacity' => 10,
            'is_configured' => true,
            'has_seat_layout' => true,
        ]);

        $ticketCat = TicketCategory::create([
            'event_id' => $seatedEvent->id,
            'name' => 'VIP',
            'price' => 100000,
            'quota' => 1,
        ]);

        $seat = \App\Models\Seat::create([
            'event_id' => $seatedEvent->id,
            'ticket_category_id' => $ticketCat->id,
            'seat_number' => 'A1',
            'status' => 'available',
        ]);

        // Register with seat layout -> succeeds
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/registrations', [
            'event_id' => $seatedEvent->id,
            'ticket_category_id' => $ticketCat->id,
            'seat_id' => $seat->id,
            'seat_number' => 'A1',
        ]);
        $response->assertStatus(200);

        // Attempt another registration when quota is full -> fails
        $user2 = User::create([
            'name' => 'Seated User 2',
            'email' => 'seated_user2@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user2, 'sanctum')->postJson('/api/registrations', [
            'event_id' => $seatedEvent->id,
            'ticket_category_id' => $ticketCat->id,
            'seat_id' => $seat->id,
            'seat_number' => 'A1',
        ]);
        $response->assertStatus(422);
    }
}
