<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_category_id',
        'seat_number',
        'row',
        'column',
        'status',
        'x',
        'y',
        'rotation'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketCategory()
    {
        return $this->belongsTo(TicketCategory::class);
    }
}