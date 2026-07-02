<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Registration;
use App\Models\LuckyDrawWinner;
use App\Models\EventPrize;

class LuckyDrawController extends Controller
{
    public function draw(Request $request)
    {
        $request->validate([
            'event_prize_id' => 'required|exists:event_prizes,id',
            'lucky_draw_mode' => 'nullable|string|in:checked_in_only,all_participants',
        ]);

        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Admin role required.'
            ], 403);
        }

        $mode = $request->input('lucky_draw_mode', 'checked_in_only');

        try {
            $result = DB::transaction(function () use ($request, $mode) {
                // 1. Pessimistic lock on the prize row
                $prize = EventPrize::where('id', $request->event_prize_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2. Validate drawing lock
                if ($prize->status === 'drawing') {
                    throw new \Exception('CONFLICT_LOCK');
                }

                // 3. Validate status and quota
                if ($prize->status === 'completed' || $prize->drawn_count >= $prize->winner_count) {
                    throw new \Exception('OUT_OF_QUOTA');
                }

                if ($prize->status === 'draft') {
                    throw new \Exception('DRAFT_STATUS');
                }

                // Lock the drawing state
                $prize->status = 'drawing';
                $prize->save();

                // 4. Fetch unique User IDs who already won at this event
                $winnerUserIds = DB::table('lucky_draw_winners')
                    ->join('registrations', 'lucky_draw_winners.registration_id', '=', 'registrations.id')
                    ->where('lucky_draw_winners.event_id', $prize->event_id)
                    ->pluck('registrations.user_id');

                $query = Registration::where('event_id', $prize->event_id)
                    ->where('registration_status', 'active')
                    ->where('payment_status', 'paid')
                    ->where('status', '!=', 'cancelled')
                    ->whereNotIn('user_id', $winnerUserIds)
                    ->where(function ($q) {
                        $q->whereDoesntHave('refund')
                            ->orWhereHas('refund', function ($qr) {
                                $qr->whereNotIn('status', ['pending', 'approved']);
                            });
                    });

                if ($mode === 'checked_in_only') {
                    $query->where('is_checked_in', 1);
                }

                $winner = $query->inRandomOrder()->first();

                if (!$winner) {
                    // Revert drawing lock
                    $prize->status = 'waiting';
                    $prize->save();
                    throw new \Exception('NO_CANDIDATES');
                }

                // 5. Update drawn_count and status
                $prize->drawn_count += 1;
                if ($prize->drawn_count >= $prize->winner_count) {
                    $prize->status = 'completed';
                } else {
                    $prize->status = 'waiting';
                }
                $prize->save();

                // 6. Create the winner record
                $luckyWinner = LuckyDrawWinner::create([
                    'event_id' => $prize->event_id,
                    'registration_id' => $winner->id,
                    'event_prize_id' => $prize->id,
                    'prize_name' => $prize->name,
                    'draw_number' => $prize->drawn_count,
                    'won_at' => now(),
                ]);

                return $luckyWinner;
            });

            return response()->json([
                'success' => true,
                'message' => 'Lucky draw berhasil',
                'winner' => $result->registration->user,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $code = 422;

            if ($msg === 'CONFLICT_LOCK') {
                $msg = 'Proses undian untuk hadiah ini sedang berlangsung.';
                $code = 409;
            } elseif ($msg === 'OUT_OF_QUOTA') {
                $msg = 'Kuota undian untuk hadiah ini sudah habis.';
                $code = 422;
            } elseif ($msg === 'DRAFT_STATUS') {
                $msg = 'Hadiah ini masih berstatus draft dan belum bisa diundi.';
                $code = 422;
            } elseif ($msg === 'NO_CANDIDATES') {
                $msg = 'Tidak ada kandidat peserta yang memenuhi syarat undian.';
                $code = 404;
            } else {
                $msg = 'Terjadi kesalahan sistem: ' . $msg;
                $code = 500;
            }

            return response()->json([
                'success' => false,
                'message' => $msg
            ], $code);
        }
    }
 
    public function getWinners($eventId)
    {
        $winners = LuckyDrawWinner::with('registration.user')
            ->where('event_id', $eventId)
            ->orderBy('won_at', 'desc')
            ->get();
 
        return response()->json([
            'success' => true,
            'data' => $winners
        ]);
    }

    public function getMyWins(Request $request)
    {
        $userId = $request->user()->id;

        $wins = LuckyDrawWinner::whereHas('registration', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('event')
        ->orderBy('won_at', 'desc')
        ->get()
        ->map(function ($win) {
            return [
                'id' => $win->id,
                'event_name' => $win->event->name ?? 'JoyVent Event',
                'event_id' => $win->event_id,
                'prize_name' => $win->prize_name,
                'won_at' => $win->won_at ? \Carbon\Carbon::parse($win->won_at)->format('Y-m-d H:i:s') : ($win->created_at ? \Carbon\Carbon::parse($win->created_at)->format('Y-m-d H:i:s') : null),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $wins
        ]);
    }
}