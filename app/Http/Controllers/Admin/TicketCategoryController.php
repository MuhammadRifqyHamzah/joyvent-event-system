<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\TicketCategory;
 
class TicketCategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TICKET CATEGORY PAGE
    |--------------------------------------------------------------------------
    */
 
    public function index(Event $event)
    {
        $tickets = $event->ticketCategories;
 
        return view('admin.tickets.index', compact('event', 'tickets'));
    }
 
    /*
    |--------------------------------------------------------------------------
    | STORE TICKET CATEGORY
    |--------------------------------------------------------------------------
    */
 
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:0',
            'description' => 'nullable',
        ]);
 
        $ticket = $event->ticketCategories()->create([
            'name' => $request->name,
            'price' => $request->price,
            'quota' => $request->quota,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket category berhasil dibuat! 🎟️',
                'ticket' => $ticket,
            ]);
        }
 
        return redirect()
            ->route('admin.tickets.index', $event->id)
            ->with('success', 'Ticket category berhasil dibuat! 🎟️');
    }
 
    /*
    |--------------------------------------------------------------------------
    | DESTROY TICKET CATEGORY
    |--------------------------------------------------------------------------
    */
 
    public function destroy(TicketCategory $ticket)
    {
        $eventId = $ticket->event_id;
        $ticket->delete();
 
        return redirect()
            ->route('admin.tickets.index', $eventId)
            ->with('success', 'Ticket category berhasil dihapus! 🗑️');
    }
}