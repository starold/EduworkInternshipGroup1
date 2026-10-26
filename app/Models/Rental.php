<?php

namespace App\Models;

use App\Enums\RentalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_item_id',
        'pickup_at',
        'due_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_at' => 'datetime',
            'due_date' => 'datetime',
            'status' => RentalStatus::class,
        ];
    }

    public function bookingItem(): BelongsTo
    {
        return $this->belongsTo(BookingItem::class);
    }

    public function rentalReturn(): HasOne
    {
        return $this->hasOne(RentalReturn::class);
    }
}
