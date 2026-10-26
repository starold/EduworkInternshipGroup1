<?php

namespace App\Models;

use App\Enums\FineStatus;
use App\Enums\FineType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_item_id',
        'type',
        'description',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => FineType::class,
            'amount' => 'decimal:2',
            'status' => FineStatus::class,
        ];
    }

    public function bookingItem(): BelongsTo
    {
        return $this->belongsTo(BookingItem::class);
    }
}
