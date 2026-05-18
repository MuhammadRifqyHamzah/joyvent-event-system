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
        $seats = Seat::where('event_id', $eventId)
            ->orderBy('seat_number')
            ->get();

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