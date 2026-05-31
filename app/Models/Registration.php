<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_category_id',
        'seat_number',
        'qr_code',
        'is_checked_in',
        'checked_in_at',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketCategory()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}