<?php

namespace App\Models;

use App\Enums\FineStatus;
use App\Enums\FineType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fine extends Model
{
    use HasFactory;

    public const MIN_AMOUNT = 10000;

    public const MAX_AMOUNT = 100000000;

    protected $fillable = [
        'booking_item_id',
        'type',
        'description',
        'amount',
        'status',
        'paid_at',
        'started_at',
        'ended_at',
        'days_late',
        'payment_method',
        'transaction_reference',
    ];

    protected function casts(): array
    {
        return [
            'type' => FineType::class,
            'amount' => 'decimal:2',
            'status' => FineStatus::class,
            'paid_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'days_late' => 'integer',
        ];
    }

    /**
     * Alias booking_items_id to booking_item_id for compatibility.
     */
    protected function bookingItemsId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->booking_item_id,
            set: fn ($value) => ['booking_item_id' => $value],
        );
    }

    public function bookingItem(): BelongsTo
    {
        return $this->belongsTo(BookingItem::class, 'booking_item_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->status === FineStatus::Paid;
    }

    public function isUnpaid(): bool
    {
        return $this->status === FineStatus::Unpaid;
    }

    public function isLate(): bool
    {
        return $this->type === FineType::Late;
    }

    public function isDamage(): bool
    {
        return $this->type === FineType::Damage;
    }

    public function isOther(): bool
    {
        return $this->type === FineType::Other;
    }

    public function hasPendingPayment(): bool
    {
        return $this->isUnpaid()
            && $this->payment
            && $this->payment->isPending()
            && ! empty($this->payment->payment_proof);
    }

    public function isAwaitingVerification(): bool
    {
        return $this->hasPendingPayment();
    }

    public function hasRejectedPayment(): bool
    {
        return $this->isUnpaid()
            && $this->payment
            && $this->payment->isFailed();
    }

    public function canSubmitPaymentProof(): bool
    {
        return $this->isUnpaid() && ! $this->isAwaitingVerification();
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp'.number_format($this->amount, 0, ',', '.');
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->payment?->proof_url;
    }
}
