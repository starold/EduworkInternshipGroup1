<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\FineStatus;
use App\Enums\FineType;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FineCalculationService
{
    /**
     * Additional daily late penalty fee in IDR.
     */
    public const DAILY_LATE_FEE = 30000;

    /**
     * Calculate and sync the late fine for a specific booking item.
     */
    public function calculateForItem(BookingItem $item, ?Carbon $asOf = null): ?Fine
    {
        $booking = $item->booking;

        // Never generate late fines for pending, rejected, or cancelled bookings.
        if (! $booking || ! in_array($booking->status, [BookingStatus::Confirmed, BookingStatus::Completed])) {
            return null;
        }

        // Once motor is returned, late calculation is anchored to returned_at
        $checkTime = $item->isReturned() ? $item->returned_at : ($asOf ?? now());

        // If not past due date, no fine
        if ($checkTime->lessThanOrEqualTo($item->due_date)) {
            return null;
        }

        $daysLate = $item->calculateDaysLate($checkTime);

        if ($daysLate <= 0) {
            return null;
        }

        // Daily rate is frozen at booking creation time + late surcharge
        $dailyRate = round((float) $item->price_per_day + self::DAILY_LATE_FEE, 2);
        $totalLateFine = round($daysLate * $dailyRate, 2);

        // Boundary validation: between Rp10.000 and Rp100.000.000
        if ($totalLateFine < Fine::MIN_AMOUNT) {
            Log::warning("Kalkulasi denda keterlambatan booking item #{$item->id} sebesar Rp{$totalLateFine} di bawah batas minimum Rp10.000.");

            return null;
        }

        if ($totalLateFine > Fine::MAX_AMOUNT) {
            Log::warning("Kalkulasi denda keterlambatan booking item #{$item->id} sebesar Rp{$totalLateFine} melebihi batas maksimum Rp100.000.000.");

            return null;
        }

        return DB::transaction(function () use ($item, $daysLate, $totalLateFine, $checkTime) {
            /** @var Fine|null $existingFine */
            $existingFine = Fine::where('booking_item_id', $item->id)
                ->where('type', FineType::Late)
                ->lockForUpdate()
                ->first();

            $motorName = $item->motor ? "{$item->motor->merk} {$item->motor->model}" : 'Motor';
            $description = "Denda keterlambatan pengembalian {$daysLate} hari ({$motorName})";

            if (! $existingFine) {
                return Fine::create([
                    'booking_item_id' => $item->id,
                    'type' => FineType::Late,
                    'description' => $description,
                    'amount' => $totalLateFine,
                    'status' => FineStatus::Unpaid,
                    'started_at' => $item->due_date,
                    'ended_at' => $item->isReturned() ? ($item->returned_at ?? $checkTime) : null,
                    'days_late' => $daysLate,
                ]);
            }

            // If already paid, do not overwrite unless motor was returned and we are finalizing end metadata
            if ($existingFine->isPaid()) {
                if ($item->isReturned() && ! $existingFine->ended_at) {
                    $existingFine->update([
                        'ended_at' => $item->returned_at ?? $checkTime,
                    ]);
                }

                return $existingFine;
            }

            // If still unpaid and motor is not returned, update fine amount & days
            // If motor has been returned, finalize end timestamp and final amount
            $updateData = [
                'description' => $description,
                'amount' => $totalLateFine,
                'days_late' => $daysLate,
            ];

            if ($item->isReturned()) {
                $updateData['ended_at'] = $item->returned_at ?? $checkTime;
            }

            $existingFine->update($updateData);

            return $existingFine->fresh();
        });
    }

    /**
     * Process all confirmed active bookings that are overdue and unreturned.
     * Idempotent and concurrency-safe.
     *
     * @return int Count of fines updated or created
     */
    public function calculateAllActiveBookings(): int
    {
        $now = now();
        $processedCount = 0;

        // Find confirmed bookings with overdue, unreturned items
        $overdueItems = BookingItem::whereHas('booking', function ($query) {
            $query->where('status', BookingStatus::Confirmed);
        })
            ->whereNull('returned_at')
            ->where('due_date', '<', $now)
            ->with(['booking', 'motor'])
            ->get();

        foreach ($overdueItems as $item) {
            try {
                $fine = $this->calculateForItem($item, $now);
                if ($fine) {
                    $processedCount++;
                }
            } catch (\Throwable $e) {
                Log::error('Error calculating late fine for booking item #'.$item->id.': '.$e->getMessage());
            }
        }

        return $processedCount;
    }
}
