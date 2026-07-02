<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'participant'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Register success',
            'token' => $token,
            'user' => $user
        ]);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            throw ValidationException::withMessages([
                'email' => ['Email atau password salah'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'token' => $token,
            'user' => $user
        ]);
    }

    // GOOGLE LOGIN
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $googleClientId = config('services.google.client_id');
        if (!$googleClientId) {
            return response()->json([
                'message' => 'Server misconfiguration: Google Client ID is missing.'
            ], 500);
        }

        try {
            $client = app(\Google_Client::class);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return response()->json([
                    'message' => 'Token Google tidak valid atau telah kedaluwarsa.'
                ], 401);
            }

            // Verify email ownership and verified status
            if (empty($payload['email']) || empty($payload['email_verified'])) {
                return response()->json([
                    'message' => 'Akun Google wajib memiliki email yang terverifikasi.'
                ], 401);
            }

            $email = $payload['email'];
            $name = $payload['name'] ?? $payload['given_name'] ?? 'User';

            // Find or create participant user
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(uniqid()),
                    'role' => 'participant'
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Google login success',
                'token' => $token,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            // Log internal error details server-side
            \Illuminate\Support\Facades\Log::error('Google login verification failure: ' . $e->getMessage(), [
                'exception' => $e,
                'id_token' => substr($request->id_token, 0, 30) . '...' // Safe logging
            ]);

            // Return generic error message to avoid exposing details
            return response()->json([
                'message' => 'Gagal memverifikasi akun Google.'
            ], 500);
        }
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}