<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PickupMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_code',
        'start_date',
        'end_date',
        'pickup_method',
        'delivery_address',
        'subtotal',
        'delivery_fee',
        'discount',
        'total_price',
        'status',
        'notes',
        'payment_deadline',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'pickup_method' => PickupMethod::class,
            'status' => BookingStatus::class,
            'payment_deadline' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            $booking->booking_code = 'BK-' . strtoupper(uniqid());
            $booking->payment_deadline = now()->addHours(24);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isPending(): bool
    {
        return $this->status === BookingStatus::Pending;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BookingStatus::Confirmed;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp' . number_format($this->total_price, 0, ',', '.');
    }

    public function isPaymentExpired(): bool
    {
        return $this->payment_deadline && now()->isAfter($this->payment_deadline);
    }
}
