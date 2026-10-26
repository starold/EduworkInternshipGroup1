<?php

namespace App\Http\Controllers\Customer;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $booking->load(['items.motor', 'payment']);

        return view('customer.payments.show', compact('booking'));
    }

    public function uploadProof(Request $request, Booking $booking): RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $booking->payment()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'payment_method' => $request->payment_method,
                'payment_proof' => $path,
                'status' => PaymentStatus::Pending,
            ]
        );

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin.');
    }
}
