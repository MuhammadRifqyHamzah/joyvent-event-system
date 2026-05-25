<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TicketCategory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',
        'description',
        'location',

        'start_date',
        'end_date',

        'start_time',
        'end_time',

        'capacity',
        'status',

        'has_certificate',
        'has_seat_layout',
        'has_lucky_draw',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }
}