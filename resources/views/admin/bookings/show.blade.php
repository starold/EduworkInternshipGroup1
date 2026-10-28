@extends('layouts.admin')

@section('title', 'Detail Pemesanan')
@section('page-title', 'Detail Pemesanan #' . $booking->booking_code)

@push('styles')
<style>
.detail-grid { display:grid; grid-template-columns:1fr 360px; gap:24px; }
.card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; padding:24px; margin-bottom:20px; }
.card-title { font-size:15px; font-weight:700; color:#1a1a2e; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid #f0f0f5; display:flex; justify-content:space-between; align-items:center; }
.info-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f9f9f9; font-size:14px; }
.info-row:last-child { border-bottom:none; }
.info-row .lbl { color:#8a8a9a; }
.info-row .val { color:#1a1a2e; font-weight:600; }

.item-box { border:1px solid #eef0f4; border-radius:10px; padding:16px; margin-bottom:16px; background:#fafbfc; }
.motor-header { display:flex; gap:16px; align-items:center; margin-bottom:14px; }
.motor-img { width:90px; height:64px; object-fit:cover; border-radius:8px; }
.motor-meta h4 { font-size:15px; font-weight:700; color:#1a1a2e; margin-bottom:4px; }
.motor-meta p { font-size:13px; color:#8a8a9a; }

.fines-table { width:100%; border-collapse:collapse; font-size:13px; }
.fines-table th { background:#f4f5f8; padding:10px 12px; text-align:left; color:#1a1a2e; font-weight:700; border-bottom:1px solid #e2e4e9; white-space:nowrap; }
.fines-table td { padding:10px 12px; border-bottom:1px solid #f0f0f5; color:#333; vertical-align:middle; }

.btn-detail-fine { display:inline-flex; align-items:center; gap:5px; margin-top:6px; padding:6px 10px; background:#eff6ff; color:#1847ED; border:1px solid #bfdbfe; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; text-decoration:none; transition:all .15s ease; }
.btn-detail-fine:hover { background:#1847ED; color:#fff; }

.action-buttons { display:flex; flex-direction:column; gap:10px; }
.btn-confirm { padding:12px; background:#16a34a; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; text-align:center; transition:all .2s; }
.btn-confirm:hover { background:#15803d; }
.btn-confirm-disabled { padding:12px; background:#e2e4e9; color:#8a8a9a; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:not-allowed; text-align:center; }
.btn-reject { padding:12px; background:#fff; color:#dc2626; border:2px solid #dc2626; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; text-align:center; transition:all .2s; }
.btn-reject:hover { background:#dc2626; color:#fff; }
.btn-return { padding:10px 16px; background:#1847ED; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer; }
.btn-return:hover { background:#1235b2; }
.btn-fine { padding:8px 14px; background:#f59e0b; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; }
.btn-fine:hover { background:#d97706; }
.btn-back-link { display:inline-flex; align-items:center; gap:6px; font-size:14px; color:#8a8a9a; margin-bottom:20px; text-decoration:none; font-weight:600; }
.btn-back-link:hover { color:#1847ED; }

.btn-verify-fine { padding:6px 12px; background:#16a34a; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; }
.btn-verify-fine:hover { background:#15803d; }
.btn-reject-fine { padding:6px 12px; background:#fff; color:#dc2626; border:1px solid #dc2626; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; }
.btn-reject-fine:hover { background:#dc2626; color:#fff; }

/* Modal styling */
.custom-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
.custom-modal.active { display:flex; }
.modal-content-card { background:#fff; border-radius:12px; width:100%; max-width:480px; padding:24px; box-shadow:0 10px 30px rgba(0,0,0,.2); }
.modal-header-line { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
.modal-header-line h3 { font-size:17px; font-weight:700; color:#1a1a2e; }
.modal-close-btn { background:none; border:none; font-size:20px; cursor:pointer; color:#999; display:flex; align-items:center; justify-content:center; }
.modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
.btn-secondary { padding:9px 18px; background:#f0f2f5; border:none; border-radius:6px; font-weight:600; cursor:pointer; color:#556; }
.btn-primary { padding:9px 18px; background:#1847ED; border:none; border-radius:6px; font-weight:600; cursor:pointer; color:#fff; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#445; margin-bottom:6px; }
.form-group input, .form-group select, .form-group textarea { width:100%; padding:10px; border:1px solid #dcdfe5; border-radius:6px; font-size:14px; }

@media(max-width:992px) { .detail-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<a href="{{ route('admin.bookings.index') }}" class="btn-back-link">← Kembali ke Daftar</a>

<div class="detail-grid">
    <div>
        <!-- Order info -->
        <div class="card">
            <h3 class="card-title">
                <span>Informasi Pesanan</span>
                <span class="badge {{ $booking->uiStatusBadgeClass() }}">{{ $booking->uiStatusLabel() }}</span>
            </h3>
            <div class="info-row"><span class="lbl">Kode Booking</span><span class="val">{{ $booking->booking_code }}</span></div>
            <div class="info-row"><span class="lbl">Tanggal Pemesanan</span><span class="val">{{ $booking->created_at->format('d M Y, H:i') }}</span></div>
            <div class="info-row"><span class="lbl">Jadwal Sewa Mulai</span><span class="val">{{ $booking->start_date->format('d M Y') }}</span></div>
            <div class="info-row"><span class="lbl">Jadwal Selesai</span><span class="val">{{ $booking->end_date->format('d M Y') }}</span></div>
            <div class="info-row"><span class="lbl">Metode Ambil</span><span class="val">{{ $booking->pickup_method->label() }}</span></div>
            @if($booking->delivery_address)
                <div class="info-row"><span class="lbl">Alamat Antar</span><span class="val" style="text-align:right;max-width:60%">{{ $booking->delivery_address }}</span></div>
            @endif
            @if($booking->notes)
                <div class="info-row"><span class="lbl">Catatan</span><span class="val">{{ $booking->notes }}</span></div>
            @endif
        </div>

        <!-- Customer info -->
        <div class="card">
            <h3 class="card-title">Data Pemesan</h3>
            <div class="info-row"><span class="lbl">Nama Lengkap</span><span class="val">{{ $booking->user->name }}</span></div>
            <div class="info-row"><span class="lbl">Email</span><span class="val">{{ $booking->user->email }}</span></div>
            <div class="info-row"><span class="lbl">No. WhatsApp</span>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->user->phone) }}" target="_blank" class="val" style="color:#25D366;text-decoration:none">{{ $booking->user->phone }}</a>
            </div>
            @if($booking->user->address)
                <div class="info-row"><span class="lbl">Alamat KTP</span><span class="val">{{ $booking->user->address }}</span></div>
            @endif
        </div>

        <!-- Motors & Return Status -->
        <div class="card">
            <div class="card-title">
                <span>Motor &amp; Status Pengembalian</span>
                @php
                    $hasReturnedMotor = $booking->items->contains(fn ($i) => $i->isReturned());
                    $canRequestFine = $hasReturnedMotor && $booking->isConfirmed() && ! $booking->isCompleted();
                @endphp
                @if($canRequestFine)
                    <button type="button" class="btn-fine" onclick="openFineModal({{ $booking->items->first(fn ($i) => $i->isReturned())?->id ?? 0 }})">
                        + Request Denda
                    </button>
                @endif
            </div>

            @foreach($booking->items as $item)
                @php
                    $daysLate = $item->calculateDaysLate();
                    $isOverdue = $item->isOverdue();
                @endphp
                <div class="item-box">
                    <div class="motor-header">
                        <img src="{{ $item->motor->image_url }}" alt="{{ $item->motor->model }}" class="motor-img">
                        <div class="motor-meta" style="flex:1">
                            <h4>{{ $item->motor->merk }} {{ $item->motor->model }}</h4>
                            <p>{{ $item->motor->category->name }} · Plat: <strong>{{ $item->motor->plate_number }}</strong></p>
                            <p>Harga Sewa Beku: <strong>Rp{{ number_format($item->price_per_day, 0, ',', '.') }}/hari</strong> · Durasi: {{ $item->total_days }} hari</p>
                        </div>
                        <div style="text-align:right">
                            <span class="badge badge-{{ $item->return_status->value }}">{{ $item->return_status->label() }}</span>
                        </div>
                    </div>

                    <div class="info-row"><span class="lbl">Batas Waktu Pengembalian</span><span class="val">{{ $item->due_date->format('d M Y, H:i') }}</span></div>
                    <div class="info-row">
                        <span class="lbl">Waktu Kembali Aktual</span>
                        <span class="val">
                            @if($item->returned_at)
                                <strong style="color:#16a34a">{{ $item->returned_at->format('d M Y, H:i') }}</strong>
                            @else
                                <span style="color:#dc2626">Belum Dikembalikan</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Keterlambatan</span>
                        <span class="val">
                            @if($daysLate > 0)
                                <span style="color:#dc2626;font-weight:700">{{ $daysLate }} Hari Terlambat</span>
                            @else
                                <span style="color:#16a34a">Tepat Waktu</span>
                            @endif
                        </span>
                    </div>

                    <!-- Return confirmation button -->
                    @if($booking->isConfirmed() && ! $item->isReturned())
                        <div style="margin-top:14px;padding-top:12px;border-top:1px dashed #e2e4e9;display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:13px;color:#556">
                                @if($isOverdue)
                                    <strong style="color:#dc2626">⚠ Melewati batas waktu kembali.</strong>
                                @else
                                    <span>Motor siap dikonfirmasi pengembalian.</span>
                                @endif
                            </span>
                            <button type="button" class="btn-return" onclick="openReturnModal({{ $item->id }}, '{{ $item->motor->merk }} {{ $item->motor->model }}')">
                                Konfirmasi Pengembalian
                            </button>
                        </div>
                    @endif

                    <!-- Fines for this item -->
                    <div style="margin-top:14px;padding-top:12px;border-top:1px solid #eef0f4">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                            <span style="font-weight:700;font-size:13px;color:#1a1a2e">Rincian Denda</span>
                            <span style="font-size:12px;color:#8a8a9a">Unpaid: <strong>Rp{{ number_format($item->unpaidFinesTotal(), 0, ',', '.') }}</strong></span>
                        </div>

                        @if($item->fines->isEmpty())
                            <p style="font-size:12px;color:#99a;margin:6px 0">Tidak ada denda untuk motor ini.</p>
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
                                                <th>Bukti Transfer &amp; Pembayaran</th>
                                                <th>Tindakan Admin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item->fines as $fine)
                                                @php
                                                    $fineModalData = [
                                                        'id' => $fine->id,
                                                        'booking_code' => $booking->booking_code,
                                                        'customer_name' => $booking->user->name,
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
                                                    <td>{{ $fine->description }}</td>
                                                    <td><strong>{{ $fine->formatted_amount }}</strong></td>
                                                    <td>
                                                        <span class="badge badge-{{ $fine->status->value === 'paid' ? 'completed' : 'unpaid' }}">
                                                            {{ $fine->status->label() }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($fine->isPaid())
                                                            <span class="badge badge-completed">Lunas</span>
                                                            <small style="display:block;color:#166534;margin-top:2px">{{ $fine->paid_at?->format('d/m/Y H:i') }}</small>
                                                            <div>
                                                                <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                                                    Detail Denda &amp; Pembayaran
                                                                </button>
                                                            </div>
                                                        @elseif($fine->isAwaitingVerification())
                                                            <span class="badge badge-pending">Menunggu Verifikasi</span>
                                                            <small style="display:block;color:#854d0e;font-weight:600;margin-top:2px">{{ $fine->payment?->payment_method }} · {{ $fine->payment?->updated_at->format('d/m H:i') }}</small>
                                                            <div>
                                                                <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                                                    Detail Denda &amp; Pembayaran
                                                                </button>
                                                            </div>
                                                        @elseif($fine->hasRejectedPayment())
                                                            <span class="badge badge-rejected">Bukti Ditolak</span>
                                                            <small style="display:block;color:#dc2626;margin-top:2px">Alasan: {{ $fine->payment?->rejection_reason }}</small>
                                                            <div>
                                                                <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                                                    Detail Denda &amp; Pembayaran
                                                                </button>
                                                            </div>
                                                        @else
                                                            <span class="badge badge-unpaid">Belum Ada Bukti</span>
                                                            <div>
                                                                <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                                                    Detail Denda &amp; Pembayaran
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($fine->isAwaitingVerification())
                                                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                                                <form method="POST" action="{{ route('admin.fines.verify', $fine) }}" style="display:inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn-verify-fine" onclick="return confirm('Konfirmasi bahwa bukti transfer valid dan denda lunas?')">
                                                                        &check; Konfirmasi Lunas
                                                                    </button>
                                                                </form>
                                                                <button type="button" class="btn-reject-fine" onclick="openRejectModal({{ $fine->id }})">
                                                                    &cross; Tolak
                                                                </button>
                                                            </div>
                                                        @elseif($fine->isPaid())
                                                            <span style="color:#16a34a;font-weight:700;font-size:12px">&check; Selesai</span>
                                                        @else
                                                            <span style="color:#99a;font-size:12px">Menunggu Pembayaran</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Review for this item if exists -->
                    @if($item->review)
                        <div style="margin-top:14px;padding-top:12px;border-top:1px dashed #e2e4e9">
                            <span style="font-weight:700;font-size:13px;color:#1a1a2e">Ulasan Pelanggan:</span>
                            <div style="font-size:14px;color:#efa928;margin-top:4px">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $item->review->rating ? '★' : '☆' }}
                                @endfor
                                <span style="color:#445;font-size:13px;margin-left:6px">"{{ $item->review->comment ?? 'Tidak ada komentar.' }}"</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Sidebar -->
    <div>
        <!-- Cost summary -->
        <div class="card">
            <h3 class="card-title">Ringkasan Transaksi</h3>
            <div class="info-row"><span class="lbl">Subtotal Sewa</span><span class="val">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span></div>
            <div class="info-row"><span class="lbl">Biaya Pengantaran</span><span class="val">{{ $booking->delivery_fee > 0 ? 'Rp'.number_format($booking->delivery_fee,0,',','.') : '-' }}</span></div>
            <div class="info-row"><span class="lbl">Total Denda</span><span class="val" style="color:#dc2626">Rp{{ number_format($booking->totalFines(), 0, ',', '.') }}</span></div>
            <div class="info-row"><span class="lbl">Denda Belum Dibayar</span><span class="val" style="color:#dc2626;font-size:16px">Rp{{ number_format($booking->totalUnpaidFines(), 0, ',', '.') }}</span></div>
            <div class="info-row" style="font-size:16px;border-top:1px dashed #e2e4e9;padding-top:12px">
                <span class="lbl" style="font-weight:700;color:#1a1a2e">Total Biaya Sewa</span>
                <span class="val" style="font-size:18px">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Status Process Indicator -->
        <div class="card">
            <h3 class="card-title">Status Kelengkapan Rental</h3>
            <div class="info-row">
                <span class="lbl">Status Pengembalian</span>
                <span class="val">
                    @if($booking->isAllItemsReturned())
                        <span class="badge badge-returned">Semua Kembali</span>
                    @else
                        <span class="badge badge-pending">Belum Lengkap</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="lbl">Status Denda</span>
                <span class="val">
                    @if($booking->hasUnpaidFines())
                        <span class="badge badge-unpaid">Ada Denda Unpaid</span>
                    @else
                        <span class="badge badge-completed">Semua Lunas</span>
                    @endif
                </span>
            </div>
            <div class="info-row" style="margin-top:8px;padding-top:10px;border-top:1px solid #f0f0f5">
                <span class="lbl">Tahap Booking</span>
                <span class="val">
                    @if($booking->isCompleted())
                        <strong style="color:#166534">Selesai Rental (Completed)</strong>
                    @elseif($booking->isAllItemsReturned())
                        <strong style="color:#ca8a04">Menunggu Konfirmasi Pengembalian</strong>
                    @elseif($booking->isConfirmed())
                        <strong style="color:#1e40af">Sedang Disewa</strong>
                    @else
                        <span style="color:#8a8a9a">{{ $booking->status->label() }}</span>
                    @endif
                </span>
            </div>
        </div>

        <!-- Actions: Completion for Returned Motors -->
        @if($booking->isConfirmed() && $booking->isAllItemsReturned())
            <div class="card" style="border:1.5px solid #bfdbfe;background:#f8fafc">
                <h3 class="card-title" style="color:#1e40af;border-bottom-color:#e2e8f0">
                    <span>Penyelesaian Pemesanan</span>
                </h3>
                <div class="action-buttons">
                    @if($booking->isEligibleForCompletion())
                        <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
                            @csrf
                            <button type="submit" class="btn-confirm" style="width:100%" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan pemesanan ini?')">
                                &check; Konfirmasi Pemesanan Selesai
                            </button>
                        </form>
                        <small style="color:#166534;font-size:12px;display:block;margin-top:4px">
                            &check; Seluruh motor telah dikembalikan dan semua denda telah lunas.
                        </small>
                    @else
                        <button type="button" class="btn-confirm-disabled" style="width:100%" disabled>
                            &check; Konfirmasi Pemesanan Selesai
                        </button>
                        <div style="margin-top:8px;padding:10px;background:#fee2e2;border:1px solid #fecaca;border-radius:6px;font-size:12px;color:#991b1b;line-height:1.4">
                            @if($booking->hasPendingFinePayments())
                                <strong>⚠ Perhatian:</strong> Pembayaran denda masih menunggu konfirmasi verifikasi Admin.
                            @elseif($booking->hasUnpaidFines())
                                <strong>⚠ Perhatian:</strong> Selesaikan seluruh pembayaran denda terlebih dahulu.
                            @else
                                <strong>⚠ Perhatian:</strong> {{ $booking->getCompletionBlockReason() }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @elseif($booking->isCompleted())
            <div class="card" style="border:1px solid #bbf7d0;background:#f0fdf4">
                <div style="text-align:center;padding:8px 0">
                    <span style="font-size:24px;display:block;margin-bottom:4px">✓</span>
                    <strong style="color:#166534;font-size:15px">Pemesanan Selesai</strong>
                    <p style="color:#15803d;font-size:12px;margin-top:4px">Seluruh rangkaian rental telah diselesaikan.</p>
                </div>
            </div>
        @endif

        @if($booking->isConfirmed())
            <div class="card" style="border:1.5px solid #bfdbfe;background:#eff6ff">
                <h3 class="card-title" style="color:#1e40af;margin-bottom:8px">Instruksi Pengambilan (Sedang Disewa)</h3>
                <p style="font-size:13px;color:#1e3a8a;margin:0;line-height:1.6">
                    <strong>Metode:</strong> {{ $booking->pickup_method->label() }}<br>
                    <strong>Keterangan Pelanggan:</strong><br>
                    <span style="font-weight:700">"{{ $booking->getDeliveryInstructions() }}"</span>
                </p>
            </div>
        @endif

        <!-- Admin Action for Pending Booking -->
        @if($booking->isPending())
            <!-- Verifikasi Pembayaran Sewa -->
            <div class="card" style="border:1.5px solid #e2e4e9">
                <h3 class="card-title">
                    <span>Verifikasi Pembayaran Sewa</span>
                    @if($booking->payment && $booking->payment->isPaid())
                        <span class="badge badge-completed">Sudah Lunas</span>
                    @elseif($booking->payment && $booking->payment->isPending() && $booking->payment->payment_proof)
                        <span class="badge badge-pending">Menunggu Verifikasi</span>
                    @elseif($booking->payment && $booking->payment->isFailed())
                        <span class="badge badge-rejected">Bukti Ditolak</span>
                    @else
                        <span class="badge badge-unpaid">Belum Ada Bukti</span>
                    @endif
                </h3>

                <div class="info-row"><span class="lbl">Total Tagihan</span><span class="val" style="font-weight:700">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
                @if($booking->payment)
                    <div class="info-row"><span class="lbl">Metode Bayar</span><span class="val">{{ $booking->payment->payment_method ?? 'Transfer Bank' }}</span></div>
                    <div class="info-row"><span class="lbl">Kode Bayar</span><span class="val">{{ $booking->payment->payment_code }}</span></div>
                    @if($booking->payment->paid_at)
                        <div class="info-row"><span class="lbl">Waktu Lunas</span><span class="val" style="color:#16a34a">{{ $booking->payment->paid_at->format('d M Y, H:i') }}</span></div>
                    @endif
                @endif

                @if($booking->payment && $booking->payment->isPending() && $booking->payment->payment_proof)
                    <div style="margin:14px 0;padding:12px;background:#f8f9fb;border-radius:8px;text-align:center">
                        <span style="display:block;font-size:12px;color:#667;margin-bottom:8px">Bukti Transfer Pelanggan:</span>
                        <a href="{{ $booking->payment->proof_url }}" target="_blank">
                            <img src="{{ $booking->payment->proof_url }}" alt="Bukti Transfer" style="max-height:160px;margin:0 auto;border-radius:6px;border:1px solid #dcdfe5;display:block">
                        </a>
                        <small style="display:block;color:#8a8a9a;margin-top:6px">Klik gambar untuk melihat ukuran penuh</small>
                    </div>

                    <div class="action-buttons" style="margin-top:12px">
                        <form method="POST" action="{{ route('admin.bookings.payments.verify', $booking) }}">
                            @csrf
                            <button type="submit" class="btn-confirm" style="width:100%" onclick="return confirm('Konfirmasi bahwa pembayaran sewa telah diterima dan lunas?')">
                                &check; Konfirmasi Pembayaran Lunas
                            </button>
                        </form>
                        <button type="button" class="btn-reject" style="width:100%" onclick="openRejectRentalPaymentModal()">
                            &cross; Tolak Bukti Pembayaran
                        </button>
                    </div>
                @elseif($booking->payment && $booking->payment->isFailed())
                    <div style="margin:12px 0;padding:10px;background:#fee2e2;border:1px solid #fecaca;border-radius:8px;font-size:12px;color:#991b1b">
                        <strong>Bukti pembayaran ditolak:</strong> {{ $booking->payment->rejection_reason }}<br>
                        <span style="color:#b91c1c">Menunggu pelanggan upload ulang bukti bayar sebelum tenggat waktu.</span>
                    </div>
                    @if($booking->payment->payment_proof)
                        <div style="text-align:center;margin-top:6px">
                            <a href="{{ $booking->payment->proof_url }}" target="_blank" style="font-size:12px;color:#dc2626">Lihat Bukti yang Ditolak ↗</a>
                        </div>
                    @endif
                @elseif($booking->payment && $booking->payment->isPaid())
                    <div style="margin:12px 0;padding:10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;font-size:12px;color:#166534">
                        &check; Pembayaran sewa telah selesai diverifikasi.
                    </div>
                    @if($booking->payment->payment_proof)
                        <div style="text-align:center;margin-top:4px">
                            <a href="{{ $booking->payment->proof_url }}" target="_blank" style="font-size:12px;color:#1847ED">Lihat Bukti Transfer ↗</a>
                        </div>
                    @endif
                @else
                    <div style="margin:12px 0;padding:10px;background:#fefce8;border:1px solid #fef08a;border-radius:8px;font-size:12px;color:#854d0e">
                        Pelanggan belum mengunggah bukti pembayaran.<br>
                        Batas Waktu: {{ $booking->getPaymentDeadline()->format('d M Y, H:i') }}
                    </div>
                @endif
            </div>

            <!-- Tindakan Pemesanan -->
            <div class="card">
                <h3 class="card-title">Tindakan Pemesanan</h3>
                <div class="action-buttons">
                    @if($booking->payment && $booking->payment->isPaid())
                        <div style="margin-bottom:10px;padding:8px 10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:12px;color:#166534">
                            &check; Status Menunggu Konfirmasi: Anda dapat mengonfirmasi atau menolak pesanan.
                        </div>
                        <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">
                            @csrf
                            <button type="submit" class="btn-confirm" style="width:100%" onclick="return confirm('Konfirmasi booking ini? Status pesanan akan langsung menjadi Sedang Disewa.')">
                                &check; Konfirmasi Pesanan (Sedang Disewa)
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.bookings.reject', $booking) }}" style="margin-top:10px">
                            @csrf
                            <div style="margin-bottom:10px">
                                <label style="font-size:13px;font-weight:600;color:#4a4a5a;display:block;margin-bottom:6px">Alasan penolakan:</label>
                                <textarea name="reason" style="width:100%;padding:10px;border:1.5px solid #e2e4e9;border-radius:8px;font-size:13px" rows="3" placeholder="Tulis alasan penolakan pesanan..."></textarea>
                            </div>
                            <button type="submit" class="btn-reject" style="width:100%" onclick="return confirm('Tolak booking ini?')">
                                &cross; Tolak Pesanan
                            </button>
                        </form>
                    @else
                        <div style="margin-bottom:10px;padding:10px;background:#fef9c3;border:1px solid #fef08a;border-radius:8px;font-size:12px;color:#854d0e;line-height:1.5">
                            <strong>🔒 Konfirmasi/Tolak Terkunci:</strong> Admin tidak dapat konfirmasi atau tolak pemesanan jika status pembayaran belum selesai.
                        </div>
                        <button type="button" class="btn-confirm-disabled" style="width:100%" disabled>
                            &check; Konfirmasi Pesanan
                        </button>
                        <button type="button" class="btn-confirm-disabled" style="width:100%;margin-top:6px" disabled>
                            &cross; Tolak Pesanan
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Pengembalian Motor -->
<div id="returnConfirmModal" class="custom-modal">
    <div class="modal-content-card">
        <div class="modal-header-line">
            <h3>Konfirmasi Pengembalian Motor</h3>
            <button type="button" class="modal-close-btn" onclick="closeReturnModal()" aria-label="Tutup"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
        <p id="returnModalMotorLabel" style="font-size:14px;color:#556;margin-bottom:16px"></p>
        <p style="font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:18px">
            Apakah motor sudah dikembalikan?
        </p>

        <form id="returnConfirmForm" method="POST" action="">
            @csrf
            <div class="form-group">
                <label>Kondisi Motor Saat Pengembalian</label>
                <select name="condition_status">
                    <option value="good">Baik (Tidak ada kerusakan baru)</option>
                    <option value="minor_damage">Kerusakan Ringan (Baret halus)</option>
                    <option value="damaged">Rusak (Perlu perbaikan)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Level Bahan Bakar</label>
                <select name="fuel_level">
                    <option value="full">Penuh</option>
                    <option value="three_quarter">3/4</option>
                    <option value="half">1/2</option>
                    <option value="quarter">1/4</option>
                    <option value="empty">Kosong</option>
                </select>
            </div>
            <div class="form-group">
                <label>Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan fisik motor..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeReturnModal()">No (Batal)</button>
                <button type="submit" class="btn-primary" style="background:#16a34a">Yes (Sudah Kembali)</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Request Denda (Late, Damage & Other) -->
<div id="requestFineModal" class="custom-modal">
    <div class="modal-content-card">
        <div class="modal-header-line">
            <h3>Request Denda (Kerusakan / Lainnya)</h3>
            <button type="button" class="modal-close-btn" onclick="closeFineModal()" aria-label="Tutup"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>

        <form method="POST" action="{{ route('admin.fines.store') }}">
            @csrf
            <div class="form-group">
                <label>Pilih Motor (Hanya yang Sudah Kembali)</label>
                <select name="booking_item_id" id="fineBookingItemId" required>
                    @foreach($booking->items->filter(fn ($i) => $i->isReturned()) as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->motor->merk }} {{ $item->motor->model }} ({{ $item->motor->plate_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Jenis Denda</label>
                <select name="type" required>
                    <option value="damage">Damage (Kerusakan Motor)</option>
                    <option value="late">Late (Keterlambatan Manual)</option>
                    <option value="other">Other (Biaya Lain yang Disetujui)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Deskripsi / Alasan Denda</label>
                <textarea name="description" rows="3" required placeholder="Jelaskan alasan dan rincian denda..."></textarea>
            </div>

            <div class="form-group">
                <label>Jumlah Denda (Rp)</label>
                <input type="number" name="amount" min="10000" max="100000000" step="any" required placeholder="Min Rp10.000, Maks Rp100.000.000">
                <small style="color:#889;display:block;margin-top:4px">Batas nominal: Rp10.000 s/d Rp100.000.000 (contoh: 29001, 30001, 150000)</small>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeFineModal()">Batal</button>
                <button type="submit" class="btn-primary">Simpan Denda</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak Pembayaran Denda -->
<div id="rejectFineModal" class="custom-modal">
    <div class="modal-content-card">
        <div class="modal-header-line">
            <h3>Tolak Bukti Pembayaran</h3>
            <button type="button" class="modal-close-btn" onclick="closeRejectModal()" aria-label="Tutup"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>

        <form id="rejectFineForm" method="POST" action="">
            @csrf
            <div class="form-group">
                <label>Alasan Penolakan <span style="color:#e74c3c">*</span></label>
                <textarea name="rejection_reason" rows="3" required placeholder="Jelaskan alasan penolakan (misal: nominal transfer tidak sesuai, bukti buram, dll)..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn-primary" style="background:#dc2626">Tolak Bukti Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak Pembayaran Sewa -->
<div id="rejectRentalPaymentModal" class="custom-modal">
    <div class="modal-content-card">
        <div class="modal-header-line">
            <h3>Tolak Bukti Pembayaran Sewa</h3>
            <button type="button" class="modal-close-btn" onclick="closeRejectRentalPaymentModal()" aria-label="Tutup"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>

        <form method="POST" action="{{ route('admin.bookings.payments.reject', $booking) }}">
            @csrf
            <div class="form-group">
                <label>Alasan Penolakan Bukti Pembayaran <span style="color:#e74c3c">*</span></label>
                <textarea name="rejection_reason" rows="3" required placeholder="Jelaskan alasan penolakan (misal: nominal transfer tidak sesuai, bukti buram/tidak valid, dll)..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeRejectRentalPaymentModal()">Batal</button>
                <button type="submit" class="btn-primary" style="background:#dc2626">Tolak Bukti Pembayaran</button>
            </div>
        </form>
    </div>
</div>

@include('partials.fine-detail-modal')

<script>
function openRejectRentalPaymentModal() {
    document.getElementById('rejectRentalPaymentModal').classList.add('active');
}

function closeRejectRentalPaymentModal() {
    document.getElementById('rejectRentalPaymentModal').classList.remove('active');
}
function openReturnModal(itemId, motorName) {
    document.getElementById('returnModalMotorLabel').innerText = 'Motor: ' + motorName;
    var form = document.getElementById('returnConfirmForm');
    form.action = "{{ url('admin/bookings/' . $booking->id . '/items') }}/" + itemId + "/return";
    document.getElementById('returnConfirmModal').classList.add('active');
}

function closeReturnModal() {
    document.getElementById('returnConfirmModal').classList.remove('active');
}

function openFineModal(itemId) {
    if (itemId) {
        document.getElementById('fineBookingItemId').value = itemId;
    }
    document.getElementById('requestFineModal').classList.add('active');
}

function closeFineModal() {
    document.getElementById('requestFineModal').classList.remove('active');
}

function openRejectModal(fineId) {
    var form = document.getElementById('rejectFineForm');
    form.action = "{{ url('admin/fines') }}/" + fineId + "/reject";
    document.getElementById('rejectFineModal').classList.add('active');
}

function closeRejectModal() {
    document.getElementById('rejectFineModal').classList.remove('active');
}
</script>
@endsection
