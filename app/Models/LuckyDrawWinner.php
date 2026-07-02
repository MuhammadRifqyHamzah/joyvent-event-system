<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LuckyDrawWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'registration_id',
        'event_prize_id',
        'prize_name',
        'draw_number',
        'won_at'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function eventPrize()
    {
        return $this->belongsTo(EventPrize::class, 'event_prize_id');
    }
}