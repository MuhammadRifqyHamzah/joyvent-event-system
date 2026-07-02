<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;
use App\Models\LuckyDrawWinner;
use App\Models\EventPrize;
use Illuminate\Support\Facades\DB;
 
class LuckyDrawController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DISPLAY LUCKY DRAW RAFFLE SCREEN
    |--------------------------------------------------------------------------
    */
 
    public function index(Event $event, Request $request)
    {
        return redirect()->route('admin.events.show', ['event' => $event->id, 'tab' => 'lucky_draw']);
    }
 
    /*
    |--------------------------------------------------------------------------
    | RECORD RAFFLE WINNER (AJAX / POST)
    |--------------------------------------------------------------------------
    */
 
    public function draw(Request $request)
    {
        $request->validate([
            'event_prize_id' => 'required|exists:event_prizes,id',
            'lucky_draw_mode' => 'nullable|string|in:checked_in_only,all_participants',
            'exclude_user_ids' => 'nullable|array',
            'exclude_user_ids.*' => 'integer',
        ]);

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
                    ->pluck('registrations.user_id')
                    ->toArray();

                // Validate and filter exclude_user_ids from request
                $excludeUserIds = $request->input('exclude_user_ids', []);
                $validExcludeUserIds = [];
                if (!empty($excludeUserIds)) {
                    $validExcludeUserIds = DB::table('registrations')
                        ->where('event_id', $prize->event_id)
                        ->whereIn('user_id', $excludeUserIds)
                        ->where('registration_status', 'active')
                        ->where('payment_status', 'paid')
                        ->where('status', '!=', 'cancelled')
                        ->pluck('user_id')
                        ->toArray();
                }

                $allExcludedUserIds = array_unique(array_merge($winnerUserIds, $validExcludeUserIds));

                $query = Registration::where('event_id', $prize->event_id)
                    ->where('registration_status', 'active')
                    ->where('payment_status', 'paid')
                    ->where('status', '!=', 'cancelled')
                    ->whereNotIn('user_id', $allExcludedUserIds)
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

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '🎉 Pemenang berhasil terpilih secara acak!',
                    'data' => $result->load('registration.user')
                ]);
            }

            return redirect()
                ->back()
                ->with('success', "🎉 Selamat! Pemenang undian berhasil disimpan!");

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

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ], $code);
            }

            return redirect()
                ->back()
                ->with('error', $msg);
        }
    }
 
    /*
    |--------------------------------------------------------------------------
    | RESET / DELETE A WINNER ENTRY
    |--------------------------------------------------------------------------
    */
 
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $winner = LuckyDrawWinner::findOrFail($id);
            if ($winner->event_prize_id) {
                $prize = EventPrize::where('id', $winner->event_prize_id)->lockForUpdate()->first();
                if ($prize) {
                    $prize->drawn_count = max(0, $prize->drawn_count - 1);
                    if ($prize->status === 'completed' && $prize->drawn_count < $prize->winner_count) {
                        $prize->status = 'waiting';
                    }
                    $prize->save();
                }
            }
            $winner->delete();
        });
 
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🗑 Data pemenang undian berhasil di-reset!'
            ]);
        }

        return redirect()
            ->back()
            ->with('success', '🗑 Data pemenang undian berhasil di-reset!');
    }
}
