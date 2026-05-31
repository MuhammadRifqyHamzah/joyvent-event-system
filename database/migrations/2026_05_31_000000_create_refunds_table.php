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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            
            // Relasi Registrasi
            $table->foreignId('registration_id')
                ->constrained()
                ->onDelete('cascade');
            
            // Alasan Refund
            $table->text('reason');
            
            // Catatan Tambahan
            $table->text('additional_notes')->nullable();
            
            // Status Refund
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
