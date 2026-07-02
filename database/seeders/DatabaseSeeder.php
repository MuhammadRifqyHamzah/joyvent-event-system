<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // In production, only run the safe bootstrapping seeder (AdminSeeder)
        if (app()->environment('production')) {
            $this->call([
                AdminSeeder::class,
            ]);
            return;
        }

        // Panggil AdminSeeder terlebih dahulu untuk memastikan akun admin default terbentuk
        // Setelah itu baru jalankan EventSeeder untuk seed data demo/event
        $this->call([
            AdminSeeder::class,
            EventSeeder::class,
            RefundSeeder::class,
        ]);
    }
}
