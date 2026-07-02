<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $participant;

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
            'name' => 'Participant User',
            'email' => 'participant@joyvent.com',
            'role' => 'participant',
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * Scenario 1: Admin login succeeds and redirects to dashboard.
     */
    public function test_admin_login_success_redirects_to_dashboard()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@joyvent.com',
            'password' => 'Admin@123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * Scenario 2: Participant login is rejected and session is cleared.
     */
    public function test_participant_login_rejected_and_session_cleared()
    {
        $response = $this->post('/admin/login', [
            'email' => 'participant@joyvent.com',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        
        $errors = session('errors');
        $this->assertEquals('Akses ditolak. Hanya admin yang diizinkan masuk.', $errors->first('email'));
    }

    /**
     * Scenario 3: Guest trying to access dashboard is redirected to login.
     */
    public function test_guest_access_dashboard_redirects_to_login()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Scenario 4: Admin logout ends the session.
     */
    public function test_admin_logout_ends_session()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.logout'));

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    /**
     * Scenario 5: User enters valid email but wrong password.
     */
    public function test_invalid_password_login_fails()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@joyvent.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();

        $errors = session('errors');
        $this->assertEquals('Email atau password salah', $errors->first('email'));
    }
}
