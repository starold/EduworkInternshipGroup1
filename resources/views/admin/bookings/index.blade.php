@extends('layouts.admin')

@section('title', 'Pemesanan')
@section('page-title', 'Daftar Pemesanan')

@push('styles')
<style>
.filter-bar { display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap; }
.filter-input { padding:9px 14px; border:1.5px solid #e2e4e9; border-radius:8px; font-size:14px; font-family:'Inter',sans-serif; outline:none; }
.filter-input:focus { border-color:#4758D6; }
.btn-filter { padding:10px 18px; background:#f8f9fb; color:#4a4a5a; border:1.5px solid #e2e4e9; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; }
.table-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; overflow:hidden; }
table { width:100%; border-collapse:collapse; }
th { background:#f8f9fb; padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#8a8a9a; text-transform:uppercase; letter-spacing:.5px; }
td { padding:14px 16px; font-size:14px; color:#4a4a5a; border-top:1px solid #f0f0f5; vertical-align:middle; }
tr:hover td { background:#fafbfc; }
.badge { display:inline-flex; padding:4px 10px; border-radius:50px; font-size:11px; font-weight:700; }
.badge-pending { background:#fff3cd; color:#856404; }
.badge-confirmed { background:#d4edda; color:#155724; }
.badge-cancelled { background:#e2e3e5; color:#383d41; }
.badge-rejected { background:#f8d7da; color:#721c24; }
.badge-completed { background:#cce5ff; color:#004085; }
.action-link { font-size:13px; color:#4758D6; font-weight:600; padding:6px 14px; border:1.5px solid #4758D6; border-radius:6px; }
.action-link:hover { background:#4758D6; color:#fff; }
</style>
@endpush

@section('content')
<form method="GET" class="filter-bar">
    <input type="text" name="search" class="filter-input" placeholder="Cari kode/nama..." value="{{ request('search') }}" style="flex:1;min-width:200px">
    <select name="status" class="filter-input">
        <option value="">Semua Status</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
    </select>
    <button type="submit" class="btn-filter">Filter</button>
</form>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Motor</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td><strong>{{ $booking->booking_code }}</strong></td>
                    <td>
                        <strong>{{ $booking->user->name }}</strong><br>
                        <small style="color:#8a8a9a">{{ $booking->user->phone }}</small>
                    </td>
                    <td>
                        @foreach($booking->items as $item)
                            {{ $item->motor->merk }} {{ $item->motor->model }}<br>
                        @endforeach
                    </td>
                    <td>
                        {{ $booking->start_date->format('d M Y') }}<br>
                        <small style="color:#8a8a9a">{{ $booking->items->sum('total_days') }} hari</small>
                    </td>
                    <td><strong>Rp{{ number_format($booking->total_price, 0, ',', '.') }}</strong></td>
                    <td><span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span></td>
                    <td><a href="{{ route('admin.bookings.show', $booking) }}" class="action-link">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:#8a8a9a">Tidak ada pemesanan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $bookings->withQueryString()->links() }}</div>
@endsection
