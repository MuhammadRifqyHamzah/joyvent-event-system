<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketCategory;

class TicketCategoryController extends Controller
{
    public function index()
    {
        $tickets = TicketCategory::with('event')->latest()->get();

        return response()->json([
            'message' => 'List Ticket Categories',
            'data' => $tickets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required',
            'price' => 'required',
            'quota' => 'required|integer'
        ]);

        $ticket = TicketCategory::create([
            'event_id' => $request->event_id,
            'name' => $request->name,
            'price' => $request->price,
            'quota' => $request->quota,
            'description' => $request->description,
            'is_active' => true
        ]);

        return response()->json([
            'message' => 'Ticket category created successfully',
            'data' => $ticket
        ]);
    }

    public function show(string $id)
    {
        $ticket = TicketCategory::with('event')->findOrFail($id);

        return response()->json([
            'message' => 'Detail Ticket Category',
            'data' => $ticket
        ]);
    }

    public function update(Request $request, string $id)
    {
        $ticket = TicketCategory::findOrFail($id);

        $ticket->update($request->all());

        return response()->json([
            'message' => 'Ticket category updated successfully',
            'data' => $ticket
        ]);
    }

    public function destroy(string $id)
    {
        $ticket = TicketCategory::findOrFail($id);

        $ticket->delete();

        return response()->json([
            'message' => 'Ticket category deleted successfully'
        ]);
    }
}