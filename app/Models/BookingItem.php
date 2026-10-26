<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'motor_id',
        'price_per_day',
        'due_date',
        'total_days',
        'subtotal',
        'deposit',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'due_date' => 'datetime',
            'subtotal' => 'decimal:2',
            'deposit' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class);
    }

    public function rental(): HasOne
    {
        return $this->hasOne(Rental::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'booking_items_id');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class, 'booking_item_id');
    }
}
