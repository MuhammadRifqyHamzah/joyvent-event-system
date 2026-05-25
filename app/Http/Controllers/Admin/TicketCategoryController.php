<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

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
}