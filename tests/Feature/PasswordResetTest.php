<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a dummy user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);
    }

    public function test_forgot_password_sends_otp()
    {
        Mail::fake();

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);

        // Assert record exists in password_reset_tokens
        $this->assertTrue(DB::table('password_reset_tokens')->where('email', 'test@example.com')->exists());

        // Assert mail was sent
        Mail::assertSent(SendOtpMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_forgot_password_fails_if_email_not_exists()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'notfound@example.com'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_verify_otp_success()
    {
        // Manually insert an OTP record
        $otp = '123456';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($otp),
            'created_at' => now()
        ]);

        $response = $this->postJson('/api/verify-otp', [
            'email' => 'test@example.com',
            'otp' => '123456'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Kode OTP berhasil diverifikasi.']);
    }

    public function test_verify_otp_fails_if_incorrect()
    {
        $otp = '123456';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($otp),
            'created_at' => now()
        ]);

        $response = $this->postJson('/api/verify-otp', [
            'email' => 'test@example.com',
            'otp' => '000000'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Kode OTP salah. Silakan periksa kembali.']);
    }

    public function test_reset_password_success()
    {
        $otp = '123456';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($otp),
            'created_at' => now()
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'test@example.com',
            'otp' => '123456',
            'password' => 'newpassword123'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Password Anda berhasil diperbarui. Silakan login kembali.']);

        // Assert password changed in DB
        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('newpassword123', $user->password));

        // Assert token deleted
        $this->assertFalse(DB::table('password_reset_tokens')->where('email', 'test@example.com')->exists());
    }
}
