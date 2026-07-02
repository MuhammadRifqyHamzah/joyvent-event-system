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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('source_key')->nullable()->unique(); // Anti-duplikasi
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type'); // 'payment' | 'refund' | 'event' | 'certificate' | 'lucky_draw' | 'system'
            $table->string('action_url')->nullable(); // Contoh: 'ticket?registrationId=13'
            $table->json('data')->nullable(); // Metadata tambahan
            $table->boolean('is_read')->default(false);
            $table->softDeletes(); // Dukungan soft delete
            $table->timestamps();

            // Indexing untuk optimalisasi query
            $table->index('user_id');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
