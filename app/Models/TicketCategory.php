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

    protected $appends = [
        'sold',
        'remaining'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'ticket_category_id');
    }

    public function getSoldAttribute()
    {
        return $this->registrations()
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    public function getRemainingAttribute()
    {
        return max(0, $this->quota - $this->getSoldAttribute());
    }
}