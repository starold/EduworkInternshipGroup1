@extends('layouts.admin')

@section('title', 'Kelola Motor')

@push('styles')
<style>
.page-heading .btn-add { display:inline-flex; align-items:center; gap:7px; padding:9px 13px; border-radius:5px; background:var(--primary); color:#fff; font-size:11px; font-weight:600; white-space:nowrap; }
.page-heading .btn-add:hover { background:var(--primary-dark); }
.filter-panel { display:grid; grid-template-columns:1fr 1fr 1.4fr auto; align-items:end; gap:10px; padding:14px; margin-bottom:13px; background:#fff; border:1px solid #edf0f5; border-radius:9px; box-shadow:var(--shadow); }
.filter-field label { display:block; margin-bottom:5px; color:#8794a9; font-size:10px; font-weight:600; }
.filter-input { width:100%; height:35px; padding:0 10px; border:1px solid #e0e6ef; border-radius:5px; color:#394861; background:#fff; font-size:11px; outline:none; }
.filter-input:focus { border-color:#7292f1; box-shadow:0 0 0 2px #edf2ff; }
.filter-actions { display:flex; gap:7px; }
.btn-filter, .btn-reset { display:inline-flex; height:35px; justify-content:center; align-items:center; padding:0 13px; border-radius:5px; font-size:11px; font-weight:600; white-space:nowrap; }
.btn-filter { border:0; background:var(--primary); color:#fff; cursor:pointer; }
.btn-filter:hover { background:var(--primary-dark); }
.btn-reset { border:1px solid #e0e6ef; background:#fff; color:#71809a; }
.results-bar { display:flex; justify-content:space-between; margin:0 0 12px; color:#8794a9; font-size:10px; }
.motor-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; }
.motor-card { min-width:0; overflow:hidden; background:#fff; border:1px solid #edf0f5; border-radius:8px; box-shadow:var(--shadow); transition:transform .15s, box-shadow .15s; }
.motor-card:hover { transform:translateY(-2px); box-shadow:0 7px 18px rgba(28,46,79,.1); }
.motor-photo { display:flex; align-items:center; justify-content:center; height:172px; padding:12px; background:#fff; }
.motor-photo img { width:100%; height:100%; object-fit:contain; }
.motor-card.is-rented .motor-photo img { opacity:.42; }
.motor-card-body { padding:0 13px 13px; }
.motor-card-name { color:var(--text-dark); font-size:12px; font-weight:700; }
.motor-card-category { margin-top:4px; color:#8b9bb4; font-size:10px; }
.motor-meta { display:flex; justify-content:space-between; align-items:center; gap:8px; margin-top:13px; padding-bottom:11px; border-bottom:1px solid #f0f2f6; }
.motor-plate { color:#91a1bb; font-size:9px; }
.motor-status { display:inline-flex; align-items:center; gap:4px; font-size:9px; white-space:nowrap; }
.motor-status::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
.motor-status.available { color:#36b753; }
.motor-status.rented { color:#aab8ca; }
.motor-status.maintenance { color:#e4a032; }
.motor-status.inactive { color:#e06458; }
.motor-card-footer { display:flex; justify-content:space-between; align-items:center; gap:8px; padding-top:10px; }
.motor-price { color:var(--text-dark); font-size:12px; font-weight:800; white-space:nowrap; }
.motor-price small { color:#8b9bb4; font-size:9px; font-weight:500; }
.motor-actions { display:flex; align-items:center; gap:6px; }
.btn-detail { display:inline-flex; justify-content:center; padding:7px 13px; border:0; border-radius:4px; background:var(--primary); color:#fff; font-size:9px; font-weight:600; }
.btn-detail:hover { background:var(--primary-dark); }
.btn-delete { min-height:29px; padding:0 9px; display:inline-flex; align-items:center; justify-content:center; border:1px solid #f6d2cc; border-radius:4px; background:#fff; color:#de6255; font-size:9px; font-weight:600; white-space:nowrap; cursor:pointer; }
.btn-delete:hover { background:#fff0ee; }
.empty-state { grid-column:1 / -1; padding:50px 18px; border:1px solid #edf0f5; border-radius:9px; color:var(--text-muted); background:#fff; text-align:center; font-size:12px; }
.pagination-wrap { margin-top:18px; }
@media(max-width:1000px) { .motor-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media(max-width:650px) {
    .filter-panel { grid-template-columns:1fr 1fr; }
    .filter-field.search-field { grid-column:1 / -1; }
    .filter-actions { grid-column:1 / -1; }
    .motor-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:9px; }
    .motor-photo { height:130px; }
    .motor-card-body { padding:0 10px 10px; }
    .motor-card-footer { align-items:flex-start; flex-direction:column; }
    .motor-actions { width:100%; }
    .btn-detail { flex:1; }
}
@media(max-width:390px) { .motor-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <h1>Daftar Kendaraan</h1>
        <p>Kelola armada, kategori, harga, dan ketersediaan kendaraan.</p>
    </div>
    <a href="{{ route('admin.motors.create') }}" class="btn-add"><span>＋</span> Tambah</a>
</div>

<form method="GET" action="{{ route('admin.motors.index') }}" class="filter-panel">
    <div class="filter-field">
        <label for="motor-status-filter">Status</label>
        <select id="motor-status-filter" name="status" class="filter-input">
            <option value="">Semua</option>
            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="rented" {{ request('status') === 'rented' ? 'selected' : '' }}>Disewa</option>
            <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Perawatan</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
    </div>
    <div class="filter-field">
        <label for="motor-category-filter">Tipe kendaraan</label>
        <select id="motor-category-filter" name="category" class="filter-input">
            <option value="">Semua</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-field search-field">
        <label for="motor-search">Cari kendaraan</label>
        <input id="motor-search" type="search" name="search" class="filter-input" placeholder="Nama motor atau nomor plat" value="{{ request('search') }}">
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn-filter">Search</button>
        @if(request()->hasAny(['status', 'category', 'search']))
            <a href="{{ route('admin.motors.index') }}" class="btn-reset">Reset</a>
        @endif
    </div>
</form>

<div class="results-bar">
    <span>{{ $motors->total() }} kendaraan</span>
    <span>Halaman {{ $motors->currentPage() }} dari {{ $motors->lastPage() }}</span>
</div>

<div class="motor-grid">
    @forelse($motors as $motor)
        <article class="motor-card {{ $motor->status->value === 'rented' ? 'is-rented' : '' }}">
            <div class="motor-photo">
                <img src="{{ $motor->image_url }}" alt="{{ $motor->merk }} {{ $motor->model }}">
            </div>
            <div class="motor-card-body">
                <h2 class="motor-card-name">{{ $motor->merk }} {{ $motor->model }}</h2>
                <div class="motor-card-category">{{ $motor->category->name }}</div>
                <div class="motor-meta">
                    <span class="motor-plate">▣ {{ $motor->plate_number }}</span>
                    <span class="motor-status {{ $motor->status->value }}">{{ $motor->status->label() }}</span>
                </div>
                <div class="motor-card-footer">
                    <div class="motor-price">Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}<small>/hari</small></div>
                    <div class="motor-actions">
                        <a href="{{ route('admin.motors.edit', $motor) }}" class="btn-detail">Detail</a>
                        <form method="POST" action="{{ route('admin.motors.destroy', $motor) }}" onsubmit="return confirm('Hapus kendaraan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" aria-label="Hapus {{ $motor->merk }} {{ $motor->model }}">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="empty-state">Tidak ada kendaraan yang cocok dengan filter ini.</div>
    @endforelse
</div>

<div class="pagination-wrap">{{ $motors->withQueryString()->links() }}</div>
@endsection
