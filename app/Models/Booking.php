<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\FineStatus;
use App\Enums\PaymentStatus;
use App\Enums\PickupMethod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_code',
        'start_date',
        'end_date',
        'pickup_method',
        'delivery_address',
        'subtotal',
        'delivery_fee',
        'discount',
        'total_price',
        'status',
        'notes',
        'payment_deadline',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'pickup_method' => PickupMethod::class,
            'status' => BookingStatus::class,
            'payment_deadline' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'BK-'.strtoupper(uniqid());
            }
            if (empty($booking->payment_deadline)) {
                $booking->payment_deadline = now()->addHours(24);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function fines(): HasManyThrough
    {
        return $this->hasManyThrough(Fine::class, BookingItem::class, 'booking_id', 'booking_item_id');
    }

    public function isPending(): bool
    {
        return $this->status === BookingStatus::Pending;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BookingStatus::Confirmed;
    }

    public function isRejected(): bool
    {
        return $this->status === BookingStatus::Rejected;
    }

    public function isCancelled(): bool
    {
        return $this->status === BookingStatus::Cancelled;
    }

    public function isCompleted(): bool
    {
        return $this->status === BookingStatus::Completed;
    }

    public function isAllItemsReturned(): bool
    {
        if ($this->items->isEmpty()) {
            return false;
        }

        return $this->items->every(fn (BookingItem $item) => $item->isReturned());
    }

    public function hasUnpaidFines(): bool
    {
        return $this->fines()->where('status', FineStatus::Unpaid)->exists();
    }

    public function hasPendingFinePayments(): bool
    {
        return $this->fines()->where('fines.status', FineStatus::Unpaid)
            ->whereHas('payments', function ($query) {
                $query->where('status', PaymentStatus::Pending)
                    ->whereNotNull('payment_proof');
            })->exists();
    }

    public function totalUnpaidFines(): float
    {
        return (float) $this->fines()->where('status', FineStatus::Unpaid)->sum('amount');
    }

    public function totalFines(): float
    {
        return (float) $this->fines()->sum('amount');
    }

    public function canBeReturned(): bool
    {
        return $this->isConfirmed() && ! $this->isAllItemsReturned();
    }

    public function isEligibleForCompletion(): bool
    {
        return $this->isConfirmed()
            && $this->isAllItemsReturned()
            && ! $this->hasUnpaidFines()
            && ! $this->hasPendingFinePayments();
    }

    public function isAwaitingReturnConfirmation(): bool
    {
        return $this->isConfirmed() && $this->isAllItemsReturned();
    }

    public function uiStatusLabel(): string
    {
        if ($this->isConfirmed()) {
            if ($this->isAllItemsReturned()) {
                return 'Menunggu Konfirmasi Pengembalian';
            }

            return 'Sedang Disewa';
        }

        if ($this->isCompleted()) {
            return 'Selesai Rental';
        }

        if ($this->isPending()) {
            if ($this->payment && $this->payment->isPaid()) {
                return 'Menunggu Konfirmasi';
            }

            return 'Menunggu Pembayaran';
        }

        if ($this->isRejected()) {
            return 'Ditolak';
        }

        if ($this->isCancelled()) {
            return 'Dibatalkan';
        }

        return $this->status->label();
    }

    public function uiStatusBadgeClass(): string
    {
        if ($this->isConfirmed()) {
            if ($this->isAllItemsReturned()) {
                return 'badge-pending';
            }

            return 'badge-confirmed';
        }

        if ($this->isCompleted()) {
            return 'badge-completed';
        }

        if ($this->isPending()) {
            return 'badge-pending';
        }

        if ($this->isRejected()) {
            return 'badge-rejected';
        }

        if ($this->isCancelled()) {
            return 'badge-cancelled';
        }

        return 'badge-pending';
    }

    public function getCompletionBlockReason(): ?string
    {
        if ($this->isCompleted()) {
            return 'Pemesanan sudah berstatus selesai.';
        }

        if (! $this->isConfirmed()) {
            return 'Pemesanan belum berstatus dikonfirmasi.';
        }

        if (! $this->isAllItemsReturned()) {
            return 'Seluruh motor harus dikonfirmasi sudah kembali terlebih dahulu.';
        }

        if ($this->hasPendingFinePayments()) {
            return 'Pembayaran denda masih menunggu konfirmasi verifikasi Admin.';
        }

        if ($this->hasUnpaidFines()) {
            return 'Selesaikan seluruh pembayaran denda terlebih dahulu.';
        }

        return null;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp'.number_format($this->total_price, 0, ',', '.');
    }

    public function isWaitingPayment(): bool
    {
        return $this->isPending() && (! $this->payment || ! $this->payment->isPaid());
    }

    public function isWaitingConfirmation(): bool
    {
        return $this->isPending() && (bool) ($this->payment && $this->payment->isPaid());
    }

    public function isRented(): bool
    {
        return $this->isConfirmed() && ! $this->isAllItemsReturned();
    }

    public function getDeliveryInstructions(): ?string
    {
        if (! $this->isConfirmed()) {
            return null;
        }

        if ($this->pickup_method === PickupMethod::Delivery || $this->pickup_method?->value === 'delivery') {
            return 'Silahkan tunggu motor anda datang. (maksimal 1x24 jam)';
        }

        return 'Silahkan datang ke konter langsung.';
    }

    public function getPaymentDeadline(): Carbon
    {
        if ($this->created_at) {
            return $this->created_at->copy()->addHours(24);
        }

        if ($this->payment_deadline) {
            return $this->payment_deadline instanceof Carbon ? $this->payment_deadline : Carbon::parse($this->payment_deadline);
        }

        return now()->addHours(24);
    }

    public function isPaymentExpired(): bool
    {
        if ($this->payment && $this->payment->isPaid()) {
            return false;
        }

        return now()->greaterThan($this->getPaymentDeadline());
    }
}
