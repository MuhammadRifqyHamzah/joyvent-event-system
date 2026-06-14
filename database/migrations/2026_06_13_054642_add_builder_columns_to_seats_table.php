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
        Schema::table('seats', function (Blueprint $table) {
            $table->foreignId('ticket_category_id')
                ->nullable()
                ->after('event_id')
                ->constrained('ticket_categories')
                ->nullOnDelete();
            $table->integer('x')->nullable()->after('column');
            $table->integer('y')->nullable()->after('x');
            $table->integer('rotation')->default(0)->after('y');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropForeign(['ticket_category_id']);
            $table->dropColumn(['ticket_category_id', 'x', 'y', 'rotation']);
        });
    }
};
