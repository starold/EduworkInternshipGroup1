<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FineStatus;
use App\Enums\FineType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFineRequest;
use App\Models\BookingItem;
use App\Models\Fine;
use App\Services\FinePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(Request $request): View
    {
        $fines = Fine::with(['bookingItem.booking.user', 'bookingItem.motor', 'payment'])
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('created_at', $request->input('date')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->input('search').'%';

                $query->where(function ($sub) use ($search) {
                    $sub->where('description', 'like', $search)
                        ->orWhere('transaction_reference', 'like', $search)
                        ->orWhereHas('bookingItem.booking', fn ($b) => $b->where('booking_code', 'like', $search))
                        ->orWhereHas('bookingItem.booking.user', fn ($u) => $u->where('name', 'like', $search))
                        ->orWhereHas('bookingItem.motor', function ($m) use ($search) {
                            $m->where('merk', 'like', $search)->orWhere('model', 'like', $search);
                        });
                });
            })
            ->latest()
            ->paginate(15);

        $types = FineType::cases();
        $statuses = FineStatus::cases();

        return view('admin.fines.index', compact('fines', 'types', 'statuses'));
    }

    public function store(StoreFineRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $item = BookingItem::with('booking')->findOrFail($validated['booking_item_id']);

        if (! $item->isReturned()) {
            return back()->with('error', 'Denda hanya dapat dibuat setelah motor dinyatakan sudah kembali.');
        }

        if ($item->booking->isCompleted()) {
            return back()->with('error', 'Denda tidak dapat dibuat untuk pesanan yang telah selesai.');
        }

        Fine::create([
            'booking_item_id' => $validated['booking_item_id'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'status' => FineStatus::Unpaid,
        ]);

        return back()->with('success', 'Denda berhasil ditambahkan.');
    }

    public function verifyPayment(Request $request, Fine $fine, FinePaymentService $paymentService): RedirectResponse
    {
        try {
            $result = $paymentService->confirmPaymentByAdmin($fine, Auth::user());

            return back()->with('success', $result['message']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectPayment(Request $request, Fine $fine, FinePaymentService $paymentService): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan pembayaran wajib diisi.',
        ]);

        try {
            $result = $paymentService->rejectPaymentByAdmin(
                fine: $fine,
                admin: Auth::user(),
                reason: $request->input('rejection_reason')
            );

            return back()->with('success', $result['message']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
