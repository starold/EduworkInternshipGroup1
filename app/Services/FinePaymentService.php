<?php

namespace App\Services;

use App\Enums\FineStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FinePaymentService
{
    public function __construct(
        protected BookingCompletionService $bookingCompletionService,
    ) {}

    /**
     * Submit payment proof for a fine by user.
     * Sets payment to pending_verification (PaymentStatus::Pending) without marking fine as paid.
     *
     * @return array{
     *     success: bool,
     *     payment: Payment,
     *     message: string
     * }
     */
    public function submitPaymentProof(
        Fine $fine,
        User $user,
        string $paymentMethod,
        UploadedFile $proofFile
    ): array {
        return DB::transaction(function () use ($fine, $user, $paymentMethod, $proofFile) {
            /** @var Fine $lockedFine */
            $lockedFine = Fine::where('id', $fine->id)
                ->with(['bookingItem.booking', 'payment'])
                ->lockForUpdate()
                ->firstOrFail();

            $booking = $lockedFine->bookingItem->booking;

            // Authorization: fine must belong to the user (or admin)
            if (! $user->isAdmin() && $booking->user_id !== $user->id) {
                throw new InvalidArgumentException('Anda tidak memiliki akses ke denda ini.');
            }

            // Cannot pay fine that is already paid
            if ($lockedFine->isPaid()) {
                throw new InvalidArgumentException('Denda ini sudah lunas.');
            }

            // Check if already awaiting verification
            if ($lockedFine->isAwaitingVerification()) {
                throw new InvalidArgumentException('Pembayaran untuk denda ini sedang menunggu verifikasi admin.');
            }

            if ($lockedFine->amount < Fine::MIN_AMOUNT || $lockedFine->amount > Fine::MAX_AMOUNT) {
                throw new InvalidArgumentException('Nominal denda berada di luar batas yang diizinkan.');
            }

            // Store file securely using Laravel Storage
            $path = $proofFile->store('payment-proofs', 'public');

            // Create or update Payment record
            /** @var Payment $payment */
            $payment = Payment::updateOrCreate(
                [
                    'fine_id' => $lockedFine->id,
                ],
                [
                    'booking_id' => $booking->id,
                    'payment_type' => 'fine',
                    'amount' => $lockedFine->amount,
                    'payment_method' => $paymentMethod,
                    'payment_proof' => $path,
                    'status' => PaymentStatus::Pending,
                    'rejection_reason' => null,
                ]
            );

            // Keep reference on fine for quick lookup
            $lockedFine->update([
                'payment_method' => $paymentMethod,
                'transaction_reference' => $payment->payment_code,
            ]);

            return [
                'success' => true,
                'payment' => $payment->fresh(),
                'message' => 'Bukti pembayaran denda berhasil diunggah. Menunggu verifikasi admin.',
            ];
        });
    }

    /**
     * Admin confirms fine payment as paid / verified.
     *
     * @return array{
     *     success: bool,
     *     fine: Fine,
     *     booking_completed: bool,
     *     message: string
     * }
     */
    public function confirmPaymentByAdmin(Fine $fine, User $admin): array
    {
        if (! $admin->isAdmin()) {
            throw new InvalidArgumentException('Hanya admin yang berwenang mengonfirmasi pembayaran denda.');
        }

        return DB::transaction(function () use ($fine) {
            /** @var Fine $lockedFine */
            $lockedFine = Fine::where('id', $fine->id)
                ->with(['bookingItem.booking', 'payment'])
                ->lockForUpdate()
                ->firstOrFail();

            $booking = $lockedFine->bookingItem->booking;

            if ($lockedFine->isPaid()) {
                throw new InvalidArgumentException('Denda ini sudah dikonfirmasi lunas sebelumnya.');
            }

            $payment = $lockedFine->payment;
            if (! $payment || ! $payment->payment_proof) {
                throw new InvalidArgumentException('Bukti pembayaran tidak ditemukan.');
            }

            if (! $payment->isPending()) {
                throw new InvalidArgumentException('Status pembayaran bukan menunggu verifikasi.');
            }

            $now = now();

            // Update payment to paid
            $payment->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => $now,
            ]);

            // Update fine to paid
            $lockedFine->update([
                'status' => FineStatus::Paid,
                'paid_at' => $now,
                'payment_method' => $payment->payment_method,
                'transaction_reference' => $payment->payment_code,
            ]);

            // Booking remains in Menunggu Konfirmasi Pengembalian until explicit Admin completion
            $bookingCompleted = false;

            $message = 'Pembayaran denda berhasil diverifikasi dan dikonfirmasi lunas.';

            return [
                'success' => true,
                'fine' => $lockedFine->fresh(),
                'booking_completed' => $bookingCompleted,
                'message' => $message,
            ];
        });
    }

    /**
     * Admin rejects fine payment proof.
     *
     * @return array{
     *     success: bool,
     *     message: string
     * }
     */
    public function rejectPaymentByAdmin(Fine $fine, User $admin, string $reason): array
    {
        if (! $admin->isAdmin()) {
            throw new InvalidArgumentException('Hanya admin yang berwenang menolak bukti pembayaran.');
        }

        return DB::transaction(function () use ($fine, $reason) {
            /** @var Fine $lockedFine */
            $lockedFine = Fine::where('id', $fine->id)
                ->with(['payment'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedFine->isPaid()) {
                throw new InvalidArgumentException('Denda yang sudah lunas tidak dapat ditolak pembayarannya.');
            }

            $payment = $lockedFine->payment;
            if (! $payment || ! $payment->isPending()) {
                throw new InvalidArgumentException('Tidak ada pembayaran yang sedang menunggu verifikasi.');
            }

            $payment->update([
                'status' => PaymentStatus::Failed,
                'rejection_reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => 'Bukti pembayaran denda telah ditolak. Pengguna dapat mengunggah bukti baru.',
            ];
        });
    }

    /**
     * Programmatic / direct payment helper (also used by existing automated tests).
     *
     * @return array{
     *     success: bool,
     *     fine: Fine,
     *     booking_completed: bool,
     *     message: string
     * }
     */
    public function payFine(
        Fine $fine,
        User $user,
        string $paymentMethod = 'bank_transfer',
        ?string $transactionReference = null
    ): array {
        return DB::transaction(function () use ($fine, $user, $paymentMethod, $transactionReference) {
            /** @var Fine $lockedFine */
            $lockedFine = Fine::where('id', $fine->id)
                ->with(['bookingItem.booking'])
                ->lockForUpdate()
                ->firstOrFail();

            $booking = $lockedFine->bookingItem->booking;

            if (! $user->isAdmin() && $booking->user_id !== $user->id) {
                throw new InvalidArgumentException('Anda tidak memiliki akses ke denda ini.');
            }

            if ($lockedFine->isPaid()) {
                throw new InvalidArgumentException('Denda ini sudah dibayar sebelumnya.');
            }

            if ($lockedFine->amount < Fine::MIN_AMOUNT || $lockedFine->amount > Fine::MAX_AMOUNT) {
                throw new InvalidArgumentException('Nominal denda tidak valid.');
            }

            $reference = $transactionReference ?? ('FINE-' . strtoupper(uniqid()));

            // Update or create payment record
            Payment::updateOrCreate(
                ['fine_id' => $lockedFine->id],
                [
                    'booking_id' => $booking->id,
                    'payment_type' => 'fine',
                    'amount' => $lockedFine->amount,
                    'payment_method' => $paymentMethod,
                    'status' => PaymentStatus::Paid,
                    'paid_at' => now(),
                    'payment_code' => $reference,
                ]
            );

            $lockedFine->update([
                'status' => FineStatus::Paid,
                'paid_at' => now(),
                'payment_method' => $paymentMethod,
                'transaction_reference' => $reference,
            ]);

            $bookingCompleted = false;

            return [
                'success' => true,
                'fine' => $lockedFine->fresh(),
                'booking_completed' => $bookingCompleted,
                'message' => 'Pembayaran denda berhasil.',
            ];
        });
    }

    /**
     * Pay all unpaid fines for a booking at once.
     */
    public function payAllFinesForBooking(
        Booking $booking,
        User $user,
        string $paymentMethod = 'bank_transfer'
    ): array {
        return DB::transaction(function () use ($booking, $user, $paymentMethod) {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::where('id', $booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $user->isAdmin() && $lockedBooking->user_id !== $user->id) {
                throw new InvalidArgumentException('Anda tidak memiliki akses ke pemesanan ini.');
            }

            $unpaidFines = Fine::whereHas('bookingItem', function ($query) use ($lockedBooking) {
                $query->where('booking_id', $lockedBooking->id);
            })
                ->where('status', FineStatus::Unpaid)
                ->lockForUpdate()
                ->get();

            if ($unpaidFines->isEmpty()) {
                return [
                    'success' => true,
                    'paid_count' => 0,
                    'total_amount' => 0.0,
                    'booking_completed' => false,
                    'message' => 'Tidak ada denda yang perlu dibayar.',
                ];
            }

            $referenceBatch = 'FINES-' . strtoupper(uniqid());
            $now = now();
            $totalAmount = 0.0;

            foreach ($unpaidFines as $fine) {
                $totalAmount += (float) $fine->amount;

                Payment::updateOrCreate(
                    ['fine_id' => $fine->id],
                    [
                        'booking_id' => $lockedBooking->id,
                        'payment_type' => 'fine',
                        'amount' => $fine->amount,
                        'payment_method' => $paymentMethod,
                        'status' => PaymentStatus::Paid,
                        'paid_at' => $now,
                        'payment_code' => $referenceBatch,
                    ]
                );

                $fine->update([
                    'status' => FineStatus::Paid,
                    'paid_at' => $now,
                    'payment_method' => $paymentMethod,
                    'transaction_reference' => $referenceBatch,
                ]);
            }

            $bookingCompleted = false;

            return [
                'success' => true,
                'paid_count' => $unpaidFines->count(),
                'total_amount' => $totalAmount,
                'booking_completed' => $bookingCompleted,
                'message' => 'Seluruh denda berhasil dibayar.',
            ];
        });
    }
}
