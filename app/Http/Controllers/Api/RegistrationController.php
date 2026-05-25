<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registration;

class RegistrationController extends Controller
{
    public function index()
    {
        $registrations = Registration::with([
            'user',
            'event',
            'ticketCategory'
        ])->latest()->get();

        return response()->json([
            'message' => 'List Registrations',
            'data' => $registrations
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'ticket_category_id' => 'required|exists:ticket_categories,id'
        ]);

        $registration = Registration::create([
            'user_id' => auth()->id(),
            'event_id' => $request->event_id,
            'ticket_category_id' => $request->ticket_category_id,

            'registration_code' => 'REG-' . strtoupper(uniqid()),

            'payment_status' => 'pending',
            'registration_status' => 'pending',

            'qr_code' => 'QR-' . strtoupper(uniqid()),

            'is_checked_in' => false
        ]);

        return response()->json([
            'message' => 'Registration created successfully',
            'data' => $registration
        ]);
    }

    public function show(string $id)
    {
        $registration = Registration::with([
            'user',
            'event',
            'ticketCategory'
        ])->findOrFail($id);

        return response()->json([
            'message' => 'Detail Registration',
            'data' => $registration
        ]);
    }

    public function update(Request $request, string $id)
    {
        $registration = Registration::findOrFail($id);

        $registration->update($request->all());

        return response()->json([
            'message' => 'Registration updated successfully',
            'data' => $registration
        ]);
    }

    public function destroy(string $id)
    {
        $registration = Registration::findOrFail($id);

        $registration->delete();

        return response()->json([
            'message' => 'Registration deleted successfully'
        ]);
    }
}