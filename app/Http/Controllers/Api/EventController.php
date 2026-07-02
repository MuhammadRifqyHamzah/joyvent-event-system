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
    public function index(Request $request)
    {
        $query = Event::with('ticketCategories')->where('is_configured', 1);

        if (!$request->boolean('include_finished')) {
            $query->active();
        }

        $events = $query->latest()->get();

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
            'google_maps_url' => [
                'nullable',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $pattern = '/^(https?:\/\/)?(www\.)?(maps\.google\.com|google\.com\/maps|maps\.app\.goo\.gl)/i';
                    if (!preg_match($pattern, $value)) {
                        $fail('Format URL harus berupa tautan resmi Google Maps.');
                    }
                }
            ],
            'start_date' => 'required',
            'end_date' => 'required',
            'capacity' => 'required|integer'
        ]);

        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'google_maps_url' => $request->google_maps_url,
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
        $request->validate([
            'google_maps_url' => [
                'nullable',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $pattern = '/^(https?:\/\/)?(www\.)?(maps\.google\.com|google\.com\/maps|maps\.app\.goo\.gl)/i';
                    if (!preg_match($pattern, $value)) {
                        $fail('Format URL harus berupa tautan resmi Google Maps.');
                    }
                }
            ]
        ]);

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