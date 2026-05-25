<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | EVENTS PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $events = Event::latest()->get();

        return view('admin.events.index', compact('events'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE EVENT PAGE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.events.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE EVENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'name' => 'required|max:255',
            'description' => 'required',

            'location' => 'required',

            'start_date' => 'required|date',
            'end_date' => 'required|date',

            'start_time' => 'required',
            'end_time' => 'required',

            'capacity' => 'required|integer',

            'status' => 'required',

        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE EVENT
        |--------------------------------------------------------------------------
        */

        $event = Event::create([

            'name' => $request->name,
            'description' => $request->description,

            'location' => $request->location,

            'start_date' => $request->start_date,
            'end_date' => $request->end_date,

            'start_time' => $request->start_time,
            'end_time' => $request->end_time,

            'capacity' => $request->capacity,
            'status' => $request->status,

            'has_certificate' => $request->has_certificate ? 1 : 0,
            'has_seat_layout' => $request->has_seat_layout ? 1 : 0,
            'has_lucky_draw' => $request->has_lucky_draw ? 1 : 0,

        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECT TO TICKET CATEGORY
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.tickets.index', $event->id)
            ->with('success', 'Event berhasil dibuat! 🎉');
    }
}