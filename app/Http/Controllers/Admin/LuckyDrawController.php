<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;
use App\Models\LuckyDrawWinner;
 
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
            'event_id' => 'required|exists:events,id',
            'registration_id' => 'required|exists:registrations,id',
            'prize_name' => 'required|string|max:255',
        ]);
 
        // Create the winner entry
        $winner = LuckyDrawWinner::create([
            'event_id' => $request->event_id,
            'registration_id' => $request->registration_id,
            'prize_name' => $request->prize_name,
            'won_at' => now(),
        ]);
 
        // If request expects JSON (from our neon JS Slot Machine), return API payload
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🎉 Pemenang berhasil tercatat!',
                'data' => $winner->load('registration.user')
            ]);
        }
 
        return redirect()
            ->back()
            ->with('success', "🎉 Selamat! Pemenang undian berhasil disimpan!");
    }
 
    /*
    |--------------------------------------------------------------------------
    | RESET / DELETE A WINNER ENTRY
    |--------------------------------------------------------------------------
    */
 
    public function destroy($id)
    {
        $winner = LuckyDrawWinner::findOrFail($id);
        $winner->delete();
 
        return redirect()
            ->back()
            ->with('success', '🗑 Data pemenang undian berhasil di-reset!');
    }
}
