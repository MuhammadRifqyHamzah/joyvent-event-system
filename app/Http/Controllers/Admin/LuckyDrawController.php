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
 
    public function index(Request $request)
    {
        // Only load events configured with has_lucky_draw = 1
        $events = Event::where('has_lucky_draw', 1)
            ->orderBy('name', 'asc')
            ->get();
            
        $eventId = $request->query('event_id');
        
        $event = null;
        $winners = collect();
        $candidates = collect();
 
        if ($eventId) {
            $event = Event::findOrFail($eventId);
            
            // Get already won registrants to prevent duplicate winning
            $winnerRegistrationIds = LuckyDrawWinner::where('event_id', $eventId)
                ->pluck('registration_id');
                
            // Load all winners for history logs
            $winners = LuckyDrawWinner::with('registration.user')
                ->where('event_id', $eventId)
                ->orderBy('won_at', 'desc')
                ->get();
                
            // Eligible candidates must be checked-in and have NOT won yet
            $candidates = Registration::with('user')
                ->where('event_id', $eventId)
                ->where('is_checked_in', 1)
                ->whereNotIn('id', $winnerRegistrationIds)
                ->get();
        }
 
        return view('admin.luckydraw.index', compact(
            'events',
            'eventId',
            'event',
            'winners',
            'candidates'
        ));
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
