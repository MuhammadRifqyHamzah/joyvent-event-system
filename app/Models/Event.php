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
        'category',
        'location',
        'google_maps_url',

        'start_date',
        'end_date',

        'start_time',
        'end_time',

        'capacity', // Deprecated: use ticket categories quota sum instead
        'status',

        'has_certificate',
        'has_seat_layout',
        'has_lucky_draw',

        'certificate_title',
        'organizer_name',
        'certificate_template',
        'signature_image',
        'seat_layout',
        'prize_name',
        'prize_description',
        'winner_count',
        'is_configured',

    ];

    protected $appends = [
        'banner_image',
        'calculated_status',
    ];

    /**
     * Get the dynamically calculated status based on date/time.
     *
     * @return string
     */
    public function getCalculatedStatusAttribute()
    {
        $now = now();
        $startDate = \Carbon\Carbon::parse($this->start_date . ' ' . $this->start_time);
        $endDate = \Carbon\Carbon::parse($this->end_date . ' ' . $this->end_time);

        if ($now->greaterThan($endDate)) {
            return 'finished';
        } elseif ($now->lessThan($startDate)) {
            return 'upcoming';
        } else {
            return 'ongoing';
        }
    }

    /**
     * Get the dynamically calculated capacity from ticket categories quota sum.
     * Overrides the deprecated 'capacity' database column.
     *
     * @return int
     */
    public function getCapacityAttribute()
    {
        if ($this->relationLoaded('ticketCategories')) {
            return $this->ticketCategories->sum('quota');
        }
        return (int) ($this->ticketCategories()->sum('quota') ?? 0);
    }

    /**
     * Get the original capacity limit defined in the database.
     *
     * @return int
     */
    public function getEventCapacityLimit()
    {
        return (int) ($this->attributes['capacity'] ?? $this->getRawOriginal('capacity') ?? 0);
    }

    public function getBannerImageAttribute()
    {
        $bannerDir = public_path('storage/banners');
        if (\Illuminate\Support\Facades\File::exists($bannerDir)) {
            $files = \Illuminate\Support\Facades\File::files($bannerDir);
            foreach ($files as $f) {
                if (str_starts_with($f->getFilename(), 'banner_image_' . $this->id . '.')) {
                    return asset('storage/banners/' . $f->getFilename());
                }
            }
        }
        return null;
    }


    public function scopeActive($query)
    {
        $nowString = now()->toDateTimeString();
        return $query->whereRaw("CONCAT(end_date, ' ', end_time) >= ?", [$nowString]);
    }

    public function scopeFinished($query)
    {
        $nowString = now()->toDateTimeString();
        return $query->whereRaw("CONCAT(end_date, ' ', end_time) < ?", [$nowString]);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function eventPrizes()
    {
        return $this->hasMany(EventPrize::class)->orderBy('draw_order', 'asc');
    }
}