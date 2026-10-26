<?php

namespace App\Models;

use App\Enums\ConditionStatus;
use App\Enums\FuelLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'returned_at',
        'fuel_level',
        'late_minutes',
        'condition_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'returned_at' => 'datetime',
            'fuel_level' => FuelLevel::class,
            'condition_status' => ConditionStatus::class,
        ];
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }
}
