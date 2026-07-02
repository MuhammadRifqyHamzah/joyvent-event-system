<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        $oldPhotoPath = $user->profile_photo;
        $emailChanged = ($request->email !== $user->email);
        $newPhotoPath = null;

        // 2. Upload file avatar baru terlebih dahulu
        if ($request->hasFile('profile_photo')) {
            // Simpan ke disk public di folder 'profiles'
            $newPhotoPath = $request->file('profile_photo')->store('profiles', 'public');
        }

        // 3. Update database
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->location = $request->location;

        if ($newPhotoPath) {
            $user->profile_photo = $newPhotoPath;
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        // 4. Hapus avatar lama dari storage setelah file baru sukses disimpan di database
        if ($newPhotoPath && $oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        // 5. Response JSON terstruktur dan konsisten
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'location' => $user->location,
                'profile_photo_url' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
            ]
        ]);
    }
}
