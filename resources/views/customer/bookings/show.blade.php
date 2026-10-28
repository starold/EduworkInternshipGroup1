@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@push('styles')
<style>
.detail-layout { display:grid; grid-template-columns:1fr 360px; gap:28px; padding:40px 0; }
.page-title { font-size:24px; font-weight:800; color:#1a1a2e; margin-bottom:24px; }
.card { background:#fff; border:1px solid #e2e4e9; border-radius:14px; padding:24px; margin-bottom:24px; }
.card-title { font-size:16px; font-weight:700; color:#1a1a2e; margin-bottom:18px; border-bottom:1px solid #f0f0f5; padding-bottom:10px; display:flex; justify-content:space-between; align-items:center; }
.info-row { display:grid; grid-template-columns:140px 12px 1fr; align-items:start; margin-bottom:12px; font-size:14px; }
.info-row .label { color:#8a8a9a; font-weight:500; }
.info-row .value { color:#1a1a2e; font-weight:600; }

/* Vehicle card inside main */
.vehicle-box { border:1px solid #eef0f4; border-radius:12px; padding:18px; margin-bottom:20px; background:#fafbfc; }
.vehicle-card-inner { display:flex; gap:16px; align-items:center; margin-bottom:14px; }
.vehicle-img { width:110px; height:74px; object-fit:cover; border-radius:8px; }
.vehicle-info h4 { font-size:16px; font-weight:700; color:#1a1a2e; margin-bottom:4px; }
.vehicle-info .vtype { font-size:13px; color:#1847ED; font-weight:500; margin-bottom:4px; }
.vehicle-plate { display:inline-flex; gap:4px; align-items:center; font-size:12px; color:#556; background:#eef0f5; padding:3px 8px; border-radius:6px; font-weight:600; }

/* Fines table */
.fines-table { width:100%; border-collapse:collapse; font-size:13px; margin-top:10px; }
.fines-table th { background:#f0f2f5; padding:10px 12px; text-align:left; color:#1a1a2e; font-weight:700; border-bottom:1px solid #e2e4e9; white-space:nowrap; }
.fines-table td { padding:10px 12px; border-bottom:1px solid #f0f0f5; color:#333; vertical-align:middle; }

.btn-detail-fine { display:inline-flex; align-items:center; gap:5px; padding:6px 10px; background:#eff6ff; color:#1847ED; border:1px solid #bfdbfe; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; text-decoration:none; transition:all .15s ease; }
.btn-detail-fine:hover { background:#1847ED; color:#fff; }

/* Review section */
.review-box { margin-top:16px; padding:16px; background:#fff; border:1px solid #e2e4e9; border-radius:10px; }
.star-rating { display:flex; flex-direction:row-reverse; justify-content:flex-end; gap:6px; margin:10px 0; }
.star-rating input { display:none; }
.star-rating label { font-size:24px; color:#dcdfe5; cursor:pointer; transition:color .2s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color:#efa928; }

/* Sidebar */
.sidebar-card { border:1px solid #e2e4e9; border-radius:14px; overflow:hidden; margin-bottom:20px; background:#fff; }
.sidebar-header { background:#f8f9fb; padding:14px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e4e9; font-size:14px; font-weight:600; color:#1a1a2e; }
.sidebar-body { padding:20px; }
.summary-row { display:flex; justify-content:space-between; font-size:14px; margin-bottom:10px; }
.summary-row .lbl { color:#8a8a9a; }
.summary-row .val { color:#1a1a2e; font-weight:600; }
.summary-divider { border:none; border-top:1px dashed #e2e4e9; margin:14px 0; }
.summary-total { display:flex; justify-content:space-between; font-size:15px; font-weight:800; color:#1a1a2e; }
.btn-action { display:block; width:100%; padding:12px; text-align:center; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; margin-top:14px; transition:all .2s; border:none; text-decoration:none; }
.btn-pay { background:#1847ED; color:#fff; }
.btn-pay:hover { background:#1235b2; }
.btn-pay-fine { padding:6px 12px; background:#1847ED; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; text-decoration:none; }
.btn-pay-fine:hover { background:#1235b2; }
.btn-cancel { background:#fff; color:#dc2626; border:2px solid #dc2626; }
.btn-cancel:hover { background:#dc2626; color:#fff; }

@media(max-width:992px) { .detail-layout { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="detail-layout">
        <!-- Left content -->
        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                <h1 class="page-title" style="margin-bottom:0">Detail Pemesanan</h1>
                <a href="{{ route('customer.bookings.index') }}" style="color:#1847ED;font-size:14px;font-weight:600;text-decoration:none">← Kembali ke Pesanan</a>
            </div>

            <!-- Booking and Customer info -->
            <div class="card">
                <h3 class="card-title">
                    <span>Informasi Pemesanan</span>
                    <span class="badge {{ $booking->uiStatusBadgeClass() }}">{{ $booking->uiStatusLabel() }}</span>
                </h3>
                <div class="info-row"><span class="label">Kode Booking</span><span>:</span><span class="value">{{ $booking->booking_code }}</span></div>
                <div class="info-row"><span class="label">Status Booking</span><span>:</span><span class="value"><span class="badge {{ $booking->uiStatusBadgeClass() }}">{{ $booking->uiStatusLabel() }}</span></span></div>
                <div class="info-row"><span class="label">Tanggal Sewa</span><span>:</span><span class="value">{{ $booking->start_date->translatedFormat('d F Y') }}</span></div>
                <div class="info-row"><span class="label">Selesai Sewa</span><span>:</span><span class="value">{{ $booking->end_date->translatedFormat('d F Y') }}</span></div>
                <div class="info-row"><span class="label">Metode Ambil</span><span>:</span><span class="value">{{ $booking->pickup_method->label() }}</span></div>
                @if($booking->delivery_address)
                    <div class="info-row"><span class="label">Alamat Kirim</span><span>:</span><span class="value">{{ $booking->delivery_address }}</span></div>
                @endif
                @if($booking->notes)
                    <div class="info-row"><span class="label">Catatan</span><span>:</span><span class="value">{{ $booking->notes }}</span></div>
                @endif
            </div>

            <!-- Motor Items, Return Status & Fines -->
            <div class="card">
                <h3 class="card-title">Motor, Pengembalian &amp; Denda</h3>

                @foreach($booking->items as $item)
                    @php
                        $daysLate = $item->calculateDaysLate();
                    @endphp
                    <div class="vehicle-box">
                        <div class="vehicle-card-inner">
                            <img src="{{ $item->motor->image_url }}" alt="{{ $item->motor->model }}" class="vehicle-img">
                            <div class="vehicle-info" style="flex:1">
                                <h4>{{ $item->motor->merk }} {{ $item->motor->model }}</h4>
                                <div class="vtype">{{ $item->motor->category->name }}</div>
                                <div class="vehicle-plate">🏍 Plat: {{ $item->motor->plate_number }}</div>
                            </div>
                            <div style="text-align:right">
                                <span class="badge badge-{{ $item->return_status->value }}">{{ $item->return_status->label() }}</span>
                            </div>
                        </div>

                        <div class="info-row"><span class="label">Tarif Sewa</span><span>:</span><span class="value">Rp{{ number_format($item->price_per_day, 0, ',', '.') }} / hari ({{ $item->total_days }} hari)</span></div>
                        <div class="info-row"><span class="label">Batas Kembali</span><span>:</span><span class="value">{{ $item->due_date->format('d M Y, H:i') }}</span></div>
                        <div class="info-row">
                            <span class="label">Status Kembali</span><span>:</span>
                            <span class="value">
                                @if($item->returned_at)
                                    <strong style="color:#16a34a">Dikembalikan pada {{ $item->returned_at->format('d M Y, H:i') }}</strong>
                                @else
                                    <span style="color:#dc2626">Belum Dikembalikan ke Rental</span>
                                @endif
                            </span>
                        </div>
                        @if($daysLate > 0)
                            <div class="info-row">
                                <span class="label">Keterlambatan</span><span>:</span>
                                <span class="value" style="color:#dc2626;font-weight:700">{{ $daysLate }} Hari Terlambat</span>
                            </div>
                        @endif

                        <!-- Fines on this motor -->
                        <div style="margin-top:14px;padding-top:12px;border-top:1px solid #eef0f4">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                                <span style="font-weight:700;font-size:13px;color:#1a1a2e">Rincian Denda</span>
                                @if($item->hasUnpaidFines())
                                    <span style="font-size:12px;color:#dc2626;font-weight:700">
                                        Belum Dibayar: Rp{{ number_format($item->unpaidFinesTotal(), 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            @if($item->fines->isEmpty())
                                <p style="font-size:12px;color:#8a8a9a;margin:4px 0">Tidak ada denda untuk motor ini.</p>
                            @else
                                <div class="table-scroll-wrapper">
                                    <div class="table-scroll-body">
                                        <table class="fines-table">
                                            <thead>
                                                <tr>
                                                    <th>Jenis</th>
                                                    <th>Deskripsi</th>
                                                    <th>Nominal</th>
                                                    <th>Status Denda</th>
                                                    <th>Aksi &amp; Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($item->fines as $fine)
                                                    @php
                                                        $fineModalData = [
                                                            'id' => $fine->id,
                                                            'booking_code' => $booking->booking_code,
                                                            'customer_name' => auth()->user()->name,
                                                            'motor' => "{$item->motor->merk} {$item->motor->model} ({$item->motor->plate_number})",
                                                            'created_at' => $fine->created_at?->format('d/m/Y H:i') ?? '-',
                                                            'amount' => $fine->formatted_amount,
                                                            'description' => $fine->description,
                                                            'type' => $fine->type->value,
                                                            'type_label' => $fine->type->label(),
                                                            'fine_status' => $fine->status->value,
                                                            'payment_method' => $fine->payment?->payment_method ?? '-',
                                                            'payment_amount' => $fine->payment ? 'Rp' . number_format($fine->payment->amount, 0, ',', '.') : $fine->formatted_amount,
                                                            'payment_submitted_at' => $fine->payment?->created_at?->format('d/m/Y H:i') ?? '-',
                                                            'payment_verified_at' => $fine->paid_at?->format('d/m/Y H:i') ?? '-',
                                                            'is_awaiting_verification' => $fine->isAwaitingVerification(),
                                                            'is_rejected' => $fine->hasRejectedPayment(),
                                                            'rejection_reason' => $fine->payment?->rejection_reason ?? '',
                                                            'proof_url' => $fine->payment?->proof_url ?? null,
                                                        ];
                                                    @endphp
                                                    <tr>
                                                        <td><strong>{{ $fine->type->label() }}</strong></td>
                                                        <td>
                                                            {{ $fine->description }}
                                                            @if($fine->isLate() && $fine->days_late > 0)
                                                                <br><small style="color:#889">Tarif: (Rp{{ number_format($item->price_per_day, 0, ',', '.') }} + Rp30.000) × {{ $fine->days_late }} hari</small>
                                                            @endif
                                                        </td>
                                                        <td><strong>{{ $fine->formatted_amount }}</strong></td>
                                                        <td>
                                                            @if($fine->isPaid())
                                                                <span class="badge badge-completed">Lunas</span>
                                                            @elseif($fine->isAwaitingVerification())
                                                                <span class="badge badge-pending">Menunggu Verifikasi</span>
                                                            @elseif($fine->hasRejectedPayment())
                                                                <span class="badge badge-rejected">Bukti Ditolak</span>
                                                            @else
                                                                <span class="badge badge-unpaid">{{ $fine->status->label() }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div style="display:flex;flex-direction:column;gap:5px;align-items:flex-start">
                                                                <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                                                    Detail Denda &amp; Pembayaran
                                                                </button>
                                                                @if($fine->isPaid())
                                                                    <span style="color:#16a34a;font-size:11px;font-weight:700">&check; Terverifikasi</span>
                                                                @elseif($fine->isAwaitingVerification())
                                                                    <a href="{{ route('customer.fines.payment', $fine) }}" style="color:#ca8a04;font-size:11px;font-weight:700;text-decoration:none">
                                                                        Lihat Bukti &rarr;
                                                                    </a>
                                                                @elseif($fine->hasRejectedPayment())
                                                                    <a href="{{ route('customer.fines.payment', $fine) }}" class="btn-pay-fine" style="background:#dc2626;text-decoration:none;font-size:11px;padding:4px 8px">
                                                                        Unggah Ulang
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('customer.fines.payment', $fine) }}" class="btn-pay-fine" style="text-decoration:none;font-size:11px;padding:4px 8px">
                                                                        Bayar Denda
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Review section for this motor item -->
                        <div class="review-box">
                            <h4 style="font-size:14px;font-weight:700;color:#1a1a2e;margin-bottom:8px">Ulasan Kendaraan</h4>

                            @if($item->review)
                                <!-- Existing Review Display -->
                                <div style="background:#f8f9fb;padding:12px 16px;border-radius:8px">
                                    <div style="color:#efa928;font-size:18px;margin-bottom:4px">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $item->review->rating ? '★' : '☆' }}
                                        @endfor
                                        <span style="font-size:13px;color:#1a1a2e;font-weight:700;margin-left:6px">({{ $item->review->rating }}/5)</span>
                                    </div>
                                    <p style="font-size:13px;color:#445;margin-bottom:4px">
                                        "{{ $item->review->comment ?: 'Tidak ada komentar.' }}"
                                    </p>
                                    <small style="color:#8a8a9a">Dikirim pada: {{ $item->review->created_at->format('d M Y, H:i') }} · <em>Ulasan Anda telah tersimpan</em></small>
                                </div>
                            @elseif(! $item->isReturned())
                                <p style="font-size:13px;color:#8a8a9a;margin:0">
                                    🔒 Form review akan terbuka secara otomatis setelah motor dikonfirmasi kembali oleh admin.
                                </p>
                            @elseif($item->hasUnpaidFines())
                                <p style="font-size:13px;color:#dc2626;margin:0">
                                    ⚠ Form review akan terbuka setelah seluruh denda untuk motor ini diselesaikan.
                                </p>
                            @else
                                <!-- Review submission form -->
                                <form method="POST" action="{{ route('customer.reviews.store', $item) }}">
                                    @csrf
                                    <div style="font-size:13px;font-weight:600;color:#445;margin-bottom:4px">Beri Rating:</div>
                                    <div class="star-rating">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star-{{ $item->id }}-{{ $i }}" name="rating" value="{{ $i }}" required>
                                            <label for="star-{{ $item->id }}-{{ $i }}" title="{{ $i }} bintang">★</label>
                                        @endfor
                                    </div>
                                    <div style="margin-top:10px">
                                        <textarea name="comment" rows="2" style="width:100%;padding:10px;border:1px solid #dcdfe5;border-radius:6px;font-size:13px" placeholder="Tuliskan pengalaman rental Anda dengan motor ini (opsional)..."></textarea>
                                    </div>
                                    <button type="submit" style="margin-top:10px;padding:8px 16px;background:#1847ED;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer">
                                        Kirim Review
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right sidebar -->
        <div>
            <!-- Duration -->
            <div class="sidebar-card">
                <div class="sidebar-header">
                    Durasi sewa: <strong>{{ $booking->items->sum('total_days') }} hari</strong>
                </div>
                <div class="sidebar-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <div style="font-size:12px;color:#8a8a9a;margin-bottom:4px">Mulai Sewa</div>
                        <div style="font-size:14px;font-weight:700;color:#1a1a2e">{{ $booking->start_date->translatedFormat('d M Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#8a8a9a;margin-bottom:4px">Selesai Sewa</div>
                        <div style="font-size:14px;font-weight:700;color:#1a1a2e">{{ $booking->end_date->translatedFormat('d M Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="sidebar-card">
                <div class="sidebar-header">Ringkasan Biaya</div>
                <div class="sidebar-body">
                    <div class="summary-row"><span class="lbl">Subtotal Sewa</span><span class="val">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span></div>
                    <div class="summary-row"><span class="lbl">Biaya Antar</span><span class="val">{{ $booking->delivery_fee > 0 ? 'Rp'.number_format($booking->delivery_fee,0,',','.') : '-' }}</span></div>
                    <div class="summary-row"><span class="lbl">Diskon</span><span class="val">{{ $booking->discount > 0 ? 'Rp'.number_format($booking->discount,0,',','.') : '-' }}</span></div>
                    <hr class="summary-divider">
                    <div class="summary-total"><span>Total Biaya Sewa</span><span>Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span></div>

                    @if($booking->payment)
                        <div style="margin-top:12px;padding-top:12px;border-top:1px dashed #e2e4e9">
                            <div class="summary-row">
                                <span class="lbl">Status Bayar Sewa</span>
                                <span><span class="badge badge-{{ $booking->payment->status->value }}">{{ $booking->payment->status->label() }}</span></span>
                            </div>
                        </div>
                    @endif

                    <!-- Unpaid fines notification and Pay button -->
                    @if($booking->hasUnpaidFines())
                        @php
                            $firstUnpaidFine = $booking->fines->first(fn ($f) => $f->isUnpaid());
                        @endphp
                        <div style="margin-top:14px;padding:12px;background:#fef2f2;border-radius:8px;border:1px solid #fecaca">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                                <span style="font-size:13px;font-weight:700;color:#991b1b">Kewajiban Denda</span>
                                <span style="font-size:14px;font-weight:800;color:#dc2626">Rp{{ number_format($booking->totalUnpaidFines(), 0, ',', '.') }}</span>
                            </div>
                            @if($firstUnpaidFine)
                                <a href="{{ route('customer.fines.payment', $firstUnpaidFine) }}" class="btn-action" style="background:#dc2626;color:#fff;margin-top:4px;text-decoration:none">
                                    Bayar Denda Sekarang
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($booking->isPending() && $booking->payment && ! $booking->payment->isPaid() && ! $booking->payment->payment_proof)
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

@include('partials.fine-detail-modal')
@endsection
