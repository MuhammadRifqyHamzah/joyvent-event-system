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
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();

            // Relasi ke Event
            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');

            // Informasi Ticket
            $table->string('name');

            // Harga Ticket
            $table->decimal('price', 12, 2)->default(0);

            // Kuota Ticket
            $table->integer('quota');

            // Deskripsi / Benefit
            $table->text('description')->nullable();

            // Status Ticket
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};