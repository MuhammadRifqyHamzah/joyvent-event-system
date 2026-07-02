<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $participant;
    protected $event;
    protected $ticketCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Register CONCAT function for SQLite
        $connection = \Illuminate\Support\Facades\DB::connection();
        if ($connection->getDriverName() === 'sqlite') {
            $connection->getPdo()->sqliteCreateFunction('CONCAT', function (...$args) {
                return implode('', $args);
            });
        }

        // Disable foreign key constraints during seeding
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        TicketCategory::truncate();
        Event::truncate();
        User::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 1. Create Admin
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // 2. Create Participant
        $this->participant = User::create([
            'name' => 'Participant User',
            'email' => 'participant@joyvent.com',
            'role' => 'participant',
            'password' => bcrypt('password'),
        ]);

        // 3. Create Event
        $this->event = Event::create([
            'name' => 'JoyVent Summit 2026',
            'category' => 'Business',
            'location' => 'JCC Jakarta',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-12',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'capacity' => 100,
            'is_configured' => true,
        ]);

        // 4. Create Ticket Category
        $this->ticketCategory = TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Regular Ticket',
            'price' => 100000,
            'quota' => 50,
        ]);
    }

    /**
     * Test participant access to GET (read) endpoints succeeds.
     */
    public function test_participant_can_access_read_endpoints()
    {
        // GET /api/events
        $response = $this->actingAs($this->participant, 'sanctum')->getJson('/api/events');
        $response->assertStatus(200);

        // GET /api/events/{id}
        $response = $this->actingAs($this->participant, 'sanctum')->getJson("/api/events/{$this->event->id}");
        $response->assertStatus(200);

        // GET /api/ticket-categories
        $response = $this->actingAs($this->participant, 'sanctum')->getJson('/api/ticket-categories');
        $response->assertStatus(200);

        // GET /api/ticket-categories/{id}
        $response = $this->actingAs($this->participant, 'sanctum')->getJson("/api/ticket-categories/{$this->ticketCategory->id}");
        $response->assertStatus(200);
    }

    /**
     * Test participant access to write (admin-only) endpoints fails with 403.
     */
    public function test_participant_blocked_from_write_endpoints()
    {
        // POST /api/events -> 403
        $response = $this->actingAs($this->participant, 'sanctum')->postJson('/api/events', [
            'name' => 'New Event'
        ]);
        $response->assertStatus(403);

        // PUT /api/events/{id} -> 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/events/{$this->event->id}", [
            'name' => 'Updated Event'
        ]);
        $response->assertStatus(403);

        // DELETE /api/events/{id} -> 403
        $response = $this->actingAs($this->participant, 'sanctum')->deleteJson("/api/events/{$this->event->id}");
        $response->assertStatus(403);

        // POST /api/ticket-categories -> 403
        $response = $this->actingAs($this->participant, 'sanctum')->postJson('/api/ticket-categories', [
            'name' => 'New Category'
        ]);
        $response->assertStatus(403);

        // PUT /api/ticket-categories/{id} -> 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/ticket-categories/{$this->ticketCategory->id}", [
            'name' => 'Updated Category'
        ]);
        $response->assertStatus(403);

        // DELETE /api/ticket-categories/{id} -> 403
        $response = $this->actingAs($this->participant, 'sanctum')->deleteJson("/api/ticket-categories/{$this->ticketCategory->id}");
        $response->assertStatus(403);

        // POST /api/generate-certificate -> 403
        $response = $this->actingAs($this->participant, 'sanctum')->postJson('/api/generate-certificate', [
            'registration_id' => 1
        ]);
        $response->assertStatus(403);
    }

    /**
     * Test admin access to write endpoints is authorized (not 403).
     */
    public function test_admin_authorized_for_write_endpoints()
    {
        // POST /api/events -> Should trigger validation or succeed, not 403
        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/events', []);
        $this->assertNotEquals(403, $response->getStatusCode());

        // PUT /api/events/{id} -> Should succeed or validate, not 403
        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/events/{$this->event->id}", [
            'name' => 'Admin Updated Event'
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        // POST /api/ticket-categories -> Should not be 403
        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/ticket-categories', []);
        $this->assertNotEquals(403, $response->getStatusCode());

        // POST /api/generate-certificate -> Should not be 403
        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/generate-certificate', []);
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /**
     * Test participant can access, update, delete, and request refund for their own registrations.
     */
    public function test_participant_can_manage_own_registration()
    {
        $reg = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-OWN-REG',
            'status' => 'pending',
        ]);

        // Scenario 1: GET own registration -> 200 OK
        $response = $this->actingAs($this->participant, 'sanctum')->getJson("/api/registrations/{$reg->id}");
        $response->assertStatus(200);

        // PUT own registration -> should not be 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'payment_notes' => 'Some safe notes'
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        // Scenario 3: Request refund for own registration -> should not be 403
        $reg->update(['status' => 'confirmed']); // Reset status
        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$reg->id}/refund", [
            'reason' => 'Schedule conflict'
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        // DELETE own registration -> should not be 403
        $response = $this->actingAs($this->participant, 'sanctum')->deleteJson("/api/registrations/{$reg->id}");
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /**
     * Test participant is blocked (403) from accessing another user's registration.
     */
    public function test_participant_cannot_access_other_users_registration()
    {
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@joyvent.com',
            'role' => 'participant',
            'password' => bcrypt('password'),
        ]);

        $otherReg = Registration::create([
            'user_id' => $otherUser->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-OTHER-REG',
            'status' => 'pending',
        ]);

        // Scenario 2: GET other user's registration -> 403 Forbidden
        $response = $this->actingAs($this->participant, 'sanctum')->getJson("/api/registrations/{$otherReg->id}");
        $response->assertStatus(403);

        // PUT other user's registration -> 403 Forbidden
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/registrations/{$otherReg->id}", [
            'status' => 'cancelled'
        ]);
        $response->assertStatus(403);

        // Scenario 4: Request refund for other user's registration -> 403 Forbidden
        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$otherReg->id}/refund", [
            'reason' => 'Unauthorized refund attempt'
        ]);
        $response->assertStatus(403);

        // DELETE other user's registration -> 403 Forbidden
        $response = $this->actingAs($this->participant, 'sanctum')->deleteJson("/api/registrations/{$otherReg->id}");
        $response->assertStatus(403);
    }

    /**
     * Test admin can access any registration.
     */
    public function test_admin_can_access_any_registration()
    {
        $reg = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-ADMIN-VIEW',
            'status' => 'pending',
        ]);

        // Scenario 5: Admin accesses participant registration -> 200 OK (not 403)
        $response = $this->actingAs($this->admin, 'sanctum')->getJson("/api/registrations/{$reg->id}");
        $response->assertStatus(200);

        // Admin updates participant registration -> not 403
        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'status' => 'confirmed'
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        // Admin refunds participant registration -> not 403
        $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/registrations/{$reg->id}/refund", [
            'reason' => 'Admin override'
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        // Admin deletes participant registration -> not 403
        $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/registrations/{$reg->id}");
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    /**
     * Test registration list ownership isolation.
     */
    public function test_registration_list_ownership_isolation()
    {
        $participantB = User::create([
            'name' => 'Participant B',
            'email' => 'participant_b@joyvent.com',
            'role' => 'participant',
            'password' => bcrypt('password'),
        ]);

        // Create registration for Participant A (the default $this->participant)
        $regA = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-PARTICIPANT-A',
            'status' => 'pending',
        ]);

        // Create registration for Participant B
        $regB = Registration::create([
            'user_id' => $participantB->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-PARTICIPANT-B',
            'status' => 'pending',
        ]);

        // Scenario 1: Participant A gets registrations list -> expects only Participant A's registrations
        $response = $this->actingAs($this->participant, 'sanctum')->getJson('/api/registrations');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($regA->id, $data[0]['id']);

        // Scenario 2: Participant B gets registrations list -> expects only Participant B's registrations
        $response = $this->actingAs($participantB, 'sanctum')->getJson('/api/registrations');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($regB->id, $data[0]['id']);

        // Scenario 3: Admin gets registrations list -> expects all registrations
        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/registrations');
        $response->assertStatus(200);
        $data = $response->json('data');
        // Admin should see at least regA and regB (depending on test db seed state)
        $ids = collect($data)->pluck('id');
        $this->assertTrue($ids->contains($regA->id));
        $this->assertTrue($ids->contains($regB->id));
    }

    /**
     * Test payment status protection.
     */
    public function test_payment_status_protection()
    {
        $reg = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-SECURITY-PAY',
            'status' => 'pending',
            'registration_status' => 'active',
            'payment_status' => 'pending',
            'payment_amount' => 100000,
        ]);

        // 1. Participant cannot set payment_status = paid -> expects 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'payment_status' => 'paid'
        ]);
        $response->assertStatus(403);

        // 2. Participant cannot set status = confirmed -> expects 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'status' => 'confirmed'
        ]);
        $response->assertStatus(403);

        // 3. Participant cannot set registration_status = active -> expects 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'registration_status' => 'active'
        ]);
        $response->assertStatus(403);

        // 4. Participant cannot set paid_at -> expects 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'paid_at' => now()->toDateTimeString()
        ]);
        $response->assertStatus(403);

        // 5. Participant cannot modify payment_amount -> expects 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'payment_amount' => 100
        ]);
        $response->assertStatus(403);

        // 5b. Participant cannot set payment_gateway -> expects 403
        $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'payment_gateway' => 'paypal'
        ]);
        $response->assertStatus(403);

        // 6. Admin behavior remains functional -> expects 200 OK and updates fields
        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/registrations/{$reg->id}", [
            'payment_status' => 'paid'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('paid', $reg->fresh()->payment_status);
        $this->assertEquals('confirmed', $reg->fresh()->status);
    }

    /**
     * Test QR Check-in refund status validation constraints.
     */
    public function test_check_in_refund_hardening()
    {
        // 1. Refund pending => blocked (400)
        $regPending = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-REFUND-PENDING',
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        \App\Models\Refund::create([
            'registration_id' => $regPending->id,
            'reason' => 'Pending check',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/check-in', [
            'qr_code' => 'QR-REFUND-PENDING'
        ]);
        $response->assertStatus(400);
        $response->assertJsonPath('message', 'Check-in gagal: tiket sedang atau sudah direfund.');

        // 2. Refund approved => blocked (400)
        $regApproved = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-REFUND-APPROVED',
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        \App\Models\Refund::create([
            'registration_id' => $regApproved->id,
            'reason' => 'Approved check',
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/check-in', [
            'qr_code' => 'QR-REFUND-APPROVED'
        ]);
        $response->assertStatus(400);
        $response->assertJsonPath('message', 'Check-in gagal: tiket sedang atau sudah direfund.');

        // 3. Refund rejected => allowed (200)
        $regRejected = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-REFUND-REJECTED',
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        \App\Models\Refund::create([
            'registration_id' => $regRejected->id,
            'reason' => 'Rejected check',
            'status' => 'rejected'
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/check-in', [
            'qr_code' => 'QR-REFUND-REJECTED'
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Check-in berhasil');
    }

    /**
     * Test refund request business logic hardening rules.
     */
    public function test_refund_request_hardening()
    {
        // Case 1: payment_status = pending => refund rejected (422)
        $regUnpaid = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-UNPAID',
            'status' => 'pending',
            'registration_status' => 'active',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$regUnpaid->id}/refund", [
            'reason' => 'Need money back'
        ]);
        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Tiket yang belum dibayar tidak dapat direfund.');

        // Case 2: is_checked_in = true => refund rejected (422)
        $regCheckedIn = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-CHECKED-IN',
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
            'is_checked_in' => true,
        ]);

        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$regCheckedIn->id}/refund", [
            'reason' => 'Changed my mind'
        ]);
        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Tiket yang sudah digunakan untuk check-in tidak dapat direfund.');

        // Case 3: registration_status = cancelled => refund rejected (422)
        $regInactive = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-INACTIVE',
            'status' => 'pending',
            'registration_status' => 'cancelled',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$regInactive->id}/refund", [
            'reason' => 'Ticket is inactive'
        ]);
        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Tiket sudah tidak aktif dan tidak dapat direfund.');

        // Case 4: status = cancelled => refund rejected (422)
        $regCancelled = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-CANCELLED-STATUS',
            'status' => 'cancelled',
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$regCancelled->id}/refund", [
            'reason' => 'Ticket is cancelled'
        ]);
        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Tiket yang telah dibatalkan tidak dapat direfund.');

        // Case 5: payment_status = paid, registration_status = active, status = confirmed, is_checked_in = false => refund succeeds
        $regValid = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-VALID-REFUND',
            'status' => 'confirmed',
            'registration_status' => 'active',
            'payment_status' => 'paid',
            'is_checked_in' => false,
        ]);

        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$regValid->id}/refund", [
            'reason' => 'Valid cancellation reason'
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Refund request submitted successfully');
    }

    /**
     * Test payment expiration notification generation.
     */
    public function test_payment_expiration_notification()
    {
        // 1. Create a registration that has expired payment
        $reg = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-EXPIRED',
            'status' => 'pending',
            'registration_status' => 'active',
            'payment_status' => 'pending',
            'payment_expired_at' => now()->subMinutes(10), // expired 10 minutes ago
        ]);

        // 2. Run the payments:expire command
        $this->artisan('payments:expire')
            ->expectsOutput('Expired 1 pending registration(s).')
            ->assertExitCode(0);

        // Verify status fields are updated
        $reg->refresh();
        $this->assertEquals('cancelled', $reg->status);
        $this->assertEquals('cancelled', $reg->registration_status);
        $this->assertEquals('expired', $reg->payment_status);

        // 3. Generate and verify notification
        // Call the API endpoint GET /api/notifications which calls UserNotification::generateForUser
        $response = $this->actingAs($this->participant, 'sanctum')->getJson('/api/notifications');
        $response->assertStatus(200);

        // Verify the notification exists in database
        $notification = \App\Models\UserNotification::where('source_key', "payment_expired_{$reg->id}")->first();
        $this->assertNotNull($notification);
        $this->assertEquals('Pembayaran Kedaluwarsa', $notification->title);
        $this->assertStringContainsString('telah melewati batas waktu. Tiket otomatis dibatalkan.', $notification->message);
    }

    /**
     * Test notification generation does not crash when there are soft-deleted notifications.
     */
    public function test_notifications_generation_handles_soft_deleted_notifications()
    {
        // 1. Create a registration
        $reg = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $this->ticketCategory->id,
            'qr_code' => 'QR-SOFT-DELETE-TEST',
            'status' => 'pending',
            'registration_status' => 'active',
            'payment_status' => 'pending',
        ]);

        // 2. Initial notifications generation (triggers ticket_ordered generation)
        $response = $this->actingAs($this->participant, 'sanctum')->getJson('/api/notifications');
        $response->assertStatus(200);

        // Find the generated notification and soft delete it
        $notification = \App\Models\UserNotification::where('source_key', "ticket_ordered_{$reg->id}")->first();
        $this->assertNotNull($notification);
        $notification->delete(); // Soft delete

        // 3. Second notifications generation (must run successfully without unique constraint violation)
        $response = $this->actingAs($this->participant, 'sanctum')->getJson('/api/notifications');
        $response->assertStatus(200);

        // Verify the notification is still soft deleted and not duplicated
        $this->assertTrue($notification->fresh()->trashed());
        $count = \App\Models\UserNotification::withTrashed()->where('source_key', "ticket_ordered_{$reg->id}")->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test QR Check-in with unauthenticated user (no token) returns 401.
     */
    public function test_check_in_unauthenticated_returns_401()
    {
        $response = $this->postJson('/api/check-in', [
            'qr_code' => 'SOME-QR'
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test QR Check-in route protection and controller-level defense-in-depth protection.
     */
    public function test_check_in_route_protection_by_role()
    {
        // 1. Route-level middleware check for Participant -> 403 Forbidden
        $response = $this->actingAs($this->participant, 'sanctum')->postJson('/api/check-in', [
            'qr_code' => 'SOME-QR'
        ]);
        $response->assertStatus(403);
        $response->assertJsonPath('message', 'Unauthorized. Admin role required.');

        // 2. Controller-level defense-in-depth check for Participant -> 403 Forbidden with 'Unauthorized.'
        $controller = new \App\Http\Controllers\Api\CheckInController();
        $request = \Illuminate\Http\Request::create('/api/check-in', 'POST', ['qr_code' => 'SOME-QR']);
        $request->setUserResolver(function () {
            return $this->participant;
        });
        
        $responseControllerPart = $controller->checkIn($request);
        $this->assertEquals(403, $responseControllerPart->getStatusCode());
        
        $data = $responseControllerPart->getData();
        $this->assertEquals('Unauthorized.', $data->message);

        // 3. Controller-level check for Admin -> Proceeds past role check (returns 404 validation as QR not found, not 403)
        $requestAdmin = \Illuminate\Http\Request::create('/api/check-in', 'POST', ['qr_code' => 'NON-EXISTENT-QR']);
        $requestAdmin->setUserResolver(function () {
            return $this->admin;
        });

        $responseControllerAdmin = $controller->checkIn($requestAdmin);
        $this->assertEquals(404, $responseControllerAdmin->getStatusCode());
        $this->assertEquals('QR Code tidak valid', $responseControllerAdmin->getData()->message);
    }
}
