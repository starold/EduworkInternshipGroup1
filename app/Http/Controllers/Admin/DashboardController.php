<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Motor;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_motors' => Motor::count(),
            'available_motors' => Motor::where('status', 'available')->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', BookingStatus::Pending)->count(),
            'confirmed_bookings' => Booking::where('status', BookingStatus::Confirmed)->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_revenue' => Payment::where('status', PaymentStatus::Paid)->sum('amount'),
            'pending_payments' => Payment::where('status', PaymentStatus::Pending)->count(),
        ];

        $recentBookings = Booking::with(['user', 'items.motor', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentBookings'));
    }
}
