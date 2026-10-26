@extends('layouts.admin')

@section('title', 'Detail Pemesanan')
@section('page-title', 'Detail Pemesanan #' . $booking->booking_code)

@push('styles')
<style>
.detail-grid { display:grid; grid-template-columns:1fr 340px; gap:24px; }
.card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; padding:24px; margin-bottom:20px; }
.card-title { font-size:15px; font-weight:700; color:#1a1a2e; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid #f0f0f5; }
.info-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f9f9f9; font-size:14px; }
.info-row:last-child { border-bottom:none; }
.info-row .lbl { color:#8a8a9a; }
.info-row .val { color:#1a1a2e; font-weight:600; }
.badge { display:inline-flex; padding:4px 10px; border-radius:50px; font-size:11px; font-weight:700; }
.badge-pending { background:#fff3cd; color:#856404; }
.badge-confirmed { background:#d4edda; color:#155724; }
.badge-cancelled { background:#e2e3e5; color:#383d41; }
.badge-rejected { background:#f8d7da; color:#721c24; }
.badge-completed { background:#cce5ff; color:#004085; }
.badge-paid { background:#d4edda; color:#155724; }
.badge-failed { background:#f8d7da; color:#721c24; }
.badge-refunded { background:#cce5ff; color:#004085; }

.motor-item { display:flex; gap:16px; padding:16px 0; border-bottom:1px solid #f0f0f5; }
.motor-item:last-child { border-bottom:none; padding-bottom:0; }
.motor-img { width:90px; height:64px; object-fit:cover; border-radius:8px; }
.motor-info h4 { font-size:15px; font-weight:700; color:#1a1a2e; margin-bottom:4px; }
.motor-info p { font-size:13px; color:#8a8a9a; }
.motor-price { margin-left:auto; text-align:right; }
.motor-price .price { font-size:15px; font-weight:800; color:#1a1a2e; }
.motor-price .days { font-size:12px; color:#8a8a9a; }

.proof-img { max-width:100%; border-radius:10px; margin-top:10px; }

.action-buttons { display:flex; flex-direction:column; gap:10px; }
.btn-confirm { padding:13px; background:#25a244; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; text-align:center; transition:all .2s; }
.btn-confirm:hover { background:#1e8b39; }
.btn-reject { padding:13px; background:#f8f9fb; color:#e74c3c; border:2px solid #e74c3c; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; text-align:center; transition:all .2s; }
.btn-reject:hover { background:#e74c3c; color:#fff; }
.btn-back-link { display:inline-flex; align-items:center; gap:6px; font-size:14px; color:#8a8a9a; margin-bottom:20px; }
.btn-back-link:hover { color:#4758D6; }

@media(max-width:992px) { .detail-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<a href="{{ route('admin.bookings.index') }}" class="btn-back-link">← Kembali ke Daftar</a>

<div class="detail-grid">
    <div>
        <!-- Booking Info -->
        <div class="card">
            <h3 class="card-title">Informasi Pesanan</h3>
            <div class="info-row"><span class="lbl">Kode Booking</span><span class="val">{{ $booking->booking_code }}</span></div>
            <div class="info-row"><span class="lbl">Status</span><span class="val"><span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span></span></div>
            <div class="info-row"><span class="lbl">Tanggal Dibuat</span><span class="val">{{ $booking->created_at->format('d M Y, H:i') }}</span></div>
            <div class="info-row"><span class="lbl">Tanggal Sewa</span><span class="val">{{ $booking->start_date->format('d M Y') }}</span></div>
            <div class="info-row"><span class="lbl">Tanggal Selesai</span><span class="val">{{ $booking->end_date->format('d M Y') }}</span></div>
            <div class="info-row"><span class="lbl">Metode Ambil</span><span class="val">{{ $booking->pickup_method->label() }}</span></div>
            @if($booking->delivery_address)
                <div class="info-row"><span class="lbl">Alamat Antar</span><span class="val" style="text-align:right;max-width:60%">{{ $booking->delivery_address }}</span></div>
            @endif
            @if($booking->notes)
                <div class="info-row"><span class="lbl">Catatan</span><span class="val">{{ $booking->notes }}</span></div>
            @endif
        </div>

        <!-- Customer -->
        <div class="card">
            <h3 class="card-title">Data Pemesan</h3>
            <div class="info-row"><span class="lbl">Nama</span><span class="val">{{ $booking->user->name }}</span></div>
            <div class="info-row"><span class="lbl">Email</span><span class="val">{{ $booking->user->email }}</span></div>
            <div class="info-row"><span class="lbl">WhatsApp</span>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->user->phone) }}" target="_blank" class="val" style="color:#25D366">{{ $booking->user->phone }}</a>
            </div>
        </div>

        <!-- Motors -->
        <div class="card">
            <h3 class="card-title">Motor Dipesan</h3>
            @foreach($booking->items as $item)
                <div class="motor-item">
                    <img src="{{ $item->motor->image_url }}" alt="{{ $item->motor->model }}" class="motor-img">
                    <div class="motor-info">
                        <h4>{{ $item->motor->merk }} {{ $item->motor->model }}</h4>
                        <p>{{ $item->motor->category->name }} · {{ $item->motor->plate_number }}</p>
                        <p>{{ $item->total_days }} hari × Rp{{ number_format($item->price_per_day, 0, ',', '.') }}</p>
                    </div>
                    <div class="motor-price">
                        <div class="price">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        <div class="days">Deposit: Rp{{ number_format($item->deposit, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Payment Proof -->
        @if($booking->payment && $booking->payment->payment_proof)
            <div class="card">
                <h3 class="card-title">Bukti Pembayaran</h3>
                <div class="info-row"><span class="lbl">Metode</span><span class="val">{{ $booking->payment->payment_method ?? '-' }}</span></div>
                <div class="info-row"><span class="lbl">Status</span><span class="val"><span class="badge badge-{{ $booking->payment->status->value }}">{{ $booking->payment->status->label() }}</span></span></div>
                <img src="{{ $booking->payment->proof_url }}" alt="Bukti Bayar" class="proof-img">
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Summary -->
        <div class="card">
            <h3 class="card-title">Ringkasan Biaya</h3>
            <div class="info-row"><span class="lbl">Subtotal</span><span class="val">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span></div>
            <div class="info-row"><span class="lbl">Biaya Antar</span><span class="val">{{ $booking->delivery_fee > 0 ? 'Rp'.number_format($booking->delivery_fee,0,',','.') : '-' }}</span></div>
            <div class="info-row"><span class="lbl">Diskon</span><span class="val">{{ $booking->discount > 0 ? 'Rp'.number_format($booking->discount,0,',','.') : '-' }}</span></div>
            <div class="info-row" style="font-size:16px"><span class="lbl" style="font-weight:700;color:#1a1a2e">Total</span><span class="val" style="font-size:18px">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
        </div>

        <!-- Actions -->
        @if($booking->isPending())
            <div class="card">
                <h3 class="card-title">Tindakan Admin</h3>
                <div class="action-buttons">
                    <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">
                        @csrf
                        <button type="submit" class="btn-confirm" style="width:100%" onclick="return confirm('Konfirmasi booking ini?')">
                            ✓ Konfirmasi Pesanan
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.bookings.reject', $booking) }}">
                        @csrf
                        <div style="margin-bottom:10px">
                            <label style="font-size:13px;font-weight:600;color:#4a4a5a;display:block;margin-bottom:6px">Alasan penolakan:</label>
                            <textarea name="reason" style="width:100%;padding:10px;border:1.5px solid #e2e4e9;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;resize:vertical" rows="3" placeholder="Tulis alasan penolakan..."></textarea>
                        </div>
                        <button type="submit" class="btn-reject" style="width:100%" onclick="return confirm('Tolak booking ini?')">
                            ✗ Tolak Pesanan
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
