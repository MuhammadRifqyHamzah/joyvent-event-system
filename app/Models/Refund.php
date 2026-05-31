<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'reason',
        'additional_notes',
        'status'
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
