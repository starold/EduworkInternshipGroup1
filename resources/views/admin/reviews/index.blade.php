@extends('layouts.admin')

@section('title', 'Manajemen Review')
@section('page-title', 'Manajemen Review Pelanggan')

@push('styles')
<style>
.filter-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; padding:20px; margin-bottom:20px; }
.filter-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)) 120px; gap:16px; align-items:flex-end; }
.form-field label { display:block; font-size:12px; font-weight:700; color:#4a4a5a; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px; }
.form-field input, .form-field select { width:100%; padding:9px 12px; border:1.5px solid #e2e4e9; border-radius:8px; font-size:13px; }
.btn-filter { width:100%; padding:10px; background:#3662ed; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; }
.btn-filter:hover { background:#244ed1; }

.table-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; overflow:hidden; }
.custom-table { width:100%; border-collapse:collapse; font-size:13px; }
.custom-table th { background:#f8f9fb; padding:14px 16px; text-align:left; color:#1a1a2e; font-weight:700; border-bottom:1px solid #e2e4e9; }
.custom-table td { padding:14px 16px; border-bottom:1px solid #f0f0f5; color:#334; vertical-align:middle; }
.custom-table tr:last-child td { border-bottom:none; }

.stars { color:#efa928; font-size:15px; letter-spacing:1px; }
.pagination-wrap { padding:16px 20px; border-top:1px solid #e2e4e9; }
</style>
@endpush

@section('content')
<!-- Filter bar -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.reviews.index') }}">
        <div class="filter-grid">
            <div class="form-field">
                <label>Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Komentar, pelanggan, motor...">
            </div>
            <div class="form-field">
                <label>Rating</label>
                <select name="rating">
                    <option value="">Semua Rating</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} Bintang ({{ str_repeat('★', $i) }})
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="btn-filter">Filter</button>
            </div>
        </div>
    </form>
</div>

<!-- Table -->
<div class="table-card">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Booking</th>
                <th>Pelanggan</th>
                <th>Motor</th>
                <th>Rating</th>
                <th>Komentar</th>
                <th>Waktu Review</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                @php
                    $booking = $review->bookingItem?->booking;
                    $motor = $review->bookingItem?->motor;
                @endphp
                <tr>
                    <td>
                        @if($booking)
                            <a href="{{ route('admin.bookings.show', $booking) }}" style="color:#3662ed;font-weight:700">
                                {{ $booking->booking_code }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $booking?->user?->name ?? '-' }}</td>
                    <td>
                        @if($motor)
                            <strong>{{ $motor->merk }} {{ $motor->model }}</strong>
                            <small style="color:#889;display:block">{{ $motor->category?->name }} · {{ $motor->plate_number }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </span>
                        <span style="font-weight:700;margin-left:4px">({{ $review->rating }}/5)</span>
                    </td>
                    <td style="max-width:320px">
                        {{ $review->comment ?: 'Tidak ada komentar.' }}
                    </td>
                    <td><small>{{ $review->created_at->format('d/m/Y H:i') }}</small></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:#8a8a9a">
                        Belum ada review pelanggan yang masuk.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrap">
        {{ $reviews->withQueryString()->links() }}
    </div>
</div>
@endsection
