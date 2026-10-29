@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
.dashboard-grid { display:grid; grid-template-columns:minmax(0, 1fr) minmax(340px, .92fr); gap:18px; align-items:start; }
.dashboard-left { display:grid; gap:16px; }
.panel { background:#fff; border:1px solid #edf0f5; border-radius:10px; padding:18px; box-shadow:var(--shadow); }
.panel-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; }
.panel-title { display:flex; align-items:center; gap:9px; color:var(--text-dark); font-size:14px; font-weight:700; }
.panel-title::before { content:''; width:7px; height:7px; border-radius:50%; background:#63a6f2; box-shadow:0 0 0 3px #eaf3ff; }
.panel-link { color:var(--primary); font-size:10px; font-weight:600; }
.popular-layout { display:grid; grid-template-columns:145px 1fr; gap:20px; align-items:center; }
.donut { width:132px; height:132px; position:relative; border-radius:50%; background:conic-gradient(#103b70 0 25%, #1b5794 25% 48%, #2878c2 48% 68%, #55a1e5 68% 84%, #aad4f5 84% 100%); }
.donut::after { content:''; position:absolute; inset:14px; border-radius:50%; background:#fff; }
.donut-center { position:absolute; z-index:1; inset:0; display:flex; flex-direction:column; justify-content:center; align-items:center; color:var(--text-dark); font-size:19px; font-weight:800; }
.donut-center small { margin-top:3px; color:var(--text-muted); font-size:10px; font-weight:500; }
.popular-list { display:grid; gap:12px; }
.popular-item { display:grid; grid-template-columns:minmax(0, 1fr) auto; gap:8px; align-items:center; color:#70809a; font-size:10px; }
.popular-name { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.popular-count { color:var(--text-dark); font-weight:600; }
.popular-empty { color:var(--text-muted); font-size:12px; }
.fleet-list { display:grid; }
.fleet-row { display:flex; justify-content:space-between; align-items:center; padding:11px 0; border-top:1px solid #edf0f5; color:var(--text-dark); font-size:12px; }
.fleet-row:first-child { border-top:0; padding-top:0; }
.fleet-row span:last-child { color:#71809a; }
.revenue-summary { display:flex; justify-content:space-between; align-items:center; gap:14px; border-top:1px solid #e8edf5; padding-top:15px; }
.revenue-label { color:var(--text-dark); font-size:12px; font-weight:700; }
.revenue-caption { margin-top:4px; color:var(--text-muted); font-size:10px; }
.revenue-total { color:var(--text-dark); font-size:19px; font-weight:800; text-align:right; }
.revenue-pending { margin-top:5px; color:var(--text-muted); font-size:10px; font-weight:500; }
.recent-panel { padding:18px 16px; }
.recent-list { display:grid; }
.recent-item { display:grid; grid-template-columns:64px minmax(0, 1fr) auto; align-items:center; gap:12px; min-height:69px; border-top:1px solid #edf0f5; }
.recent-item:first-child { border-top:0; }
.recent-image { width:62px; height:48px; object-fit:contain; border-radius:7px; background:#f7f8fb; }
.recent-name { color:var(--text-dark); font-size:11px; font-weight:700; }
.recent-customer { margin-top:4px; color:var(--text-muted); font-size:10px; }
.recent-meta { text-align:right; }
.recent-date { color:#91a5c4; font-size:9px; }
.recent-price { margin-top:4px; color:var(--text-dark); font-size:11px; font-weight:700; }
.status-chip { display:inline-flex; margin-top:4px; padding:3px 7px; border-radius:30px; background:#e9f1ff; color:#4167b5; font-size:9px; font-weight:600; }
.empty-state { padding:30px 0; color:var(--text-muted); text-align:center; font-size:12px; }
@media(max-width:1050px) { .dashboard-grid { grid-template-columns:1fr; } }
@media(max-width:600px) {
    .popular-layout { grid-template-columns:105px 1fr; gap:15px; }
    .donut { width:100px; height:100px; }
    .donut::after { inset:11px; }
    .donut-center { font-size:16px; }
    .recent-item { grid-template-columns:52px minmax(0, 1fr) auto; gap:8px; }
    .recent-image { width:50px; height:42px; }
    .recent-panel { padding:15px 12px; }
}
</style>
@endpush

@section('content')
@php
    $popularMotorTotal = $popularMotors->sum('booking_items_count');
    $chartColors = ['#103b70', '#1b5794', '#2878c2', '#55a1e5', '#aad4f5'];
    $chartStops = [];
    $chartPosition = 0;

    foreach ($popularMotors as $index => $motor) {
        if ($popularMotorTotal === 0) {
            break;
        }

        $nextPosition = $chartPosition + ($motor->booking_items_count / $popularMotorTotal * 100);
        $chartStops[] = $chartColors[$index].' '.$chartPosition.'% '.$nextPosition.'%';
        $chartPosition = $nextPosition;
    }

    $popularMotorChart = $chartStops
        ? 'conic-gradient('.implode(', ', $chartStops).')'
        : 'conic-gradient(#e7edf7 0% 100%)';
@endphp
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>Ringkasan aktivitas dan performa ED.RENT.</p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-left">
        <section class="panel">
            <div class="panel-heading">
                <h2 class="panel-title">5 Motor Paling Sering Disewa</h2>
                <a href="{{ route('admin.motors.index') }}" class="panel-link">Lihat kendaraan</a>
            </div>
            <div class="popular-layout">
                <div class="donut" style="background:{{ $popularMotorChart }}">
                    <div class="donut-center">{{ $popularMotorTotal }}<small>Sewa motor</small></div>
                </div>
                <div class="popular-list">
                    @forelse($popularMotors as $motor)
                        <div class="popular-item">
                            <span class="popular-name">{{ $motor->merk }} {{ $motor->model }}</span>
                            <span class="popular-count">{{ $motor->booking_items_count }}</span>
                        </div>
                    @empty
                        <p class="popular-empty">Belum ada data penyewaan motor.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-heading">
                <h2 class="panel-title">Jumlah Kendaraan</h2>
                <a href="{{ route('admin.motors.index') }}" class="panel-link">{{ $stats['total_motors'] }}</a>
            </div>
            <div class="fleet-list">
                <div class="fleet-row"><span>Tersedia</span><span>{{ $stats['available_motors'] }}</span></div>
                <div class="fleet-row"><span>Jumlah booking</span><span>{{ $stats['total_bookings'] }}</span></div>
                <div class="fleet-row"><span>Sedang disewa</span><span>{{ $stats['rented_motors'] }}</span></div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-heading">
                <h2 class="panel-title">Pendapatan</h2>
                <span class="panel-link">Semua waktu</span>
            </div>
            <div class="revenue-summary">
                <div>
                    <div class="revenue-label">Total Pendapatan</div>
                    <div class="revenue-caption">Seluruh pembayaran terverifikasi</div>
                </div>
                <div class="revenue-total">
                    Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}
                    <div class="revenue-pending">{{ $stats['pending_payments'] }} pembayaran menunggu</div>
                </div>
            </div>
        </section>
    </div>

    <section class="panel recent-panel">
        <div class="panel-heading">
            <h2 class="panel-title">Pesanan Terakhir</h2>
            <a href="{{ route('admin.bookings.index') }}" class="panel-link">Lihat semua</a>
        </div>
        <div class="recent-list">
            @forelse($recentBookings as $booking)
                @php($motor = $booking->items->first()?->motor)
                <a href="{{ route('admin.bookings.show', $booking) }}" class="recent-item">
                    @if($motor)
                        <img src="{{ $motor->image_url }}" alt="{{ $motor->model }}" class="recent-image">
                    @else
                        <span class="recent-image"></span>
                    @endif
                    <div>
                        <div class="recent-name">{{ $motor ? $motor->merk.' '.$motor->model : 'Kendaraan tidak tersedia' }}</div>
                        <div class="recent-customer">{{ $booking->user->name }}</div>
                        <span class="badge {{ $booking->uiStatusBadgeClass() }}" style="margin-top:4px">{{ $booking->uiStatusLabel() }}</span>
                    </div>
                    <div class="recent-meta">
                        <div class="recent-date">{{ $booking->created_at->format('d M Y') }}</div>
                        <div class="recent-price">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</div>
                    </div>
                </a>
            @empty
                <div class="empty-state">Belum ada pesanan terbaru.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
