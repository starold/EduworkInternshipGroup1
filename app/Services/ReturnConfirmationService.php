<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\ConditionStatus;
use App\Enums\FuelLevel;
use App\Enums\MotorStatus;
use App\Enums\RentalStatus;
use App\Enums\ReturnStatus;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\RentalReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReturnConfirmationService
{
    public function __construct(
        protected FineCalculationService $fineCalculationService,
        protected BookingCompletionService $bookingCompletionService,
    ) {}

    /**
     * Confirm motorbike return for a specific booking item.
     *
     * @param  array{fuel_level?: FuelLevel|string, condition_status?: ConditionStatus|string, notes?: string|null}  $returnData
     * @return array{
     *     success: bool,
     *     returned_at: Carbon,
     *     days_late: int,
     *     late_fine: ?\App\Models\Fine,
     *     booking_completed: bool,
     *     remaining_unpaid_fines: float,
     *     message: string
     * }
     */
    public function confirmReturn(
        BookingItem $item,
        ?Carbon $returnTime = null,
        array $returnData = []
    ): array {
        $actualReturnTime = $returnTime ?? now();

        return DB::transaction(function () use ($item, $actualReturnTime, $returnData) {
            /** @var BookingItem $lockedItem */
            $lockedItem = BookingItem::where('id', $item->id)
                ->with(['booking', 'motor', 'rental'])
                ->lockForUpdate()
                ->firstOrFail();

            $booking = $lockedItem->booking;

            if (! $booking || $booking->status !== BookingStatus::Confirmed) {
                throw new InvalidArgumentException('Pengembalian hanya dapat diproses untuk booking berstatus Confirmed.');
            }

            if ($lockedItem->isReturned()) {
                throw new InvalidArgumentException('Motor untuk pesanan ini sudah dikonfirmasi telah dikembalikan.');
            }

            // 1. Mark item as returned
            $lockedItem->update([
                'returned_at' => $actualReturnTime,
                'return_status' => ReturnStatus::Returned,
            ]);

            // 2. Motor is back to Available
            if ($lockedItem->motor) {
                $lockedItem->motor->update(['status' => MotorStatus::Available]);
            }

            // 3. Update rental record if one exists
            if ($lockedItem->rental) {
                $lockedItem->rental->update(['status' => RentalStatus::Returned]);

                $lateMinutes = max(0, (int) $lockedItem->due_date->diffInMinutes($actualReturnTime, false));

                RentalReturn::create([
                    'rental_id' => $lockedItem->rental->id,
                    'returned_at' => $actualReturnTime,
                    'fuel_level' => $returnData['fuel_level'] ?? FuelLevel::Full,
                    'late_minutes' => $lateMinutes,
                    'condition_status' => $returnData['condition_status'] ?? ConditionStatus::Good,
                    'notes' => $returnData['notes'] ?? null,
                ]);
            }

            // 4. Calculate and finalize late fine if overdue
            $lateFine = $this->fineCalculationService->calculateForItem($lockedItem, $actualReturnTime);

            // 5. Booking transitions to Menunggu Konfirmasi Pengembalian (requires explicit Admin completion)
            $bookingCompleted = false;

            $daysLate = $lockedItem->calculateDaysLate($actualReturnTime);
            $remainingUnpaidFines = $booking->fresh()->totalUnpaidFines();

            $message = 'Pengembalian motor berhasil dikonfirmasi.';
            if ($daysLate > 0) {
                $message .= " Terdapat keterlambatan {$daysLate} hari.";
            }
            if ($remainingUnpaidFines > 0) {
                $message .= ' Terdapat denda yang belum dibayar.';
            } else {
                $message .= ' Menunggu konfirmasi penyelesaian pemesanan oleh Admin.';
            }

            return [
                'success' => true,
                'returned_at' => $actualReturnTime,
                'days_late' => $daysLate,
                'late_fine' => $lateFine,
                'booking_completed' => $bookingCompleted,
                'remaining_unpaid_fines' => $remainingUnpaidFines,
                'message' => $message,
            ];
        });
    }
}
