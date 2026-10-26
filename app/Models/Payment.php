<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_code',
        'amount',
        'payment_method',
        'payment_proof',
        'paid_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'status' => PaymentStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            $payment->payment_code = 'PAY-' . strtoupper(uniqid());
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->payment_proof
            ? asset('storage/' . $this->payment_proof)
            : null;
    }
}
