<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $user = Auth::user();

        // System Statistics
        $totalEvents = Event::where('is_configured', 1)->count();
        $totalParticipants = User::where('role', 'participant')->count();

        // Software versions
        $laravelVersion = app()->version();
        $phpVersion = PHP_VERSION;

        // Organizer Settings
        $organizerName = Setting::getValue('organizer_name', 'JoyVent Organizer');
        $organizerEmail = Setting::getValue('organizer_email', 'admin@joyvent.com');
        $organizerPhone = Setting::getValue('organizer_phone', '08123456789');

        // Manual Payment Settings
        $paymentQrisImage = Setting::getValue('payment_qris_image');
        $paymentBankName = Setting::getValue('payment_bank_name', 'Bank BCA');
        $paymentBankAccountNumber = Setting::getValue('payment_bank_account_number', '126 1234 5678 9101');
        $paymentBankAccountName = Setting::getValue('payment_bank_account_name', 'JoyVent Organizer');
        $paymentInstruction = Setting::getValue('payment_instruction', 'Silakan transfer sesuai nominal yang tertera. Setelah pembayaran berhasil, unggah bukti pembayaran. Verifikasi maksimal 1x24 jam.');
        $paymentContact = Setting::getValue('payment_contact', '08123456789');

        return view('admin.settings.index', compact(
            'user',
            'totalEvents',
            'totalParticipants',
            'laravelVersion',
            'phpVersion',
            'organizerName',
            'organizerEmail',
            'organizerPhone',
            'paymentQrisImage',
            'paymentBankName',
            'paymentBankAccountNumber',
            'paymentBankAccountName',
            'paymentInstruction',
            'paymentContact'
        ));
    }

    /**
     * Update the admin's profile info.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|max:2048' // max 2MB
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Profil berhasil diperbarui! 👤');
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()
                ->back()
                ->withErrors(['current_password' => 'Password saat ini salah.'])
                ->withInput();
        }

        // Save new password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Password berhasil diperbarui! 🔒');
    }

    /**
     * Update organizer settings info.
     */
    public function updateOrganizer(Request $request)
    {
        $request->validate([
            'organizer_name' => 'required|string|max:255',
            'organizer_email' => 'required|email|max:255',
            'organizer_phone' => 'required|string|max:20',
        ]);

        Setting::setValue('organizer_name', $request->organizer_name);
        Setting::setValue('organizer_email', $request->organizer_email);
        Setting::setValue('organizer_phone', $request->organizer_phone);

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Informasi Organizer berhasil diperbarui! 🏢');
    }

    /**
     * Update payment settings.
     */
    public function updatePaymentSettings(Request $request)
    {
        $request->validate([
            'payment_bank_name' => 'required|string|max:50',
            'payment_bank_account_number' => 'required|string|max:50',
            'payment_bank_account_name' => 'required|string|max:100',
            'payment_instruction' => 'required|string',
            'payment_contact' => 'required|string|max:100',
            'payment_qris_image' => 'nullable|image|max:2048' // max 2MB
        ]);

        Setting::setValue('payment_bank_name', $request->payment_bank_name);
        Setting::setValue('payment_bank_account_number', $request->payment_bank_account_number);
        Setting::setValue('payment_bank_account_name', $request->payment_bank_account_name);
        Setting::setValue('payment_instruction', $request->payment_instruction);
        Setting::setValue('payment_contact', $request->payment_contact);

        if ($request->hasFile('payment_qris_image')) {
            $oldQris = Setting::getValue('payment_qris_image');
            if ($oldQris) {
                Storage::disk('public')->delete($oldQris);
            }

            $path = $request->file('payment_qris_image')->store('settings', 'public');
            Setting::setValue('payment_qris_image', $path);
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Pengaturan pembayaran manual berhasil diperbarui! 💳');
    }
}
