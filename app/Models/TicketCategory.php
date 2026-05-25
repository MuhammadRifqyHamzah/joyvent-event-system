<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quota',
        'description',
        'is_active'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}