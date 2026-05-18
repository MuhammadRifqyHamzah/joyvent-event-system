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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Informasi Event
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');

            // Waktu Event
            $table->date('start_date');
            $table->date('end_date');

            // Kapasitas Peserta
            $table->integer('capacity');

            // Konfigurasi Event
            $table->boolean('has_multiple_ticket')->default(false);
            $table->boolean('has_seat_layout')->default(false);
            $table->boolean('has_certificate')->default(true);
            $table->boolean('has_lucky_draw')->default(false);

            // Status Event
            $table->enum('status', [
                'draft',
                'open',
                'closed',
                'finished'
            ])->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};