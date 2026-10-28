<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\MotorStatus;
use App\Enums\PaymentStatus;
use App\Enums\RentalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmReturnRequest;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\MotorCategory;
use App\Models\Rental;
use App\Services\BookingCompletionService;
use App\Services\ReturnConfirmationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'items.motor.category', 'payment'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->input('status');
                if ($status === 'pending_payment') {
                    $query->where('status', BookingStatus::Pending)
                        ->where(function ($q) {
                            $q->whereDoesntHave('payment')
                                ->orWhereHas('payment', fn ($pq) => $pq->where('status', '!=', PaymentStatus::Paid));
                        });
                } elseif ($status === 'pending_confirmation') {
                    $query->where('status', BookingStatus::Pending)
                        ->whereHas('payment', fn ($pq) => $pq->where('status', PaymentStatus::Paid));
                } else {
                    $query->where('status', $status);
                }
            })
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
        $booking->load([
            'user',
            'items.motor.category',
            'items.rental.rentalReturn',
            'items.fines.payment',
            'items.review',
            'payment',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        abort_if(! $booking->isPending(), 403, 'Booking tidak dalam status pending.');
        abort_if(
            ! $booking->payment || ! $booking->payment->isPaid(),
            422,
            'Pemesanan tidak dapat dikonfirmasi karena status pembayaran belum selesai.'
        );

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => BookingStatus::Confirmed]);

            // Synchronize rental records for each booking item
            foreach ($booking->items as $item) {
                if (! $item->rental) {
                    Rental::create([
                        'booking_item_id' => $item->id,
                        'pickup_at' => $booking->start_date,
                        'due_date' => $item->due_date,
                        'status' => RentalStatus::Active,
                    ]);
                } else {
                    $item->rental->update(['status' => RentalStatus::Active]);
                }
            }
        });

        return back()->with('success', 'Booking berhasil dikonfirmasi. Status pesanan kini Sedang Disewa.');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        abort_if(! $booking->isPending(), 403, 'Booking tidak dalam status pending.');
        abort_if(
            ! $booking->payment || ! $booking->payment->isPaid(),
            422,
            'Pemesanan tidak dapat ditolak pada tahap ini karena status pembayaran belum selesai.'
        );

        DB::transaction(function () use ($booking, $request) {
            $booking->update([
                'status' => BookingStatus::Rejected,
                'notes' => $request->input('reason') ?: $request->input('notes'),
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

    public function verifyPayment(Booking $booking): RedirectResponse
    {
        abort_if(! $booking->isPending(), 403, 'Booking tidak dalam status pending.');

        $payment = $booking->payment;
        abort_if(! $payment, 404, 'Data pembayaran tidak ditemukan.');
        abort_if(! $payment->payment_proof, 422, 'Bukti pembayaran belum diunggah.');
        abort_if($payment->isPaid(), 422, 'Pembayaran sudah dikonfirmasi lunas.');

        $payment->update([
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi lunas. Status pesanan kini Menunggu Konfirmasi.');
    }

    public function rejectPayment(Request $request, Booking $booking): RedirectResponse
    {
        abort_if(! $booking->isPending(), 403, 'Booking tidak dalam status pending.');

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan bukti pembayaran wajib diisi.',
        ]);

        $payment = $booking->payment;
        abort_if(! $payment, 404, 'Data pembayaran tidak ditemukan.');
        abort_if(! $payment->payment_proof, 422, 'Bukti pembayaran belum diunggah.');
        abort_if($payment->isPaid(), 422, 'Pembayaran yang sudah lunas tidak dapat ditolak.');

        $payment->update([
            'status' => PaymentStatus::Failed,
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil ditolak. Pengguna harus mengunggah ulang bukti pembayaran sebelum tenggat waktu.');
    }

    public function confirmReturn(
        ConfirmReturnRequest $request,
        Booking $booking,
        BookingItem $item,
        ReturnConfirmationService $returnService
    ): RedirectResponse {
        abort_if($item->booking_id !== $booking->id, 404, 'Item tidak sesuai dengan pesanan.');

        try {
            $result = $returnService->confirmReturn(
                item: $item,
                returnTime: now(),
                returnData: $request->validated()
            );

            return back()->with('success', $result['message']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(Booking $booking, BookingCompletionService $completionService): RedirectResponse
    {
        try {
            $result = $completionService->completeBookingByAdmin($booking, Auth::user());

            return back()->with('success', $result['message']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
