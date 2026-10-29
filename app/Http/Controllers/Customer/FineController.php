<?php

namespace App\Http\Controllers\Customer;

use App\Enums\FineStatus;
use App\Enums\FineType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PayFineRequest;
use App\Models\Booking;
use App\Models\Fine;
use App\Services\FinePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FineController extends Controller
{
    /**
     * Reusable list of payment methods identical to rental payments.
     *
     * @var array<int, array{name: string, number: string, holder: string}>
     */
    public const AVAILABLE_BANKS = [
        ['name' => 'BCA', 'number' => '1234567890', 'holder' => 'ED RENT'],
        ['name' => 'BRI', 'number' => '0987654321', 'holder' => 'ED RENT'],
        ['name' => 'Mandiri', 'number' => '1122334455', 'holder' => 'ED RENT'],
        ['name' => 'GoPay', 'number' => '081234567890', 'holder' => 'ED RENT'],
    ];

    public function index(Request $request): View
    {
        $userId = Auth::id();

        $fines = Fine::whereHas('bookingItem.booking', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['bookingItem.booking', 'bookingItem.motor', 'payment'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(10);

        $totalUnpaid = Fine::whereHas('bookingItem.booking', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('status', FineStatus::Unpaid)
            ->sum('amount');

        $types = FineType::cases();
        $statuses = FineStatus::cases();

        return view('customer.fines.index', compact('fines', 'totalUnpaid', 'types', 'statuses'));
    }

    /**
     * Show dedicated payment page for a fine (identical UI to rental payment).
     */
    public function showPayment(Fine $fine): View
    {
        $booking = $fine->bookingItem?->booking;

        abort_if(! $booking || $booking->user_id !== Auth::id(), 403, 'Akses tidak diizinkan.');

        $fine->load(['bookingItem.motor', 'bookingItem.booking', 'payment']);

        $banks = self::AVAILABLE_BANKS;

        return view('customer.fines.pay', compact('fine', 'booking', 'banks'));
    }

    /**
     * Upload proof of payment for a fine.
     */
    public function uploadProof(Request $request, Fine $fine, FinePaymentService $paymentService): RedirectResponse
    {
        $booking = $fine->bookingItem?->booking;

        abort_if(! $booking || $booking->user_id !== Auth::id(), 403, 'Akses tidak diizinkan.');

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        try {
            $result = $paymentService->submitPaymentProof(
                fine: $fine,
                user: Auth::user(),
                paymentMethod: $request->input('payment_method'),
                proofFile: $request->file('payment_proof')
            );

            return redirect()->route('customer.fines.payment', $fine)
                ->with('success', $result['message']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Backward-compatible payment handler.
     */
    public function pay(Request $request, Fine $fine, FinePaymentService $paymentService): RedirectResponse
    {
        $booking = $fine->bookingItem?->booking;

        if (! $booking || $booking->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke denda ini.');
        }

        if ($request->hasFile('payment_proof')) {
            return $this->uploadProof($request, $fine, $paymentService);
        }

        $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        try {
            $result = $paymentService->payFine(
                fine: $fine,
                user: Auth::user(),
                paymentMethod: $request->input('payment_method')
            );

            return back()->with('success', $result['message']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function payAll(PayFineRequest $request, Booking $booking, FinePaymentService $paymentService): RedirectResponse
    {
        try {
            $result = $paymentService->payAllFinesForBooking(
                booking: $booking,
                user: Auth::user(),
                paymentMethod: $request->input('payment_method')
            );

            return back()->with('success', $result['message']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
