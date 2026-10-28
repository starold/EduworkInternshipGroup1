<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\MotorStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MotorCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'items.motor.category', 'payment'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('items.motor', fn ($motorQuery) => $motorQuery->where('motor_category_id', $request->input('category')));
            })
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('start_date', $request->input('start_date')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->input('search').'%';

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('booking_code', 'like', $search)
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', $search))
                        ->orWhereHas('items.motor', function ($motorQuery) use ($search) {
                            $motorQuery->where('merk', 'like', $search)
                                ->orWhere('model', 'like', $search);
                        });
                });
            })
            ->latest()
            ->paginate(15);

        $categories = MotorCategory::orderBy('name')->get();

        return view('admin.bookings.index', compact('bookings', 'categories'));
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
