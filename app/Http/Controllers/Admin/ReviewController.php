<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::with(['bookingItem.booking.user', 'bookingItem.motor.category'])
            ->when($request->filled('rating'), fn ($query) => $query->where('rating', $request->input('rating')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->input('search').'%';

                $query->where(function ($sub) use ($search) {
                    $sub->where('comment', 'like', $search)
                        ->orWhereHas('bookingItem.booking', fn ($b) => $b->where('booking_code', 'like', $search))
                        ->orWhereHas('bookingItem.booking.user', fn ($u) => $u->where('name', 'like', $search))
                        ->orWhereHas('bookingItem.motor', fn ($m) => $m->where('model', 'like', $search)->orWhere('merk', 'like', $search));
                });
            })
            ->latest()
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }
}
