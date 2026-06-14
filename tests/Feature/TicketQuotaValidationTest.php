<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\TicketCategory;
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
}
