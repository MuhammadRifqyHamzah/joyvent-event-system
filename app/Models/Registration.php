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
        'status', // Tetap dipertahankan untuk backward compatibility
        'registration_status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_amount',
        'payment_gateway',
        'payment_notes',
        'paid_at',
        'payment_expired_at',
        'payment_proof',
        'payment_proof_uploaded_at',
        'payment_proof_size',
        'payment_rejection_reason',
        'payment_verified_by',
        'payment_verified_at',
        'payment_rejected_by',
        'payment_rejected_at'
    ];

    protected $casts = [
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_expired_at' => 'datetime',
        'payment_amount' => 'decimal:2',
        'payment_proof_uploaded_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'payment_rejected_at' => 'datetime',
    ];

    /**
     * Check if payment is successful.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if registration is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->registration_status === 'cancelled';
    }

    /**
     * Check if registration is active.
     */
    public function isActive(): bool
    {
        return $this->registration_status === 'active';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'payment_rejected_by');
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