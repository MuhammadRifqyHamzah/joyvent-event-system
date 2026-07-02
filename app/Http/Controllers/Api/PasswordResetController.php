<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // 1. REQUEST OTP (FORGOT PASSWORD)
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.'
        ]);

        $otp = (string) rand(100000, 999999);

        // Save token to DB (hash it for security)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now()
            ]
        );

        // Send Email
        try {
            Mail::to($request->email)->send(new SendOtpMail($otp));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim email OTP: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'message' => 'Gagal mengirim email verifikasi. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Kode OTP berhasil dikirim ke email Anda.'
        ]);
    }

    // 2. VERIFY OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus berupa 6 digit angka.'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Tidak ada kode OTP yang aktif untuk email ini.'
            ], 400);
        }

        // Check expiration (10 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return response()->json([
                'message' => 'Kode OTP telah kedaluwarsa. Silakan ajukan ulang.'
            ], 400);
        }

        // Verify hash
        if (!Hash::check($request->otp, $record->token)) {
            return response()->json([
                'message' => 'Kode OTP salah. Silakan periksa kembali.'
            ], 400);
        }

        return response()->json([
            'message' => 'Kode OTP berhasil diverifikasi.'
        ]);
    }

    // 3. RESET PASSWORD
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Tidak ada permintaan reset password yang aktif.'
            ], 400);
        }

        // Check expiration
        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return response()->json([
                'message' => 'Kode OTP telah kedaluwarsa. Silakan ajukan ulang.'
            ], 400);
        }

        // Verify hash
        if (!Hash::check($request->otp, $record->token)) {
            return response()->json([
                'message' => 'Kode OTP salah. Silakan periksa kembali.'
            ], 400);
        }

        // Update User Password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password Anda berhasil diperbarui. Silakan login kembali.'
        ]);
    }
}
