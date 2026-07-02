<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Refund;
use App\Models\TicketCategory;
use App\Models\LuckyDrawWinner;
use App\Models\EventPrize;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LuckyDrawTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Truncate tables for a clean slate
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        LuckyDrawWinner::truncate();
        Refund::truncate();
        Registration::truncate();
        TicketCategory::truncate();
        EventPrize::truncate();
        Event::truncate();
        User::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }

    public function test_drawing_requires_authenticated_admin()
    {
        $event = Event::create([
            'name' => 'Joy draw event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'iPad Pro',
            'winner_count' => 2,
            'status' => 'waiting',
        ]);

        // 1. Unauthenticated request
        $response = $this->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
        ]);
        $response->assertStatus(401);

        // 2. Authenticated but participant role
        $participant = User::create([
            'name' => 'Participant Test',
            'email' => 'participant@test.com',
            'role' => 'participant',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($participant, 'sanctum')->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
        ]);
        $response->assertStatus(403);

        // 3. Authenticated Admin role
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 500000,
            'quota' => 10,
        ]);

        $candidateUser = User::create([
            'name' => 'Candidate User',
            'email' => 'candidate@test.com',
            'role' => 'participant',
            'password' => bcrypt('password'),
        ]);

        Registration::create([
            'user_id' => $candidateUser->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-TEST',
            'is_checked_in' => true,
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }

    public function test_only_valid_checked_in_active_paid_candidates_can_win()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Joy draw event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 500000,
            'quota' => 10,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'TV',
            'winner_count' => 1,
            'status' => 'waiting',
        ]);

        // Candidate 1: Not checked-in
        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@test.com',
            'password' => bcrypt('password'),
        ]);
        $reg1 = Registration::create([
            'user_id' => $user1->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-1',
            'is_checked_in' => false,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        // Candidate 2: Checked-in, but unpaid
        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@test.com',
            'password' => bcrypt('password'),
        ]);
        $reg2 = Registration::create([
            'user_id' => $user2->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-2',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'pending',
        ]);

        // Candidate 3: Checked-in, paid, but cancelled
        $user3 = User::create([
            'name' => 'User 3',
            'email' => 'user3@test.com',
            'password' => bcrypt('password'),
        ]);
        $reg3 = Registration::create([
            'user_id' => $user3->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-3',
            'is_checked_in' => true,
            'registration_status' => 'cancelled',
            'payment_status' => 'paid',
        ]);

        // Candidate 4: Valid (checked-in, active, paid)
        $user4 = User::create([
            'name' => 'User 4',
            'email' => 'user4@test.com',
            'password' => bcrypt('password'),
        ]);
        $reg4 = Registration::create([
            'user_id' => $user4->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-4',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        // Draw
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.registration_id', $reg4->id);
    }

    public function test_excludes_refund_pending_and_approved_tickets()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Joy draw event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 500000,
            'quota' => 10,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'TV',
            'winner_count' => 1,
            'status' => 'waiting',
        ]);

        // Candidate 1: Valid but refund is pending
        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@test.com',
            'password' => bcrypt('password'),
        ]);
        $reg1 = Registration::create([
            'user_id' => $user1->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-1',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);
        Refund::create([
            'registration_id' => $reg1->id,
            'amount' => 500000,
            'status' => 'pending',
            'reason' => 'test'
        ]);

        // Candidate 2: Valid but refund is approved
        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@test.com',
            'password' => bcrypt('password'),
        ]);
        $reg2 = Registration::create([
            'user_id' => $user2->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-2',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);
        Refund::create([
            'registration_id' => $reg2->id,
            'amount' => 500000,
            'status' => 'approved',
            'reason' => 'test'
        ]);

        // Candidate 3: Valid and refund is rejected (should be eligible)
        $user3 = User::create([
            'name' => 'User 3',
            'email' => 'user3@test.com',
            'password' => bcrypt('password'),
        ]);
        $reg3 = Registration::create([
            'user_id' => $user3->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-3',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);
        Refund::create([
            'registration_id' => $reg3->id,
            'amount' => 500000,
            'status' => 'rejected',
            'reason' => 'test'
        ]);

        // Draw
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.registration_id', $reg3->id);
    }

    public function test_excludes_users_that_already_won()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Joy draw event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 500000,
            'quota' => 10,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'Bike',
            'winner_count' => 2,
            'status' => 'waiting',
        ]);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        // Ticket 1: Already Won
        $reg1 = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-1',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);
        LuckyDrawWinner::create([
            'event_id' => $event->id,
            'registration_id' => $reg1->id,
            'event_prize_id' => $prize->id,
            'prize_name' => 'Car',
            'won_at' => now(),
        ]);

        // Ticket 2: Owned by same User (Should be excluded)
        $reg2 = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-2',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        // Draw
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
        ]);

        $response->assertStatus(404); // No eligible candidates found
    }

    public function test_hybrid_mode_draws_all_participants()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Joy draw event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 500000,
            'quota' => 10,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'TV',
            'winner_count' => 2,
            'status' => 'waiting',
        ]);

        // User not checked in, but paid and active
        $user = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);
        $reg = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-1',
            'is_checked_in' => false,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        // Draw in default mode (checked_in_only) -> should fail (404)
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
            'lucky_draw_mode' => 'checked_in_only'
        ]);
        $response->assertStatus(404);

        // Draw in all_participants mode -> should succeed (200)
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
            'lucky_draw_mode' => 'all_participants'
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.registration_id', $reg->id);
    }

    public function test_my_wins_only_returns_logged_in_user_wins()
    {
        $event = Event::create([
            'name' => 'Joy Event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 500000,
            'quota' => 10,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'Bike',
            'winner_count' => 5,
            'status' => 'waiting',
        ]);

        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@test.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@test.com',
            'password' => bcrypt('password'),
        ]);

        $reg1 = Registration::create([
            'user_id' => $user1->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-1',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        $reg2 = Registration::create([
            'user_id' => $user2->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-2',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        $win1 = LuckyDrawWinner::create([
            'event_id' => $event->id,
            'registration_id' => $reg1->id,
            'event_prize_id' => $prize->id,
            'prize_name' => 'Car',
            'won_at' => now(),
        ]);

        $win2 = LuckyDrawWinner::create([
            'event_id' => $event->id,
            'registration_id' => $reg2->id,
            'event_prize_id' => $prize->id,
            'prize_name' => 'Bike',
            'won_at' => now(),
        ]);

        // Request wins as User 1
        $response = $this->actingAs($user1, 'sanctum')->getJson("/api/lucky-draw/my-wins");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $win1->id);
        $response->assertJsonPath('data.0.prize_name', 'Car');
        $response->assertJsonPath('data.0.event_name', 'Joy Event');
    }

    public function test_drawing_lock_mechanism_prevents_double_draw()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Joy draw event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'MacBook Pro',
            'winner_count' => 2,
            'status' => 'drawing', // Set to drawing, mimicking active locking
        ]);

        // Trying to draw a locked prize should fail with 409
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/lucky-draw", [
            'event_prize_id' => $prize->id,
        ]);

        $response->assertStatus(409);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Proses undian untuk hadiah ini sedang berlangsung.');
    }

    public function test_draw_response_contains_lucky_draw_winner_id_not_registration_id()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-draw@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Draw Audit Event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'iPad Air',
            'winner_count' => 1,
            'status' => 'waiting',
        ]);

        $user = User::create([
            'name' => 'Candidate 1',
            'email' => 'c1@example.com',
            'password' => bcrypt('password'),
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'quota' => 10,
            'price' => 100000,
        ]);

        // Create dummy registration to consume ID 1
        $dummyUser = User::create([
            'name' => 'Dummy User',
            'email' => 'dummy@example.com',
            'password' => bcrypt('password'),
        ]);
        Registration::create([
            'user_id' => $dummyUser->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-DUMMY',
            'is_checked_in' => false,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-AUDIT',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        // Draw via web admin POST request
        $response = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prize->id,
            ], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        $winnerData = $response->json('data');
        $this->assertNotNull($winnerData);
        
        // Assert that the returned ID is the LuckyDrawWinner's ID, not the Registration's ID
        $winnerRecord = LuckyDrawWinner::first();
        $this->assertEquals($winnerRecord->id, $winnerData['id']);
        $this->assertNotEquals($registration->id, $winnerData['id']); // Ensure it is not registration_id
        $this->assertEquals($registration->id, $winnerData['registration_id']); // registration_id is correct
    }

    public function test_destroy_correctly_deletes_winner_and_resets_prize_drawn_count_and_status()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-destroy@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Destroy Audit Event',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'iPhone 16 Pro',
            'winner_count' => 1,
            'status' => 'completed',
            'drawn_count' => 1,
        ]);

        $user = User::create([
            'name' => 'Candidate 2',
            'email' => 'c2@example.com',
            'password' => bcrypt('password'),
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'quota' => 10,
            'price' => 100000,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-DESTROY',
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        $winner = LuckyDrawWinner::create([
            'event_id' => $event->id,
            'registration_id' => $registration->id,
            'event_prize_id' => $prize->id,
            'prize_name' => $prize->name,
            'draw_number' => 1,
            'won_at' => now(),
        ]);

        // Prior to deletion
        $this->assertEquals(1, $prize->fresh()->drawn_count);
        $this->assertEquals('completed', $prize->fresh()->status);
        $this->assertDatabaseHas('lucky_draw_winners', ['id' => $winner->id]);

        // Delete via web admin DELETE request
        $response = $this->actingAs($admin)
            ->delete("/admin/lucky-draw/{$winner->id}", [], [
                'Accept' => 'application/json'
            ]);

        // Assert databases values are updated
        $this->assertEquals(0, $prize->fresh()->drawn_count);
        $this->assertEquals('waiting', $prize->fresh()->status);
        $this->assertDatabaseMissing('lucky_draw_winners', ['id' => $winner->id]);
    }

    public function test_redraw_exclusion_is_scoped_to_current_redraw_cycle_for_current_prize()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-test-1@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Redraw Scope Event',
            'location' => 'Bandung',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $prizeA = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'Prize A',
            'winner_count' => 1,
            'status' => 'waiting',
        ]);

        $prizeB = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'Prize B',
            'winner_count' => 1,
            'status' => 'waiting',
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'quota' => 10,
            'price' => 100000,
        ]);

        // Create 4 participants A, B, C, D
        $users = [];
        $registrations = [];
        foreach (['A', 'B', 'C', 'D'] as $name) {
            $user = User::create([
                'name' => 'User ' . $name,
                'email' => strtolower($name) . '@example.com',
                'password' => bcrypt('password'),
            ]);
            $users[$name] = $user;

            $registrations[$name] = Registration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_category_id' => $ticketCategory->id,
                'qr_code' => 'QR-' . $name,
                'is_checked_in' => true,
                'registration_status' => 'active',
                'payment_status' => 'paid',
            ]);
        }

        // Draw Prize A
        $response = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prizeA->id,
            ], [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200);
        $winnerData = $response->json('data');
        $winnerUserId = $winnerData['registration']['user_id'];
        $winnerWinnerId = $winnerData['id'];

        // Cancel/reset the winner via DELETE
        $responseDelete = $this->actingAs($admin)
            ->delete("/admin/lucky-draw/{$winnerWinnerId}", [], [
                'Accept' => 'application/json'
            ]);
        $responseDelete->assertStatus(200);

        // Perform redraw for Prize A, passing the cancelled winner's user ID in exclude_user_ids
        $responseRedraw = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prizeA->id,
                'exclude_user_ids' => [$winnerUserId]
            ], [
                'Accept' => 'application/json'
            ]);

        $responseRedraw->assertStatus(200);
        $redrawWinnerData = $responseRedraw->json('data');
        $redrawWinnerUserId = $redrawWinnerData['registration']['user_id'];

        // The redraw winner MUST NOT be the first winner
        $this->assertNotEquals($winnerUserId, $redrawWinnerUserId);

        // Cancel/exclude C and D by changing their registration statuses
        // This leaves only A (the cancelled winner) and the redraw winner.
        // But the redraw winner won Prize A (is still in lucky_draw_winners).
        // If A is eligible again for Prize B, drawing Prize B should select A.
        foreach (['A', 'B', 'C', 'D'] as $name) {
            $uId = $users[$name]->id;
            if ($uId !== $winnerUserId && $uId !== $redrawWinnerUserId) {
                $registrations[$name]->registration_status = 'cancelled';
                $registrations[$name]->save();
            }
        }

        // Draw Prize B. The cancelled winner A must win.
        $responseB = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prizeB->id,
            ], [
                'Accept' => 'application/json'
            ]);

        $responseB->assertStatus(200);
        $winnerBData = $responseB->json('data');
        $this->assertEquals($winnerUserId, $winnerBData['registration']['user_id']);
    }

    public function test_multiple_consecutive_redraws_accumulate_exclusions_on_same_prize()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-test-2@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $event = Event::create([
            'name' => 'Consecutive Redraw Event',
            'location' => 'Bandung',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-03',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        $prize = EventPrize::create([
            'event_id' => $event->id,
            'name' => 'Grand Prize',
            'winner_count' => 1,
            'status' => 'waiting',
        ]);

        $ticketCategory = TicketCategory::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'quota' => 10,
            'price' => 100000,
        ]);

        // Create 4 participants A, B, C, D
        $users = [];
        $registrations = [];
        foreach (['A', 'B', 'C', 'D'] as $name) {
            $user = User::create([
                'name' => 'User ' . $name,
                'email' => strtolower($name) . '@example.com',
                'password' => bcrypt('password'),
            ]);
            $users[$name] = $user;

            $registrations[$name] = Registration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_category_id' => $ticketCategory->id,
                'qr_code' => 'QR-' . $name,
                'is_checked_in' => true,
                'registration_status' => 'active',
                'payment_status' => 'paid',
            ]);
        }

        // Draw #1
        $response1 = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prize->id,
            ], [
                'Accept' => 'application/json'
            ]);
        $response1->assertStatus(200);
        $w1 = $response1->json('data');
        $u1 = $w1['registration']['user_id'];

        // Cancel winner 1
        $this->actingAs($admin)->delete("/admin/lucky-draw/{$w1['id']}", [], ['Accept' => 'application/json']);

        // Draw #2 (exclude u1)
        $response2 = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prize->id,
                'exclude_user_ids' => [$u1]
            ], [
                'Accept' => 'application/json'
            ]);
        $response2->assertStatus(200);
        $w2 = $response2->json('data');
        $u2 = $w2['registration']['user_id'];

        $this->assertNotEquals($u1, $u2);

        // Cancel winner 2
        $this->actingAs($admin)->delete("/admin/lucky-draw/{$w2['id']}", [], ['Accept' => 'application/json']);

        // Draw #3 (exclude u1, u2)
        $response3 = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prize->id,
                'exclude_user_ids' => [$u1, $u2]
            ], [
                'Accept' => 'application/json'
            ]);
        $response3->assertStatus(200);
        $w3 = $response3->json('data');
        $u3 = $w3['registration']['user_id'];

        $this->assertNotEquals($u1, $u3);
        $this->assertNotEquals($u2, $u3);

        // Cancel winner 3
        $this->actingAs($admin)->delete("/admin/lucky-draw/{$w3['id']}", [], ['Accept' => 'application/json']);

        // Draw #4 (exclude u1, u2, u3). Only 1 candidate remains.
        $response4 = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prize->id,
                'exclude_user_ids' => [$u1, $u2, $u3]
            ], [
                'Accept' => 'application/json'
            ]);
        $response4->assertStatus(200);
        $w4 = $response4->json('data');
        $u4 = $w4['registration']['user_id'];

        $allUsers = [$users['A']->id, $users['B']->id, $users['C']->id, $users['D']->id];
        $remainingUser = array_values(array_diff($allUsers, [$u1, $u2, $u3]))[0];
        $this->assertEquals($remainingUser, $u4);

        // Cancel winner 4
        $this->actingAs($admin)->delete("/admin/lucky-draw/{$w4['id']}", [], ['Accept' => 'application/json']);

        // Draw #5 (exclude u1, u2, u3, u4). No candidates left, should fail.
        $response5 = $this->actingAs($admin)
            ->post("/admin/lucky-draw/draw", [
                'event_prize_id' => $prize->id,
                'exclude_user_ids' => [$u1, $u2, $u3, $u4]
            ], [
                'Accept' => 'application/json'
            ]);
        $response5->assertStatus(404);
    }
}

