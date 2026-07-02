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

}