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
        'fine_id',
        'payment_type',
        'payment_code',
        'amount',
        'payment_method',
        'payment_proof',
        'paid_at',
        'status',
        'rejection_reason',
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
            if (empty($payment->payment_code)) {
                $prefix = $payment->fine_id ? 'PAY-FINE-' : 'PAY-';
                $payment->payment_code = $prefix.strtoupper(uniqid());
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function fine(): BelongsTo
    {
        return $this->belongsTo(Fine::class);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::Failed;
    }

    public function isFinePayment(): bool
    {
        return $this->fine_id !== null || $this->payment_type === 'fine';
    }

    public function isRentalPayment(): bool
    {
        return ! $this->isFinePayment();
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->payment_proof
            ? asset('storage/'.$this->payment_proof)
            : null;
    }
}
