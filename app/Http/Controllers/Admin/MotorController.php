<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\MotorCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MotorController extends Controller
{
    public function index(Request $request): View
    {
        $motors = Motor::with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->input('search').'%';

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('merk', 'like', $search)
                        ->orWhere('model', 'like', $search)
                        ->orWhere('plate_number', 'like', $search);
                });
            })
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category, fn ($q) => $q->where('motor_category_id', $request->category))
            ->latest()
            ->paginate(15);

        $categories = MotorCategory::all();

        return view('admin.motors.index', compact('motors', 'categories'));
    }

    public function create(): View
    {
        $categories = MotorCategory::all();

        return view('admin.motors.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'motor_category_id' => ['required', 'exists:motor_categories,id'],
            'merk' => ['required', 'string', 'max:100'],
            'warna' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'plate_number' => ['required', 'string', 'max:20', 'unique:motors'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:available,rented,maintenance,inactive'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('motors', 'public');
        }

        Motor::create($validated);

        return redirect()->route('admin.motors.index')
            ->with('success', 'Motor berhasil ditambahkan.');
    }

    public function edit(Motor $motor): View
    {
        $categories = MotorCategory::all();

        return view('admin.motors.edit', compact('motor', 'categories'));
    }

    public function update(Request $request, Motor $motor): RedirectResponse
    {
        $validated = $request->validate([
            'motor_category_id' => ['required', 'exists:motor_categories,id'],
            'merk' => ['required', 'string', 'max:100'],
            'warna' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'plate_number' => ['required', 'string', 'max:20', 'unique:motors,plate_number,'.$motor->id],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:available,rented,maintenance,inactive'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('motors', 'public');
        }

        $motor->update($validated);

        return redirect()->route('admin.motors.index')
            ->with('success', 'Motor berhasil diperbarui.');
    }

    public function destroy(Motor $motor): RedirectResponse
    {
        if ($motor->bookingItems()->exists()) {
            return back()->with('error', 'Motor tidak dapat dihapus karena memiliki riwayat pemesanan.');
        }

        $motor->delete();

        return redirect()->route('admin.motors.index')
            ->with('success', 'Motor berhasil dihapus.');
    }
}
