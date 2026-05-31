<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with('ticketCategories')->where('is_configured', 1)->latest()->get();

        return response()->json([
            'message' => 'List Event',
            'data' => $events
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'capacity' => 'required|integer'
        ]);

        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'capacity' => $request->capacity,

            'has_multiple_ticket' => $request->has_multiple_ticket ?? false,
            'has_seat_layout' => $request->has_seat_layout ?? false,
            'has_certificate' => $request->has_certificate ?? true,
            'has_lucky_draw' => $request->has_lucky_draw ?? false,

            'status' => 'draft'
        ]);

        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with('ticketCategories')->where('is_configured', 1)->findOrFail($id);

        return response()->json([
            'message' => 'Detail Event',
            'data' => $event
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        $event->update($request->all());

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
}