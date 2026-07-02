<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketCategory;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManualPaymentVerificationTest extends TestCase
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

        // Create Admin
        $this->admin = User::create([
            'name' => 'Admin JoyVent',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // Create Participant
        $this->participant = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
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
            'qr_code' => 'QR-TEST-5678',
            'status' => 'pending',
            'registration_status' => 'active',
            'payment_status' => 'pending',
            'payment_amount' => 500000,
            'payment_method' => 'transfer',
            'payment_gateway' => 'manual',
        ]);

        Storage::fake('public');
    }

    /**
     * Test retrieving payment settings returns the custom support contact and other fields.
     */
    public function test_get_payment_settings_includes_payment_contact()
    {
        // Seed setting values
        Setting::setValue('payment_contact', '0812-9999-8888');
        Setting::setValue('payment_instruction', 'Transfer precisely the amount then upload the proof.');

        $response = $this->actingAs($this->participant, 'sanctum')->getJson('/api/payment-settings');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.payment_contact', '0812-9999-8888')
            ->assertJsonPath('data.payment_instruction', 'Transfer precisely the amount then upload the proof.');
    }

    /**
     * Test participant can upload payment proof and status transitions to waiting_verification.
     */
    public function test_participant_can_upload_payment_proof()
    {
        $file = UploadedFile::fake()->image('proof.jpg', 800, 600)->size(1024); // 1MB

        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$this->registration->id}/upload-payment-proof", [
            'payment_proof' => $file
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.payment_status', 'waiting_verification');

        $this->registration->refresh();
        $this->assertEquals('waiting_verification', $this->registration->payment_status);
        $this->assertNotNull($this->registration->payment_proof);
        $this->assertNotNull($this->registration->payment_proof_uploaded_at);
        $this->assertGreaterThan(0, $this->registration->payment_proof_size);

        Storage::disk('public')->assertExists($this->registration->payment_proof);
    }

    /**
     * Test re-uploading payment proof overwrites the previous proof file and updates metadata.
     */
    public function test_upload_payment_proof_overwrites_old_proof()
    {
        // 1. Upload first file
        $file1 = UploadedFile::fake()->image('proof1.jpg', 800, 600)->size(500);
        $response1 = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$this->registration->id}/upload-payment-proof", [
            'payment_proof' => $file1
        ]);
        $response1->assertStatus(200);

        $this->registration->refresh();
        $path1 = $this->registration->payment_proof;
        Storage::disk('public')->assertExists($path1);

        // Transition status back to rejected to simulate re-upload with audit trail
        $this->registration->update([
            'payment_status' => 'rejected',
            'payment_rejection_reason' => 'Previous reason',
            'payment_verified_by' => $this->admin->id,
            'payment_verified_at' => now()->subHour(),
            'payment_rejected_by' => $this->admin->id,
            'payment_rejected_at' => now()->subMinutes(30),
        ]);

        // 2. Upload second file
        $file2 = UploadedFile::fake()->image('proof2.png', 800, 600)->size(800);
        $response2 = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$this->registration->id}/upload-payment-proof", [
            'payment_proof' => $file2
        ]);
        $response2->assertStatus(200);

        $this->registration->refresh();
        $path2 = $this->registration->payment_proof;

        // Verify new file is saved and old file is deleted
        $this->assertNotEquals($path1, $path2);
        Storage::disk('public')->assertMissing($path1);
        Storage::disk('public')->assertExists($path2);

        // Verify audit trail details are reset
        $this->assertNull($this->registration->payment_rejection_reason);
        $this->assertNull($this->registration->payment_verified_by);
        $this->assertNull($this->registration->payment_verified_at);
        $this->assertNull($this->registration->payment_rejected_by);
        $this->assertNull($this->registration->payment_rejected_at);
    }

    /**
     * Test admin can approve payment, updating the audit trail fields correctly.
     */
    public function test_admin_can_approve_payment_with_audit_trail()
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
        $this->assertEquals('paid', $this->registration->payment_status);
        $this->assertEquals($this->admin->id, $this->registration->payment_verified_by);
        $this->assertNotNull($this->registration->payment_verified_at);
        $this->assertNull($this->registration->payment_rejection_reason);
    }

    /**
     * Test admin can reject payment, recording audit trail and the rejection reason.
     */
    public function test_admin_can_reject_payment_with_audit_trail()
    {
        $this->registration->update([
            'payment_status' => 'waiting_verification',
            'payment_proof' => 'proofs/test_proof.jpg',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/reject-payment", [
            'payment_rejection_reason' => 'Berkas bukti transfer buram dan tidak terbaca.'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->registration->refresh();
        $this->assertEquals('pending', $this->registration->status);
        $this->assertEquals('rejected', $this->registration->payment_status);
        $this->assertEquals($this->admin->id, $this->registration->payment_rejected_by);
        $this->assertNotNull($this->registration->payment_rejected_at);
        $this->assertEquals('Berkas bukti transfer buram dan tidak terbaca.', $this->registration->payment_rejection_reason);
    }

    /**
     * Test admin cannot approve payment if payment status is not waiting_verification or payment proof is empty.
     */
    public function test_admin_cannot_approve_payment_invalid_status_or_missing_proof()
    {
        // Case 1: Status is pending (no proof)
        $this->registration->update([
            'payment_status' => 'pending',
            'payment_proof' => null,
        ]);
        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/confirm-payment");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotEquals('paid', $this->registration->refresh()->payment_status);

        // Case 2: Status is waiting_verification but proof is empty
        $this->registration->update([
            'payment_status' => 'waiting_verification',
            'payment_proof' => null,
        ]);
        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/confirm-payment");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotEquals('paid', $this->registration->refresh()->payment_status);
    }

    /**
     * Test admin cannot reject payment if payment status is not waiting_verification or payment proof is empty.
     */
    public function test_admin_cannot_reject_payment_invalid_status_or_missing_proof()
    {
        // Case 1: Status is pending (no proof)
        $this->registration->update([
            'payment_status' => 'pending',
            'payment_proof' => null,
        ]);
        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/reject-payment", [
            'payment_rejection_reason' => 'Proof missing'
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotEquals('rejected', $this->registration->refresh()->payment_status);

        // Case 2: Status is waiting_verification but proof is empty
        $this->registration->update([
            'payment_status' => 'waiting_verification',
            'payment_proof' => null,
        ]);
        $response = $this->actingAs($this->admin)->post("/admin/participants/{$this->registration->id}/reject-payment", [
            'payment_rejection_reason' => 'Proof missing'
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotEquals('rejected', $this->registration->refresh()->payment_status);
    }

    /**
     * Test participant cannot upload proof if the payment status is already paid or waiting verification.
     */
    public function test_participant_cannot_upload_proof_if_already_paid_or_waiting()
    {
        $file = UploadedFile::fake()->image('proof.jpg');

        // Case 1: Status is paid
        $this->registration->update(['payment_status' => 'paid']);
        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$this->registration->id}/upload-payment-proof", [
            'payment_proof' => $file
        ]);
        $response->assertStatus(422);

        // Case 2: Status is waiting_verification
        $this->registration->update(['payment_status' => 'waiting_verification']);
        $response = $this->actingAs($this->participant, 'sanctum')->postJson("/api/registrations/{$this->registration->id}/upload-payment-proof", [
            'payment_proof' => $file
        ]);
        $response->assertStatus(422);
    }
}
