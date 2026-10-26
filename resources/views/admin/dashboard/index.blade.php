@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
.stats-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:20px; margin-bottom:32px; }
.stat-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; padding:24px; position:relative; overflow:hidden; }
.stat-card::before { content:''; position:absolute; top:0; right:0; width:80px; height:80px; border-radius:0 12px 0 100%; }
.stat-card.blue::before { background:rgba(71,88,214,.1); }
.stat-card.green::before { background:rgba(37,211,102,.1); }
.stat-card.orange::before { background:rgba(245,158,11,.1); }
.stat-card.purple::before { background:rgba(139,92,246,.1); }
.stat-label { font-size:13px; color:#8a8a9a; font-weight:500; margin-bottom:10px; }
.stat-value { font-size:32px; font-weight:800; color:#1a1a2e; margin-bottom:4px; }
.stat-sub { font-size:13px; color:#8a8a9a; }
.stat-icon { position:absolute; top:20px; right:20px; font-size:28px; }

.section-title { font-size:18px; font-weight:700; color:#1a1a2e; margin-bottom:16px; }
.table-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; overflow:hidden; }
table { width:100%; border-collapse:collapse; }
th { background:#f8f9fb; padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#8a8a9a; text-transform:uppercase; letter-spacing:.5px; }
td { padding:14px 16px; font-size:14px; color:#4a4a5a; border-top:1px solid #f0f0f5; vertical-align:middle; }
tr:last-child td { border-top:1px solid #f0f0f5; }
tr:hover td { background:#fafbfc; }
.badge { display:inline-flex; padding:4px 10px; border-radius:50px; font-size:11px; font-weight:700; }
.badge-pending { background:#fff3cd; color:#856404; }
.badge-confirmed { background:#d4edda; color:#155724; }
.badge-cancelled { background:#e2e3e5; color:#383d41; }
.badge-rejected { background:#f8d7da; color:#721c24; }
.badge-completed { background:#cce5ff; color:#004085; }
.action-link { font-size:13px; color:#4758D6; font-weight:600; }
.action-link:hover { text-decoration:underline; }
.empty-td { text-align:center; padding:40px; color:#8a8a9a; font-size:14px; }

@media(max-width:1100px) { .stats-grid { grid-template-columns:repeat(2,1fr); } }
</style>
@endpush

@section('content')
<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-label">Total Motor</div>
        <div class="stat-value">{{ $stats['total_motors'] }}</div>
        <div class="stat-sub">{{ $stats['available_motors'] }} tersedia</div>
        <div class="stat-icon">🏍</div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">Total Pesanan</div>
        <div class="stat-value">{{ $stats['total_bookings'] }}</div>
        <div class="stat-sub">{{ $stats['pending_bookings'] }} menunggu</div>
        <div class="stat-icon">📋</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-label">Total Pelanggan</div>
        <div class="stat-value">{{ $stats['total_customers'] }}</div>
        <div class="stat-sub">Pelanggan terdaftar</div>
        <div class="stat-icon">👤</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value" style="font-size:22px">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
        <div class="stat-sub">{{ $stats['pending_payments'] }} pending</div>
        <div class="stat-icon">💰</div>
    </div>
</div>

<!-- Recent Bookings -->
<h2 class="section-title">Pesanan Terbaru</h2>
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Kode Booking</th>
                <th>Pelanggan</th>
                <th>Motor</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentBookings as $booking)
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
                    <td><strong>Rp{{ number_format($booking->total_price, 0, ',', '.') }}</strong></td>
                    <td><span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span></td>
                    <td><a href="{{ route('admin.bookings.show', $booking) }}" class="action-link">Detail →</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-td">Belum ada pesanan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
