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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            // Relasi Registrasi
            $table->foreignId('registration_id')
                ->constrained()
                ->onDelete('cascade');

            // Kode Sertifikat
            $table->string('certificate_code')->unique();

            // File Sertifikat
            $table->string('certificate_file')->nullable();

            // Status Validasi
            $table->boolean('is_valid')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};