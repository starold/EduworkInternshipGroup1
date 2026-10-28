@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@push('styles')
<style>
.detail-layout { display:grid; grid-template-columns:1fr 340px; gap:28px; padding:40px 0; }
.page-title { font-size:24px; font-weight:800; color:#1a1a2e; margin-bottom:24px; }
.card { background:#fff; border:1px solid #e2e4e9; border-radius:14px; padding:28px; margin-bottom:24px; }
.card-title { font-size:16px; font-weight:700; color:#1a1a2e; margin-bottom:20px; }
.info-row { display:grid; grid-template-columns:140px 12px 1fr; align-items:start; margin-bottom:14px; font-size:14px; }
.info-row .label { color:#8a8a9a; font-weight:500; }
.info-row .value { color:#1a1a2e; font-weight:600; }
.badge { display:inline-flex; padding:5px 12px; border-radius:50px; font-size:12px; font-weight:700; }
.badge-pending { background:#fff3cd; color:#856404; }
.badge-confirmed { background:#d4edda; color:#155724; }
.badge-cancelled { background:#e2e3e5; color:#383d41; }
.badge-rejected { background:#f8d7da; color:#721c24; }
.badge-completed { background:#cce5ff; color:#004085; }
.badge-paid { background:#d4edda; color:#155724; }
.badge-failed { background:#f8d7da; color:#721c24; }

/* Sidebar */
.sidebar-card { border:1px solid #e2e4e9; border-radius:14px; overflow:hidden; margin-bottom:20px; }
.sidebar-header { background:#f8f9fb; padding:14px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e4e9; font-size:14px; font-weight:600; color:#1a1a2e; }
.sidebar-body { padding:20px; }
.vehicle-card-inner { display:flex; gap:16px; align-items:center; }
.vehicle-img { width:120px; height:80px; object-fit:contain; border-radius:8px; }
.vehicle-info h4 { font-size:16px; font-weight:700; color:#1a1a2e; margin-bottom:4px; }
.vehicle-info .vtype { font-size:13px; color:#4758D6; font-weight:500; margin-bottom:6px; }
.vehicle-plate { display:inline-flex; gap:4px; align-items:center; font-size:12px; color:#8a8a9a; background:#f4f5f7; padding:3px 8px; border-radius:6px; font-weight:600; }
.summary-row { display:flex; justify-content:space-between; font-size:14px; margin-bottom:12px; }
.summary-row .lbl { color:#8a8a9a; }
.summary-row .val { color:#1a1a2e; font-weight:600; }
.summary-divider { border:none; border-top:1px dashed #e2e4e9; margin:14px 0; }
.summary-total { display:flex; justify-content:space-between; font-size:15px; font-weight:800; color:#1a1a2e; }
.btn-action { display:block; width:100%; padding:13px; text-align:center; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; margin-top:16px; transition:all .2s; border:none; }
.btn-pay { background:#4758D6; color:#fff; }
.btn-pay:hover { background:#3847B5; }
.btn-cancel { background:#f8f9fb; color:#e74c3c; border:2px solid #e74c3c; }
.btn-cancel:hover { background:#e74c3c; color:#fff; }

/* Terms */
.terms-list { padding-left:18px; list-style:decimal; }
.terms-list li { font-size:14px; margin-bottom:12px; line-height:1.7; }
.terms-list li strong { color:#1a1a2e; }

@media(max-width:992px) { .detail-layout { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="detail-layout">
        <!-- Left -->
        <div>
            <h1 class="page-title">Detail Pemesanan</h1>

            <!-- Pemesan -->
            <div class="card">
                <h3 class="card-title">Pemesan</h3>
                <div class="info-row"><span class="label">Nama</span><span>:</span><span class="value">{{ $booking->user->name }}</span></div>
                <div class="info-row"><span class="label">No. WhatsApp</span><span>:</span><span class="value">{{ $booking->user->phone }}</span></div>
                <div class="info-row"><span class="label">Kode Booking</span><span>:</span><span class="value">{{ $booking->booking_code }}</span></div>
                <div class="info-row"><span class="label">Status</span><span>:</span>
                    <span><span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span></span>
                </div>
                <div class="info-row"><span class="label">Metode Ambil</span><span>:</span><span class="value">{{ $booking->pickup_method->label() }}</span></div>
                @if($booking->delivery_address)
                    <div class="info-row"><span class="label">Alamat Kirim</span><span>:</span><span class="value">{{ $booking->delivery_address }}</span></div>
                @endif
                @if($booking->notes)
                    <div class="info-row"><span class="label">Catatan</span><span>:</span><span class="value">{{ $booking->notes }}</span></div>
                @endif
            </div>

            <!-- Syarat & Ketentuan -->
            <div class="card">
                <h2 class="card-title">Syarat & Ketentuan Rental Motor</h2>
                <p style="font-size:14px;color:#4a4a5a;margin-bottom:16px;line-height:1.7">Transparansi adalah kunci. Pastikan Anda memenuhi beberapa persyaratan dasar sebelum melakukan pemesanan.</p>
                <ol class="terms-list">
                    <li>Lakukan pemesanan minimal <strong>1x24 jam</strong> sebelum waktu penggunaan.</li>
                    <li>Membawa <strong>2 kartu identitas</strong> (SIM, KK, KTP, ID Kerja atau ID Pelajar).</li>
                    <li>Menyertakan akun sosial media dan <strong>nomor WhatsApp</strong> yang aktif.</li>
                    <li>Pada saat serah terima, pihak rental akan melakukan <strong>pengecekan keaslian data</strong>.</li>
                    <li>Pihak rental <strong>berhak membatalkan</strong> apabila data tidak sesuai.</li>
                    <li>Motor yang dipesan tidak tersedia, penyewa <strong>berhak memilih kendaraan lain</strong>.</li>
                </ol>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div>
            <!-- Durasi -->
            <div class="sidebar-card">
                <div class="sidebar-header">
                    Durasi sewa : <strong>{{ $booking->items->sum('total_days') }} hari</strong>
                </div>
                <div class="sidebar-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <div style="font-size:12px;color:#8a8a9a;margin-bottom:6px">Tanggal sewa</div>
                        <div style="font-size:14px;font-weight:700;color:#1a1a2e">{{ $booking->start_date->translatedFormat('D, d M Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#8a8a9a;margin-bottom:6px">Selesai sewa</div>
                        <div style="font-size:14px;font-weight:700;color:#1a1a2e">{{ $booking->end_date->translatedFormat('D, d M Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Vehicle -->
            @foreach($booking->items as $item)
                <div class="sidebar-card">
                    <div class="sidebar-body">
                        <div class="vehicle-card-inner">
                            <img src="{{ $item->motor->image_url }}" alt="{{ $item->motor->model }}" class="vehicle-img">
                            <div class="vehicle-info">
                                <h4>{{ $item->motor->model }}</h4>
                                <div class="vtype">{{ $item->motor->category->name }}</div>
                                <div class="vehicle-plate">🏍 {{ $item->motor->plate_number }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Transaction Summary -->
            <div class="sidebar-card">
                <div class="sidebar-header">Ringkasan transaksi</div>
                <div class="sidebar-body">
                    <div class="summary-row"><span class="lbl">Subtotal</span><span class="val">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span></div>
                    <div class="summary-row"><span class="lbl">Biaya Antar</span><span class="val">{{ $booking->delivery_fee > 0 ? 'Rp'.number_format($booking->delivery_fee,0,',','.') : '-' }}</span></div>
                    <div class="summary-row"><span class="lbl">Diskon</span><span class="val">{{ $booking->discount > 0 ? 'Rp'.number_format($booking->discount,0,',','.') : '-' }}</span></div>
                    <hr class="summary-divider">
                    <div class="summary-total"><span>Total harga</span><span>Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span></div>

                    @if($booking->payment)
                        <div style="margin-top:12px;padding-top:12px;border-top:1px dashed #e2e4e9">
                            <div class="summary-row">
                                <span class="lbl">Status Bayar</span>
                                <span><span class="badge badge-{{ $booking->payment->status->value }}">{{ $booking->payment->status->label() }}</span></span>
                            </div>
                        </div>
                    @endif

                    @if($booking->isPending() && $booking->payment && !$booking->payment->isPaid() && !$booking->payment->payment_proof)
                        <a href="{{ route('customer.payments.show', $booking) }}" class="btn-action btn-pay">Bayar Sekarang</a>
                    @endif

                    @if($booking->isPending())
                        <form method="POST" action="{{ route('customer.bookings.cancel', $booking) }}" onsubmit="return confirm('Yakin ingin membatalkan pesanan?')">
                            @csrf
                            <button type="submit" class="btn-action btn-cancel">Batalkan Pesanan</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
