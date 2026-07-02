<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $participant;
    protected $event;
    protected $registration;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => Hash::make('Admin@123'),
        ]);

        // Create Participant
        $this->participant = User::create([
            'name' => 'John Doe Participant',
            'email' => 'john@joyvent.com',
            'role' => 'participant',
            'password' => Hash::make('password'),
        ]);

        // Create Event
        $this->event = Event::create([
            'name' => 'JoyVent Tech Conference',
            'description' => 'Test Event Description',
            'category' => 'Technology',
            'location' => 'Virtual',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'capacity' => 100,
            'status' => 'open',
            'has_certificate' => true,
            'certificate_title' => 'Certificate of Excellence',
            'organizer_name' => 'JoyVent Organizer',
        ]);

        // Create Ticket Category
        $ticketCategory = \App\Models\TicketCategory::create([
            'event_id' => $this->event->id,
            'name' => 'VIP',
            'price' => 500000,
            'quota' => 10,
        ]);

        // Create Checked-In Registration
        $this->registration = Registration::create([
            'user_id' => $this->participant->id,
            'event_id' => $this->event->id,
            'ticket_category_id' => $ticketCategory->id,
            'qr_code' => 'QR-TEST',
            'is_checked_in' => true,
            'checked_in_at' => now(),
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Test certificate generation generates PNG file and sets relative path.
     */
    public function test_generate_certificate_creates_png_and_updates_db()
    {
        // 1. Create a mock template file in public/storage/certificates/templates
        $templatesDir = public_path('storage/certificates/templates');
        if (!File::exists($templatesDir)) {
            File::makeDirectory($templatesDir, 0755, true);
        }

        // Create a small blank white PNG image for template
        $templateFilename = 'template_' . $this->event->id . '.png';
        $templatePath = $templatesDir . '/' . $templateFilename;
        $img = imagecreatetruecolor(800, 600);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);
        imagepng($img, $templatePath);
        imagedestroy($img);

        // Update event certificate_template field
        $this->event->certificate_template = $templateFilename;
        $this->event->save();

        // 2. Perform the generate request
        $response = $this->actingAs($this->admin)->post(route('admin.certificates.generate'), [
            'event_id' => $this->event->id,
        ]);

        // 3. Assert redirected and certificate created
        $response->assertRedirect();
        
        $certificate = Certificate::where('registration_id', $this->registration->id)->first();
        $this->assertNotNull($certificate);
        
        // Assert certificate_file does NOT have storage/ prefix and starts with certificates/generated/
        $this->assertStringStartsWith('certificates/generated/', $certificate->certificate_file);
        $this->assertStringEndsWith($certificate->certificate_code . '.png', $certificate->certificate_file);
        
        // Assert physical file exists
        $physicalFilePath = public_path('storage/' . $certificate->certificate_file);
        $this->assertTrue(File::exists($physicalFilePath));

        // Clean up
        if (File::exists($templatePath)) {
            File::delete($templatePath);
        }
        if (File::exists($physicalFilePath)) {
            File::delete($physicalFilePath);
        }
    }

    /**
     * Test certificate generation handles template missing gracefully (no crash).
     */
    public function test_generate_certificate_without_template_does_not_crash()
    {
        // Ensure no template exists
        $this->event->certificate_template = null;
        $this->event->save();

        // Perform request
        $response = $this->actingAs($this->admin)->post(route('admin.certificates.generate'), [
            'event_id' => $this->event->id,
        ]);

        $response->assertRedirect();
        
        // Assert certificate created in database
        $certificate = Certificate::where('registration_id', $this->registration->id)->first();
        $this->assertNotNull($certificate);
        
        // Since template was not found, certificate_file should remain NULL (gracefully bypassed)
        $this->assertNull($certificate->certificate_file);
    }

    /**
     * Test finished event detail page renders the certificate dispatch section and button.
     */
    public function test_finished_event_detail_page_renders_dispatch_form()
    {
        // Force the event start/end dates to the past to make it finished
        $this->event->start_date = now()->subDays(5)->toDateString();
        $this->event->end_date = now()->subDays(3)->toDateString();
        $this->event->save();

        $response = $this->actingAs($this->admin)->get(route('admin.events.finished', $this->event->id));

        $response->assertStatus(200);
        $response->assertSee('Auto Certificate Dispatch');
        $response->assertSee('Mass Issue Certificates');
        $response->assertSee('Pending Generation');
    }

    /**
     * Test upcoming event detail page renders the certificate dispatch section dynamically.
     */
    public function test_upcoming_event_detail_page_renders_dispatch_form()
    {
        // Force the event start/end dates to the future to make it upcoming
        $this->event->start_date = now()->addDays(3)->toDateString();
        $this->event->end_date = now()->addDays(5)->toDateString();
        $this->event->save();

        $response = $this->actingAs($this->admin)->get(route('admin.events.upcoming', $this->event->id));

        $response->assertStatus(200);
        $response->assertSee('Auto Certificate Dispatch');
        $response->assertSee('Mass Issue Certificates');
        $response->assertSee('Pending Generation');
    }

    /**
     * Test download PDF success for owner.
     */
    public function test_download_pdf_success_for_owner()
    {
        // Create certificate record
        $certificate = Certificate::create([
            'registration_id' => $this->registration->id,
            'certificate_code' => 'CERT-OWNER-123',
            'certificate_file' => 'certificates/generated/CERT-OWNER-123.png',
            'is_valid' => true,
        ]);

        // Create a dummy physical PNG file
        $generatedDir = public_path('storage/certificates/generated');
        if (!File::exists($generatedDir)) {
            File::makeDirectory($generatedDir, 0755, true);
        }
        $physicalPath = $generatedDir . '/CERT-OWNER-123.png';
        $img = imagecreatetruecolor(100, 100);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);
        imagepng($img, $physicalPath);
        imagedestroy($img);

        // Act: login as owner (participant) and request download
        $response = $this->actingAs($this->participant)->get('/api/certificates/' . $certificate->id . '/download');

        // Assert: 200 OK, application/pdf header, filename is correct
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'inline; filename=JoyVent_Certificate_CERT-OWNER-123.pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());

        // Clean up
        if (File::exists($physicalPath)) {
            File::delete($physicalPath);
        }
    }

    /**
     * Test download PDF forbidden for non-owner.
     */
    public function test_download_pdf_forbidden_for_non_owner()
    {
        // Create certificate record
        $certificate = Certificate::create([
            'registration_id' => $this->registration->id,
            'certificate_code' => 'CERT-FORBIDDEN-123',
            'certificate_file' => 'certificates/generated/CERT-FORBIDDEN-123.png',
            'is_valid' => true,
        ]);

        // Create a dummy physical PNG file
        $generatedDir = public_path('storage/certificates/generated');
        if (!File::exists($generatedDir)) {
            File::makeDirectory($generatedDir, 0755, true);
        }
        $physicalPath = $generatedDir . '/CERT-FORBIDDEN-123.png';
        $img = imagecreatetruecolor(100, 100);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);
        imagepng($img, $physicalPath);
        imagedestroy($img);

        // Create another user
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@joyvent.com',
            'role' => 'participant',
            'password' => Hash::make('password'),
        ]);

        // Act: login as other user and request download
        $response = $this->actingAs($otherUser)->get('/api/certificates/' . $certificate->id . '/download');

        // Assert: 403 Forbidden
        $response->assertStatus(403);

        // Clean up
        if (File::exists($physicalPath)) {
            File::delete($physicalPath);
        }
    }

    /**
     * Test download PDF returns 404 if certificate model not found (Route Model Binding).
     */
    public function test_download_pdf_not_found_for_invalid_id()
    {
        $response = $this->actingAs($this->participant)->get('/api/certificates/99999/download');
        $response->assertStatus(404);
    }

    /**
     * Test download PDF returns 409 conflict when file path is missing or file not found.
     */
    public function test_download_pdf_conflict_for_missing_file()
    {
        // Case 1: certificate_file column is NULL
        $certificate1 = Certificate::create([
            'registration_id' => $this->registration->id,
            'certificate_code' => 'CERT-MISSING-1',
            'certificate_file' => null,
            'is_valid' => true,
        ]);

        $response1 = $this->actingAs($this->participant)->get('/api/certificates/' . $certificate1->id . '/download');
        $response1->assertStatus(409);
        $response1->assertJsonFragment(['message' => 'Certificate has not been generated yet.']);

        // Case 2: certificate_file column is set but physical file is missing
        $certificate2 = Certificate::create([
            'registration_id' => $this->registration->id,
            'certificate_code' => 'CERT-MISSING-2',
            'certificate_file' => 'certificates/generated/nonexistent.png',
            'is_valid' => true,
        ]);

        $response2 = $this->actingAs($this->participant)->get('/api/certificates/' . $certificate2->id . '/download');
        $response2->assertStatus(409);
        $response2->assertJsonFragment(['message' => 'Certificate has not been generated yet.']);
    }
}
