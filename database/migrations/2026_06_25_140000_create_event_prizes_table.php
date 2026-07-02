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
        $isMysql = Schema::getConnection()->getDriverName() === 'mysql';

        // 1. Force the existing lucky_draw_winners table to InnoDB to prevent storage engine mismatch (MyISAM/InnoDB)
        if ($isMysql && Schema::hasTable('lucky_draw_winners')) {
            DB::statement('ALTER TABLE lucky_draw_winners ENGINE = InnoDB');
        }

        // 2. Check database state to detect half-migrated condition
        $hasEventPrizesTable = Schema::hasTable('event_prizes');
        $hasEventPrizeIdCol = Schema::hasColumn('lucky_draw_winners', 'event_prize_id');
        $hasDrawNumberCol = Schema::hasColumn('lucky_draw_winners', 'draw_number');

        if ($hasEventPrizesTable && $hasEventPrizeIdCol && $hasDrawNumberCol) {
            // =================================================================
            // RECOVERY MODE (For Half-Migrated Database)
            // =================================================================
            // Do NOT try to create the table or add columns again.
            // Only attach the foreign key constraint and run the idempotent backfill.
            
            Schema::table('lucky_draw_winners', function (Blueprint $table) use ($isMysql) {
                $hasConstraint = false;

                if ($isMysql) {
                    $constraintCheck = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.REFERENTIAL_CONSTRAINTS 
                        WHERE CONSTRAINT_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'lucky_draw_winners' 
                          AND CONSTRAINT_NAME = 'fk_ldw_event_prize'
                    ");
                    $hasConstraint = !empty($constraintCheck);
                }

                if (!$hasConstraint) {
                    $table->foreign('event_prize_id', 'fk_ldw_event_prize')
                        ->references('id')
                        ->on('event_prizes')
                        ->onDelete('set null');
                }
            });

        } else {
            // =================================================================
            // FRESH CREATION MODE (For new local development or automated tests)
            // =================================================================
            
            // Create event_prizes table
            Schema::create('event_prizes', function (Blueprint $table) use ($isMysql) {
                if ($isMysql) {
                    $table->engine = 'InnoDB';
                }
                $table->id();
                $table->foreignId('event_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->integer('winner_count')->default(1);
                $table->integer('drawn_count')->default(0);
                $table->string('status', 30)->default('waiting');
                $table->integer('draw_order')->default(0);
                $table->timestamps();
            });

            // Add columns to lucky_draw_winners
            Schema::table('lucky_draw_winners', function (Blueprint $table) {
                $table->unsignedBigInteger('event_prize_id')->nullable()->after('registration_id');
                $table->integer('draw_number')->nullable()->after('event_prize_id');
            });

            // Add foreign key constraint
            Schema::table('lucky_draw_winners', function (Blueprint $table) {
                $table->foreign('event_prize_id', 'fk_ldw_event_prize')
                    ->references('id')
                    ->on('event_prizes')
                    ->onDelete('set null');
            });
        }

        // =================================================================
        // IDEMPOTENT BACKFILL (Shared step)
        // =================================================================
        $oldEvents = DB::table('events')
            ->where('has_lucky_draw', 1)
            ->whereNotNull('prize_name')
            ->get();

        foreach ($oldEvents as $event) {
            // Check if the prize already exists in event_prizes to prevent duplicate records
            $prizeExists = DB::table('event_prizes')
                ->where('event_id', $event->id)
                ->where('name', $event->prize_name)
                ->exists();

            if ($prizeExists) {
                continue;
            }

            // Find old winners for this event with matching prize_name
            $winners = DB::table('lucky_draw_winners')
                ->where('event_id', $event->id)
                ->where('prize_name', $event->prize_name)
                ->orderBy('won_at', 'asc')
                ->get();

            $drawnCount = $winners->count();
            $winnerCount = $event->winner_count ?? 1;
            $status = ($drawnCount >= $winnerCount) ? 'completed' : 'waiting';

            // Insert prize record into event_prizes
            $prizeId = DB::table('event_prizes')->insertGetId([
                'event_id' => $event->id,
                'name' => $event->prize_name,
                'description' => $event->prize_description,
                'image' => null,
                'winner_count' => $winnerCount,
                'drawn_count' => $drawnCount,
                'status' => $status,
                'draw_order' => 0,
                'created_at' => $event->created_at ?? now(),
                'updated_at' => $event->updated_at ?? now(),
            ]);

            // Map old winner rows to the new event_prize_id and set draw_number
            foreach ($winners as $index => $winner) {
                DB::table('lucky_draw_winners')
                    ->where('id', $winner->id)
                    ->update([
                        'event_prize_id' => $prizeId,
                        'draw_number' => $index + 1
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isMysql = Schema::getConnection()->getDriverName() === 'mysql';

        Schema::table('lucky_draw_winners', function (Blueprint $table) use ($isMysql) {
            if ($isMysql) {
                $table->dropForeign('fk_ldw_event_prize');
            }
            $table->dropColumn(['event_prize_id', 'draw_number']);
        });

        Schema::dropIfExists('event_prizes');
    }
};
