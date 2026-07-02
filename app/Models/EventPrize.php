<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventPrize extends Model
{
    use HasFactory;

    protected $table = 'event_prizes';

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'image',
        'winner_count',
        'drawn_count',
        'status',
        'draw_order',
    ];

    protected $appends = [
        'remaining_count',
    ];

    public function getRemainingCountAttribute(): int
    {
        return max(0, $this->winner_count - $this->drawn_count);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function winners()
    {
        return $this->hasMany(LuckyDrawWinner::class, 'event_prize_id');
    }
}
