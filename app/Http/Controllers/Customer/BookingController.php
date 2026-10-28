<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Enums\MotorStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Motor;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::with(['items.motor.category', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $popularMotors = Motor::with('category')
            ->where('status', MotorStatus::Available)
            ->latest()
            ->take(4)
            ->get();

        return view('customer.bookings.index', compact('bookings', 'popularMotors'));
    }

    public function create(Motor $motor): View
    {
        abort_if(! $motor->isAvailable(), 404, 'Motor tidak tersedia.');

        return view('customer.bookings.create', compact('motor'));
    }

    public function store(Request $request, Motor $motor): RedirectResponse
    {
        abort_if(! $motor->isAvailable(), 404, 'Motor tidak tersedia.');

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'pickup_method' => ['required', 'in:pickup,delivery'],
            'delivery_address' => ['required_if:pickup_method,delivery', 'nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInDays($endDate);

        $subtotal = $motor->price_per_day * $totalDays;
        $deliveryFee = $validated['pickup_method'] === 'delivery' ? 15000 : 0;
        $totalPrice = $subtotal + $deliveryFee;

        DB::transaction(function () use ($validated, $motor, $startDate, $endDate, $totalDays, $subtotal, $deliveryFee, $totalPrice) {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'pickup_method' => $validated['pickup_method'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => 0,
                'total_price' => $totalPrice,
                'status' => BookingStatus::Pending,
                'notes' => $validated['notes'] ?? null,
            ]);

            $booking->items()->create([
                'motor_id' => $motor->id,
                'price_per_day' => $motor->price_per_day,
                'due_date' => $endDate,
                'total_days' => $totalDays,
                'subtotal' => $subtotal,
                'deposit' => $motor->deposit,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalPrice,
                'status' => PaymentStatus::Pending,
            ]);

            $motor->update(['status' => MotorStatus::Rented]);
        });

        return redirect()->route('customer.bookings.index')
            ->with('success', 'Pemesanan berhasil! Silakan tunggu konfirmasi dari admin.');
    }

    public function show(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $booking->load([
            'items.motor.category',
            'items.fines',
            'items.review',
            'items.rental.rentalReturn',
            'payment',
            'user',
        ]);

        return view('customer.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(! $booking->isPending(), 403, 'Booking tidak dapat dibatalkan.');

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => BookingStatus::Cancelled]);

            foreach ($booking->items as $item) {
                $item->motor->update(['status' => MotorStatus::Available]);
            }

            if ($booking->payment) {
                $booking->payment->update(['status' => PaymentStatus::Refunded]);
            }
        });

        return back()->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}
