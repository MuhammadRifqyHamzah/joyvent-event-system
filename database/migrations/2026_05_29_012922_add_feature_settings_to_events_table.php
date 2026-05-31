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
        Schema::table('events', function (Blueprint $table) {
            // Certificate Setup
            $table->string('certificate_title')->nullable();
            $table->string('organizer_name')->nullable();
            $table->string('certificate_template')->nullable();
            $table->string('signature_image')->nullable();

            // Seat Layout Config (JSON)
            $table->text('seat_layout')->nullable();

            // Lucky Draw Setup
            $table->string('prize_name')->nullable();
            $table->text('prize_description')->nullable();
            $table->integer('winner_count')->nullable();

            // Configuration Completion Status
            $table->boolean('is_configured')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_title',
                'organizer_name',
                'certificate_template',
                'signature_image',
                'seat_layout',
                'prize_name',
                'prize_description',
                'winner_count',
                'is_configured'
            ]);
        });
    }
};
