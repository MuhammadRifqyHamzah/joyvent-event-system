<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Clean tables
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Setting::truncate();
        User::truncate();
        Event::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin JoyVent',
            'email' => 'admin@joyvent.com',
            'role' => 'admin',
            'password' => Hash::make('Admin@123'),
        ]);

        // Default values for settings are seeded via migration, 
        // but for RefreshDatabase/Testing we initialize them manually.
        Setting::setValue('organizer_name', 'JoyVent Organizer');
        Setting::setValue('organizer_email', 'admin@joyvent.com');
        Setting::setValue('organizer_phone', '08123456789');
    }

    /**
     * Test admin can access the settings index page.
     */
    public function test_admin_can_access_settings_page()
    {
        // Create a dummy event & participant to verify statistics counts
        Event::create([
            'name' => 'Test Event 1',
            'category' => 'Education',
            'location' => 'Jakarta',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-02',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'capacity' => 50,
            'is_configured' => true,
        ]);

        User::create([
            'name' => 'Participant 1',
            'email' => 'p1@test.com',
            'role' => 'participant',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/settings');

        $response->assertStatus(200);
        $response->assertSee('Settings');
        $response->assertSee('Admin JoyVent');
        $response->assertSee('admin@joyvent.com');
        $response->assertSee('JoyVent Organizer');
        $response->assertSee('08123456789');
        $response->assertSee('JoyVent Admin Panel');
        
        // Assert total counts
        $response->assertSee('1'); // total events and participants count
    }

    /**
     * Test admin can update profile name and email.
     */
    public function test_admin_can_update_profile()
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->admin)->post('/admin/settings/profile', [
            'name' => 'Super Admin',
            'email' => 'super@joyvent.com',
            'profile_photo' => $photo,
        ]);

        $response->assertRedirect();
        
        $this->admin->refresh();
        $this->assertEquals('Super Admin', $this->admin->name);
        $this->assertEquals('super@joyvent.com', $this->admin->email);
        $this->assertNotNull($this->admin->profile_photo);
        
        // Check photo file exists on disk
        Storage::disk('public')->assertExists($this->admin->profile_photo);
    }

    /**
     * Test password update validations and success behavior.
     */
    public function test_admin_can_update_password()
    {
        // 1. Success case
        $response = $this->actingAs($this->admin)->post('/admin/settings/password', [
            'current_password' => 'Admin@123',
            'new_password' => 'NewSecretPassword',
            'new_password_confirmation' => 'NewSecretPassword',
        ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('NewSecretPassword', $this->admin->refresh()->password));

        // 2. Failure: Invalid current password
        $response = $this->actingAs($this->admin)->post('/admin/settings/password', [
            'current_password' => 'wrong-password',
            'new_password' => 'NewPassword123',
            'new_password_confirmation' => 'NewPassword123',
        ]);
        $response->assertSessionHasErrors(['current_password']);

        // 3. Failure: Password confirmation mismatch
        $response = $this->actingAs($this->admin)->post('/admin/settings/password', [
            'current_password' => 'NewSecretPassword',
            'new_password' => 'NewPassword123',
            'new_password_confirmation' => 'mismatch',
        ]);
        $response->assertSessionHasErrors(['new_password']);
    }

    /**
     * Test admin can update global organizer info settings.
     */
    public function test_admin_can_update_organizer_info()
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings/organizer', [
            'organizer_name' => 'Updated Organizer Name',
            'organizer_email' => 'new-org@joyvent.com',
            'organizer_phone' => '08999999999',
        ]);

        $response->assertRedirect();

        $this->assertEquals('Updated Organizer Name', Setting::getValue('organizer_name'));
        $this->assertEquals('new-org@joyvent.com', Setting::getValue('organizer_email'));
        $this->assertEquals('08999999999', Setting::getValue('organizer_phone'));
    }
}
