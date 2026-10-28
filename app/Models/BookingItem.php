<?php

namespace App\Models;

use App\Enums\FineStatus;
use App\Enums\ReturnStatus;
use Carbon\Carbon;
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
        'returned_at',
        'return_status',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'due_date' => 'datetime',
            'subtotal' => 'decimal:2',
            'deposit' => 'decimal:2',
            'returned_at' => 'datetime',
            'return_status' => ReturnStatus::class,
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

    public function isReturned(): bool
    {
        return $this->return_status === ReturnStatus::Returned || $this->returned_at !== null;
    }

    public function isOverdue(?Carbon $checkTime = null): bool
    {
        $time = $checkTime ?? ($this->returned_at ?? now());

        return $time->greaterThan($this->due_date);
    }

    public function calculateDaysLate(?Carbon $checkTime = null): int
    {
        $endTime = $checkTime ?? ($this->returned_at ?? now());

        if ($endTime->lessThanOrEqualTo($this->due_date)) {
            return 0;
        }

        $secondsLate = $this->due_date->diffInSeconds($endTime, false);

        if ($secondsLate <= 0) {
            return 0;
        }

        return (int) ceil($secondsLate / 86400);
    }

    public function hasUnpaidFines(): bool
    {
        return $this->fines()->where('status', FineStatus::Unpaid)->exists();
    }

    public function unpaidFinesTotal(): float
    {
        return (float) $this->fines()->where('status', FineStatus::Unpaid)->sum('amount');
    }

    public function allFinesPaid(): bool
    {
        return ! $this->hasUnpaidFines();
    }
}
