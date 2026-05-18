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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            // Participant
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Event
            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');

            // Ticket Category
            $table->foreignId('ticket_category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Seat (optional)
            $table->string('seat_number')->nullable();

            // QR Ticket
            $table->string('qr_code')->unique();

            // Check-in Status
            $table->boolean('is_checked_in')->default(false);

            // Waktu Check-in
            $table->timestamp('checked_in_at')->nullable();

            // Status Registrasi
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled'
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};