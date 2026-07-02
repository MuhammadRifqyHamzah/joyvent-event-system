<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('registration_status', 20)->default('active')->after('status');
            $table->string('payment_status', 30)->default('pending')->after('registration_status');
            $table->string('payment_method', 50)->nullable()->after('payment_status');
            $table->string('payment_reference', 100)->nullable()->after('payment_method')->index();
            $table->decimal('payment_amount', 15, 2)->nullable()->after('payment_reference');
            $table->string('payment_gateway', 50)->nullable()->after('payment_amount');
            $table->text('payment_notes')->nullable()->after('payment_gateway');
            $table->timestamp('paid_at')->nullable()->after('payment_notes');
            $table->timestamp('payment_expired_at')->nullable()->after('paid_at');
        });

        // Jalankan Data Migrasi (Backfill) untuk data lama agar tetap konsisten
        DB::table('registrations')->where('status', 'confirmed')->update([
            'registration_status' => 'active',
            'payment_status' => 'paid',
            'paid_at' => DB::raw('updated_at'),
            'payment_gateway' => 'manual_transfer',
        ]);

        DB::table('registrations')->where('status', 'pending')->update([
            'registration_status' => 'active',
            'payment_status' => 'pending',
        ]);

        DB::table('registrations')->where('status', 'cancelled')->update([
            'registration_status' => 'cancelled',
            'payment_status' => 'failed',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex(['payment_reference']);
            $table->dropColumn([
                'registration_status',
                'payment_status',
                'payment_method',
                'payment_reference',
                'payment_amount',
                'payment_gateway',
                'payment_notes',
                'paid_at',
                'payment_expired_at'
            ]);
        });
    }
};
