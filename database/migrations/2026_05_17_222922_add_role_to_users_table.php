<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Role User
            $table->enum('role', [
                'admin',
                'participant'
            ])->default('participant');

            // Nomor HP
            $table->string('phone')->nullable();

            // Foto Profile
            $table->string('profile_photo')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'role',
                'phone',
                'profile_photo'
            ]);

        });
    }
};