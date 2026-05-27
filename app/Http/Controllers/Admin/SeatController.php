<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seat;
use App\Models\Event;
use App\Models\Registration;
 
class SeatController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DISPLAY SEAT LAYOUT EDITOR
    |--------------------------------------------------------------------------
    */
 
    public function index(Request $request)
    {
        $events = Event::orderBy('name', 'asc')->get();
        $eventId = $request->query('event_id');
        
        $event = null;
        $groupedSeats = collect();
        $registrations = collect();
 
        if ($eventId) {
            $event = Event::findOrFail($eventId);
            
            // Get seats sorted by row and column
            $seats = Seat::where('event_id', $eventId)
                ->orderBy('row', 'asc')
                ->orderBy('column', 'asc')
                ->get();
 
            $groupedSeats = $seats->groupBy('row');
            
            // Fetch registrations with seat numbers assigned
            $registrations = Registration::with('user')
                ->where('event_id', $eventId)
                ->whereNotNull('seat_number')
                ->get()
                ->keyBy('seat_number');
        }
 
        return view('admin.seats.index', compact(
            'events',
            'eventId',
            'event',
            'groupedSeats',
            'registrations'
        ));
    }
 
    /*
    |--------------------------------------------------------------------------
    | AUTOMATED MASS SEAT GENERATOR
    |--------------------------------------------------------------------------
    */
 
    public function generate(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'rows' => 'required|string',
            'seats_per_row' => 'required|integer|min:1|max:30',
        ]);
 
        $eventId = $request->event_id;
        $rowsInput = $request->rows;
        $seatsPerRow = $request->seats_per_row;
 
        // Parse rows and sanitize
        $rows = array_filter(
            array_map('trim', explode(',', strtoupper($rowsInput))),
            'strlen'
        );
 
        if (empty($rows)) {
            return redirect()->back()->withErrors(['rows' => 'Baris kursi tidak boleh kosong!']);
        }
 
        $totalGenerated = 0;
 
        foreach ($rows as $row) {
            for ($col = 1; $col <= $seatsPerRow; $col++) {
                $seatNumber = $row . $col;
                
                // Keep existing seats if they exist to prevent losing booking data
                $seat = Seat::firstOrCreate(
                    [
                        'event_id' => $eventId,
                        'seat_number' => $seatNumber,
                    ],
                    [
                        'row' => $row,
                        'column' => $col,
                        'status' => 'available',
                    ]
                );
                
                if ($seat->wasRecentlyCreated) {
                    $totalGenerated++;
                }
            }
        }
 
        return redirect()
            ->back()
            ->with('success', "🎉 Berhasil men-generate {$totalGenerated} kursi baru untuk event ini! 🪑");
    }
 
    /*
    |--------------------------------------------------------------------------
    | TOGGLE SEAT STATUS (BLOCK / UNBLOCK)
    |--------------------------------------------------------------------------
    */
 
    public function toggleStatus(Seat $seat)
    {
        // Booked seats cannot be blocked
        if ($seat->status === 'booked') {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Kursi yang sudah dipesan oleh peserta tidak dapat diblokir! 🚫']);
        }
 
        if ($seat->status === 'available') {
            $seat->status = 'blocked';
            $message = "Kursi {$seat->seat_number} berhasil diblokir! 🔴";
        } else {
            $seat->status = 'available';
            $message = "Blokir kursi {$seat->seat_number} berhasil dibuka! 🟢";
        }
 
        $seat->save();
 
        return redirect()
            ->back()
            ->with('success', $message);
    }
}
