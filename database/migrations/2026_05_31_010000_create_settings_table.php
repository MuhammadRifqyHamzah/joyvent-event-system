<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default organizer settings
        DB::table('settings')->insert([
            [
                'key' => 'organizer_name',
                'value' => 'JoyVent Organizer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'organizer_email',
                'value' => 'admin@joyvent.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'organizer_phone',
                'value' => '08123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
