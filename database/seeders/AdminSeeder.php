<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat/pastikan akun admin default permanen ada di database
        // 2. Gunakan firstOrCreate agar tidak terduplikasi setiap kali Seeder dijalankan
        // 3. Jika akun admin sudah ada:
        //    - Jangan membuat akun admin baru.
        //    - Jangan menghapus akun admin lama.
        //    - Jangan mengubah password secara otomatis.
        User::firstOrCreate(
            ['email' => 'admin@joyvent.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => Hash::make('Admin@123'),
                'phone' => '081234567890',
                'email_verified_at' => now(),
            ]
        );
    }
}
