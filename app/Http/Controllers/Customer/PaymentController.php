<?php

namespace App\Http\Controllers\Customer;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Booking $booking): View|RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        if ($booking->payment && $booking->payment->isPaid()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'Pembayaran untuk pesanan ini sudah selesai diverifikasi.');
        }

        if (! $booking->isPending()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'Status pesanan tidak memungkinkan untuk melakukan pembayaran.');
        }

        $booking->load(['items.motor', 'payment']);

        if ($booking->isPaymentExpired() && (! $booking->payment || ! $booking->payment->isPaid())) {
            return redirect()->route('customer.payments.expired', $booking);
        }

        return view('customer.payments.show', compact('booking'));
    }

    public function uploadProof(Request $request, Booking $booking): RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        abort_if(
            $booking->payment && $booking->payment->isPaid(),
            403,
            'Pembayaran untuk pesanan ini sudah selesai diverifikasi.'
        );

        abort_if(
            ! $booking->isPending(),
            403,
            'Pesanan tidak dalam status menunggu pembayaran.'
        );

        if ($booking->isPaymentExpired()) {
            return back()->withErrors([
                'payment_proof' => 'Tenggat waktu pembayaran telah habis. Silakan buat pesanan baru.',
            ]);
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $booking->payment()->updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'payment_method' => $request->payment_method,
                'payment_proof' => $path,
                'status' => PaymentStatus::Pending,
                'rejection_reason' => null,
            ]
        );

        if ($request->input('redirect_to') === 'success') {
            return redirect()->route('customer.payments.success', $booking)
                ->with('success', 'Bukti pembayaran berhasil diupload.');
        }

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    public function success(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $booking->load(['items.motor', 'payment']);

        return view('customer.payments.success', compact('booking'));
    }

    public function expired(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $booking->load(['items.motor']);

        return view('customer.payments.expired', compact('booking'));
    }
}
