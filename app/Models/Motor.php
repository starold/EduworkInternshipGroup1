<?php

namespace App\Models;

use App\Enums\MotorStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Motor extends Model
{
    use HasFactory;

    protected $fillable = [
        'motor_category_id',
        'merk',
        'warna',
        'model',
        'year',
        'plate_number',
        'price_per_day',
        'deposit',
        'description',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'deposit' => 'decimal:2',
            'status' => MotorStatus::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MotorCategory::class, 'motor_category_id');
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === MotorStatus::Available;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return $this->image
            ? asset('storage/'.$this->image)
            : asset('images/motor-placeholder.jpg');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp'.number_format($this->price_per_day, 0, ',', '.');
    }
}
