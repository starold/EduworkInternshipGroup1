@extends('layouts.admin')

@section('title', 'Pemesanan')

@push('styles')
<style>
.filter-panel { display:grid; grid-template-columns:1fr 1fr 1.2fr 1.5fr auto; align-items:end; gap:10px; padding:14px; margin-bottom:15px; background:#fff; border:1px solid #edf0f5; border-radius:9px; box-shadow:var(--shadow); }
.filter-field { min-width:0; }
.filter-field label { display:block; margin-bottom:5px; color:#8794a9; font-size:10px; font-weight:600; }
.filter-input { width:100%; height:35px; padding:0 10px; border:1px solid #e0e6ef; border-radius:5px; color:#394861; background:#fff; font-size:11px; outline:none; }
.filter-input:focus { border-color:#7292f1; box-shadow:0 0 0 2px #edf2ff; }
.filter-actions { display:flex; gap:7px; }
.btn-filter, .btn-reset { display:inline-flex; height:35px; justify-content:center; align-items:center; padding:0 13px; border-radius:5px; font-size:11px; font-weight:600; white-space:nowrap; }
.btn-filter { border:0; background:var(--primary); color:#fff; cursor:pointer; }
.btn-filter:hover { background:var(--primary-dark); }
.btn-reset { border:1px solid #e0e6ef; background:#fff; color:#71809a; }
.results-count { margin:0 0 10px; color:#8794a9; font-size:11px; }
.booking-list { display:grid; gap:11px; }
.booking-card { display:grid; grid-template-columns:minmax(210px,1.35fr) minmax(110px,.9fr) minmax(110px,.8fr) minmax(90px,.65fr) minmax(110px,.7fr) auto; align-items:center; gap:12px; min-height:78px; padding:12px 14px; background:#fff; border:1px solid #edf0f5; border-radius:9px; box-shadow:var(--shadow); }
.booking-motor { display:flex; align-items:center; gap:11px; min-width:0; }
.motor-image { width:55px; height:49px; flex:0 0 auto; object-fit:contain; border-radius:6px; background:#f7f8fb; }
.booking-label { margin-bottom:4px; color:#93a2bb; font-size:9px; }
.booking-value { color:var(--text-dark); font-size:10px; font-weight:600; line-height:1.4; overflow-wrap:anywhere; }
.booking-subvalue { margin-top:3px; color:#7f8ea5; font-size:9px; }
.badge { display:inline-flex; align-items:center; justify-content:center; min-width:64px; padding:4px 10px; border-radius:30px; font-size:9px; font-weight:600; white-space:nowrap; }
.badge-pending { background:#e8effb; color:#46648f; }
.badge-confirmed { background:#eff9d1; color:#53781d; }
.badge-cancelled { background:#ffe9db; color:#bf603b; }
.badge-rejected { background:#ffe9db; color:#c34d37; }
.badge-completed { background:#eaf1fa; color:#587497; }
.btn-update { display:inline-flex; align-items:center; justify-content:center; min-width:70px; padding:8px 12px; border:0; border-radius:4px; color:#fff; background:var(--primary); font-size:10px; font-weight:600; }
.btn-update:hover { background:var(--primary-dark); }
.empty-state { padding:50px 18px; border:1px solid #edf0f5; border-radius:9px; color:var(--text-muted); background:#fff; text-align:center; font-size:12px; }
.pagination-wrap { margin-top:16px; }
@media(max-width:1050px) {
    .filter-panel { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .booking-card { grid-template-columns:minmax(190px,1.4fr) minmax(100px,1fr) minmax(100px,1fr) auto; }
    .booking-card > .booking-date, .booking-card > .booking-price { display:none; }
}
@media(max-width:620px) {
    .filter-panel { grid-template-columns:1fr 1fr; }
    .filter-field.search-field { grid-column:1 / -1; }
    .filter-actions { grid-column:1 / -1; }
    .booking-card { grid-template-columns:minmax(0,1fr) auto; gap:10px; }
    .booking-card > .booking-code { grid-column:1; grid-row:2; }
    .booking-card > .booking-status { grid-column:2; grid-row:1 / span 2; text-align:right; }
    .booking-card > .booking-date, .booking-card > .booking-price { display:block; grid-column:1; }
    .booking-card > .booking-price { grid-row:4; }
    .booking-card > .booking-action { grid-column:2; grid-row:4; }
}
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <h1>Daftar Pesanan</h1>
        <p>Kelola dan perbarui status pemesanan kendaraan.</p>
    </div>
</div>

<form method="GET" action="{{ route('admin.bookings.index') }}" class="filter-panel">
    <div class="filter-field">
        <label for="status-filter">Status</label>
        <select id="status-filter" name="status" class="filter-input">
            <option value="">Semua</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
        </select>
    </div>
    <div class="filter-field">
        <label for="category-filter">Tipe kendaraan</label>
        <select id="category-filter" name="category" class="filter-input">
            <option value="">Semua</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-field">
        <label for="date-filter">Tanggal sewa</label>
        <input id="date-filter" type="date" name="start_date" class="filter-input" value="{{ request('start_date') }}">
    </div>
    <div class="filter-field search-field">
        <label for="search-filter">Cari kendaraan / pemesan</label>
        <input id="search-filter" type="search" name="search" class="filter-input" placeholder="Kode, nama, motor..." value="{{ request('search') }}">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn-filter">Search</button>
        @if(request()->hasAny(['status', 'category', 'start_date', 'search']))
            <a href="{{ route('admin.bookings.index') }}" class="btn-reset">Reset</a>
        @endif
    </div>
</form>

<p class="results-count">Menampilkan {{ $bookings->total() }} pesanan</p>

<div class="booking-list">
    @forelse($bookings as $booking)
        @php
            $item = $booking->items->first();
            $motor = $item?->motor;
        @endphp
        <article class="booking-card">
            <div class="booking-motor">
                @if($motor)
                    <img src="{{ $motor->image_url }}" alt="{{ $motor->model }}" class="motor-image">
                @else
                    <span class="motor-image"></span>
                @endif
                <div>
                    <div class="booking-label">{{ $motor?->category?->name ?? 'Kendaraan' }}</div>
                    <div class="booking-value">{{ $motor ? $motor->merk.' '.$motor->model : 'Kendaraan tidak tersedia' }}</div>
                    <div class="booking-subvalue">{{ $booking->user->name }}</div>
                </div>
            </div>
            <div class="booking-code">
                <div class="booking-label">Kode Booking</div>
                <div class="booking-value">{{ $booking->booking_code }}</div>
            </div>
            <div class="booking-date">
                <div class="booking-label">Tanggal Booking</div>
                <div class="booking-value">{{ $booking->start_date->format('d M Y') }}</div>
                <div class="booking-subvalue">{{ $item?->total_days ?? 0 }} hari</div>
            </div>
            <div class="booking-status">
                <div class="booking-label">Status</div>
                <span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span>
            </div>
            <div class="booking-price">
                <div class="booking-label">Harga</div>
                <div class="booking-value">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</div>
            </div>
            <div class="booking-action">
                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-update">Update</a>
            </div>
        </article>
    @empty
        <div class="empty-state">Tidak ada pesanan yang cocok dengan filter ini.</div>
    @endforelse
</div>

<div class="pagination-wrap">{{ $bookings->withQueryString()->links() }}</div>
@endsection
