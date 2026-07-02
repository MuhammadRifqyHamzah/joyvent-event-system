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
            $table->string('payment_proof')->nullable()->after('payment_notes');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
            $table->integer('payment_proof_size')->nullable()->after('payment_proof_uploaded_at');
            $table->string('payment_rejection_reason')->nullable()->after('payment_proof_size');
            $table->unsignedBigInteger('payment_verified_by')->nullable()->after('payment_rejection_reason');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_verified_by');
            $table->unsignedBigInteger('payment_rejected_by')->nullable()->after('payment_verified_at');
            $table->timestamp('payment_rejected_at')->nullable()->after('payment_rejected_by');

            $table->foreign('payment_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('payment_rejected_by')->references('id')->on('users')->onDelete('set null');
        });

        // Seed default manual payment settings using updateOrInsert (idempotent)
        $settings = [
            'payment_qris_image' => null,
            'payment_bank_name' => null,
            'payment_bank_account_number' => null,
            'payment_bank_account_name' => null,
            'payment_instruction' => null,
            'payment_contact' => null,
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['payment_verified_by']);
            $table->dropForeign(['payment_rejected_by']);
            
            $table->dropColumn([
                'payment_proof',
                'payment_proof_uploaded_at',
                'payment_proof_size',
                'payment_rejection_reason',
                'payment_verified_by',
                'payment_verified_at',
                'payment_rejected_by',
                'payment_rejected_at'
            ]);
        });

        DB::table('settings')->whereIn('key', [
            'payment_qris_image',
            'payment_bank_name',
            'payment_bank_account_number',
            'payment_bank_account_name',
            'payment_instruction',
            'payment_contact'
        ])->delete();
    }
};
