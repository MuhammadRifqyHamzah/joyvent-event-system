<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test successful Google login with a valid ID Token.
     */
    public function test_google_login_success_with_valid_token()
    {
        // 1. Mock Google_Client
        $mockGoogleClient = Mockery::mock(\Google_Client::class);
        $mockGoogleClient->shouldReceive('verifyIdToken')
            ->once()
            ->with('valid-google-id-token')
            ->andReturn([
                'email' => 'googleuser@test.com',
                'name' => 'Google User',
                'email_verified' => true
            ]);

        // Bind mock instance to the application container
        $this->app->instance(\Google_Client::class, $mockGoogleClient);

        // 2. Call API
        $response = $this->postJson('/api/google-login', [
            'id_token' => 'valid-google-id-token'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'token',
            'user' => ['id', 'name', 'email', 'role']
        ]);

        $response->assertJsonPath('user.email', 'googleuser@test.com');
        $response->assertJsonPath('user.name', 'Google User');
        $response->assertJsonPath('user.role', 'participant');

        // Assert user was created in DB
        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@test.com',
            'role' => 'participant'
        ]);
    }

    /**
     * Test Google login fails if the ID Token is invalid or expired.
     */
    public function test_google_login_fails_with_invalid_token()
    {
        $mockGoogleClient = Mockery::mock(\Google_Client::class);
        $mockGoogleClient->shouldReceive('verifyIdToken')
            ->once()
            ->with('invalid-token')
            ->andReturn(false); // invalid token returns false

        $this->app->instance(\Google_Client::class, $mockGoogleClient);

        $response = $this->postJson('/api/google-login', [
            'id_token' => 'invalid-token'
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Token Google tidak valid atau telah kedaluwarsa.');
    }

    /**
     * Test Google login fails if email ownership is unverified.
     */
    public function test_google_login_fails_with_unverified_email()
    {
        $mockGoogleClient = Mockery::mock(\Google_Client::class);
        $mockGoogleClient->shouldReceive('verifyIdToken')
            ->once()
            ->with('unverified-email-token')
            ->andReturn([
                'email' => 'googleuser@test.com',
                'name' => 'Google User',
                'email_verified' => false // email is not verified
            ]);

        $this->app->instance(\Google_Client::class, $mockGoogleClient);

        $response = $this->postJson('/api/google-login', [
            'id_token' => 'unverified-email-token'
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Akun Google wajib memiliki email yang terverifikasi.');
    }

    /**
     * Test Google login returns generic 500 error on internal library exception.
     */
    public function test_google_login_returns_generic_500_on_internal_error()
    {
        $mockGoogleClient = Mockery::mock(\Google_Client::class);
        $mockGoogleClient->shouldReceive('verifyIdToken')
            ->once()
            ->with('error-token')
            ->andThrow(new \Exception('Connection timeout to googleapis.com'));

        $this->app->instance(\Google_Client::class, $mockGoogleClient);

        $response = $this->postJson('/api/google-login', [
            'id_token' => 'error-token'
        ]);

        $response->assertStatus(500);
        $response->assertJsonPath('message', 'Gagal memverifikasi akun Google.');
    }
}
