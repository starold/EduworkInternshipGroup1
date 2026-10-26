<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\MotorStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'items.motor', 'payment'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('booking_code', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$request->search}%")))
            ->latest()
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['user', 'items.motor.category', 'payment']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        abort_if(! $booking->isPending(), 403, 'Booking tidak dalam status pending.');

        $booking->update(['status' => BookingStatus::Confirmed]);

        if ($booking->payment) {
            $booking->payment->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => now(),
            ]);
        }

        return back()->with('success', 'Booking berhasil dikonfirmasi.');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        abort_if(! $booking->isPending(), 403);

        DB::transaction(function () use ($booking, $request) {
            $booking->update([
                'status' => BookingStatus::Rejected,
                'notes' => $request->reason,
            ]);

            foreach ($booking->items as $item) {
                $item->motor->update(['status' => MotorStatus::Available]);
            }

            if ($booking->payment) {
                $booking->payment->update(['status' => PaymentStatus::Refunded]);
            }
        });

        return back()->with('success', 'Booking berhasil ditolak.');
    }
}
