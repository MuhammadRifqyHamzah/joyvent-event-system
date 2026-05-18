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
        Schema::create('lucky_draw_winners', function (Blueprint $table) {
            $table->id();

            // Relasi Event
            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');

            // Relasi Registration
            $table->foreignId('registration_id')
                ->constrained()
                ->onDelete('cascade');

            // Hadiah
            $table->string('prize_name')->nullable();

            // Waktu Menang
            $table->timestamp('won_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lucky_draw_winners');
    }
};