<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Registration;
use App\Models\LuckyDrawWinner;

class LuckyDrawController extends Controller
{
    public function draw(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'prize_name' => 'required|string'
        ]);

        // Ambil participant yang hadir
        $participants = Registration::where('event_id', $request->event_id)
            ->where('is_checked_in', true)
            ->get();

        if ($participants->count() == 0) {

            return response()->json([
                'message' => 'Tidak ada participant yang hadir'
            ], 404);
        }

        // Random participant
        $winner = $participants->random();

        // Simpan winner
        $luckyWinner = LuckyDrawWinner::create([
            'event_id' => $request->event_id,
            'registration_id' => $winner->id,
            'prize_name' => $request->prize_name
        ]);

        return response()->json([
            'message' => 'Lucky draw berhasil',
            'winner' => $winner,
            'data' => $luckyWinner
        ]);
    }
}