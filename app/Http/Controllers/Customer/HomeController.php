<?php

namespace App\Http\Controllers\Customer;

use App\Enums\MotorStatus;
use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\MotorCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $recommendations = Motor::with('category')
            ->where('id', '!=', $motor->id)
            ->where('status', MotorStatus::Available)
            ->take(8)
            ->get();

        return view('customer.motors.show', compact('motor', 'reviews', 'averageRating', 'recommendations'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'ktp_number' => ['nullable', 'string', 'max:20'],
            'sim_number' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Auth::user()->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
