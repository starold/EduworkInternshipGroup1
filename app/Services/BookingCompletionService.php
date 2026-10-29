<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\FineStatus;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingCompletionService
{
    /**
     * Explicit completion by Admin.
     * Validates all requirements strictly with concurrency lock.
     *
     * @return array{success: bool, booking: Booking, message: string}
     */
    public function completeBookingByAdmin(Booking $booking, User $admin): array
    {
        if (! $admin->isAdmin()) {
            throw new InvalidArgumentException('Hanya admin yang berwenang menyelesaikan pemesanan.');
        }

        return DB::transaction(function () use ($booking) {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::where('id', $booking->id)
                ->with(['items.fines'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBooking->isCompleted()) {
                throw new InvalidArgumentException('Pemesanan ini sudah diselesaikan sebelumnya.');
            }

            if (! $lockedBooking->isConfirmed()) {
                throw new InvalidArgumentException('Pemesanan hanya dapat diselesaikan jika berstatus Menunggu Konfirmasi Pengembalian.');
            }

            if (! $lockedBooking->isAllItemsReturned()) {
                throw new InvalidArgumentException('Seluruh motor sewa harus dikonfirmasi kembali terlebih dahulu.');
            }

            if ($lockedBooking->hasPendingFinePayments()) {
                throw new InvalidArgumentException('Pembayaran denda masih menunggu konfirmasi verifikasi Admin.');
            }

            if ($lockedBooking->hasUnpaidFines()) {
                throw new InvalidArgumentException('Selesaikan seluruh pembayaran denda terlebih dahulu.');
            }

            $lockedBooking->update([
                'status' => BookingStatus::Completed,
            ]);

            return [
                'success' => true,
                'booking' => $lockedBooking->fresh(),
                'message' => 'Pemesanan rental berhasil diselesaikan.',
            ];
        });
    }

    /**
     * Check if a booking is ready to be transitioned to Completed.
     * Criteria:
     * 1. Booking status is Confirmed.
     * 2. All booking items have been confirmed returned.
     * 3. All associated fines are paid (or there are no fines).
     */
    public function checkAndComplete(Booking $booking): bool
    {
        return DB::transaction(function () use ($booking) {
            /** @var Booking $freshBooking */
            $freshBooking = Booking::where('id', $booking->id)
                ->with(['items.fines'])
                ->lockForUpdate()
                ->first();

            if (! $freshBooking || $freshBooking->status !== BookingStatus::Confirmed) {
                return false;
            }

            // Ensure every item is returned
            $allItemsReturned = $freshBooking->items->isNotEmpty()
                && $freshBooking->items->every(fn ($item) => $item->isReturned());

            if (! $allItemsReturned) {
                return false;
            }

            // Ensure no item has any unpaid fine
            $hasUnpaidFines = $freshBooking->items->some(function ($item) {
                return $item->fines->contains(fn ($fine) => $fine->status === FineStatus::Unpaid);
            });

            if ($hasUnpaidFines) {
                return false;
            }

            // Ensure no fine has payment pending verification
            if ($freshBooking->hasPendingFinePayments()) {
                return false;
            }

            // All criteria met -> complete booking
            $freshBooking->update(['status' => BookingStatus::Completed]);

            return true;
        });
    }
}
