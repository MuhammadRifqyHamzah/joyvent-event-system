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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();

            // Relasi ke Event
            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');

            // Nomor Seat
            $table->string('seat_number');

            // Posisi Seat (optional)
            $table->integer('row')->nullable();
            $table->integer('column')->nullable();

            // Status Seat
            $table->enum('status', [
                'available',
                'booked'
            ])->default('available');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};