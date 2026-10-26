<?php

namespace App\Http\Controllers\Customer;

use App\Enums\MotorStatus;
use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\MotorCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $categories = MotorCategory::withCount(['motors' => function ($query) {
            $query->where('status', MotorStatus::Available);
        }])->get();

        $motorsQuery = Motor::with('category')
            ->where('status', MotorStatus::Available);

        if ($request->filled('category')) {
            $motorsQuery->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $motorsQuery->where(function ($q) use ($request) {
                $q->where('merk', 'like', "%{$request->search}%")
                  ->orWhere('model', 'like', "%{$request->search}%");
            });
        }

        $motors = $motorsQuery->latest()->paginate(8);

        return view('customer.home.index', compact('categories', 'motors'));
    }

    public function vehicles(Request $request): View
    {
        $categories = MotorCategory::withCount(['motors' => function ($query) {
            $query->where('status', MotorStatus::Available);
        }])->get();

        $motorsQuery = Motor::with('category')
            ->where('status', MotorStatus::Available);

        if ($request->filled('category')) {
            $motorsQuery->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $motorsQuery->where(function ($q) use ($request) {
                $q->where('merk', 'like', "%{$request->search}%")
                  ->orWhere('model', 'like', "%{$request->search}%");
            });
        }

        $motors = $motorsQuery->latest()->paginate(12);

        return view('customer.motors.index', compact('categories', 'motors'));
    }

    public function show(Motor $motor): View
    {
        $motor->load(['category', 'bookingItems.review']);

        $reviews = $motor->bookingItems()
            ->whereHas('review')
            ->with('review', 'booking.user')
            ->get()
            ->pluck('review');

        $averageRating = $reviews->avg('rating');

        return view('customer.motors.show', compact('motor', 'reviews', 'averageRating'));
    }
}
