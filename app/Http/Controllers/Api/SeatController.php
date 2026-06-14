<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Seat;

class SeatController extends Controller
{
    // GET LIST SEATS
    public function index($eventId)
    {
        $seats = Seat::with('ticketCategory')
            ->where('event_id', $eventId)
            ->orderBy('seat_number')
            ->get()
            ->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'seat_number' => $seat->seat_number,
                    'ticket_category_id' => $seat->ticket_category_id,
                    'category' => $seat->ticketCategory ? $seat->ticketCategory->name : null,
                    'status' => $seat->status,
                    'x' => $seat->x,
                    'y' => $seat->y,
                    'rotation' => $seat->rotation,
                ];
            });

        return response()->json([
            'message' => 'List Seats',
            'data' => $seats
        ]);
    }

    // BOOK SEAT
    public function bookSeat(Request $request)
    {
        $request->validate([
            'seat_id' => 'required|exists:seats,id'
        ]);

        $seat = Seat::findOrFail($request->seat_id);

        // cek seat sudah dibooking atau belum
        if ($seat->status == 'booked') {

            return response()->json([
                'message' => 'Seat sudah dibooking'
            ], 400);
        }

        // update status seat
        $seat->update([
            'status' => 'booked'
        ]);

        return response()->json([
            'message' => 'Seat berhasil dibooking',
            'data' => $seat
        ]);
    }
}