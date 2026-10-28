<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreReviewRequest;
use App\Models\BookingItem;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, BookingItem $bookingItem): RedirectResponse
    {
        $booking = $bookingItem->booking;

        abort_if(! $booking || $booking->user_id !== Auth::id(), 403, 'Akses tidak diizinkan.');
        abort_if(
            $booking->isPending() || ($booking->payment && ! $booking->payment->isPaid()),
            403,
            'Review tidak dapat diberikan saat status masih menunggu konfirmasi pembayaran.'
        );
        abort_if($booking->hasPendingFinePayments(), 403, 'Review tidak dapat diberikan saat pembayaran denda masih menunggu konfirmasi.');
        abort_if(! $bookingItem->isReturned(), 403, 'Review hanya dapat diberikan setelah motor dikembalikan.');
        abort_if($bookingItem->hasUnpaidFines(), 403, 'Review hanya dapat diberikan setelah seluruh denda diselesaikan.');
        abort_if(! $booking->isCompleted(), 403, 'Review hanya dapat diberikan setelah pemesanan selesai.');
        abort_if($bookingItem->review()->exists(), 403, 'Review sudah diberikan untuk pemesanan ini.');

        Review::create([
            'booking_items_id' => $bookingItem->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        return back()->with('success', 'Terima kasih! Review Anda berhasil dikirim.');
    }
}
