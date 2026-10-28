@extends('layouts.app')

@section('title', 'Denda Saya')

@push('styles')
<style>
.fines-page { padding:40px 0; }
.page-title { font-size:24px; font-weight:800; color:#1a1a2e; margin-bottom:24px; }
.card { background:#fff; border:1px solid #e2e4e9; border-radius:14px; padding:24px; margin-bottom:24px; }
.summary-banner { background:linear-gradient(135deg, #1e293b, #0f172a); color:#fff; border-radius:14px; padding:24px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; }
.summary-banner .lbl { font-size:13px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px; font-weight:600; }
.summary-banner .val { font-size:28px; font-weight:800; color:#ef4444; }

.filter-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) 120px; gap:16px; align-items:flex-end; margin-bottom:20px; }
.form-field label { display:block; font-size:12px; font-weight:700; color:#4a4a5a; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px; }
.form-field select { width:100%; padding:10px 14px; border:1.5px solid #e2e4e9; border-radius:8px; font-size:14px; }
.btn-filter { width:100%; padding:11px; background:#1847ED; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer; }
.btn-filter:hover { background:#1235b2; }

.fines-table { width:100%; border-collapse:collapse; font-size:14px; }
.fines-table th { background:#f8f9fb; padding:14px 16px; text-align:left; color:#1a1a2e; font-weight:700; border-bottom:1px solid #e2e4e9; white-space:nowrap; }
.fines-table td { padding:16px; border-bottom:1px solid #f0f0f5; color:#334; vertical-align:middle; }
.fines-table tr:last-child td { border-bottom:none; }

.btn-detail-fine { display:inline-flex; align-items:center; gap:5px; padding:7px 12px; background:#eff6ff; color:#1847ED; border:1px solid #bfdbfe; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; text-decoration:none; transition:all .15s ease; }
.btn-detail-fine:hover { background:#1847ED; color:#fff; }

.btn-pay-fine { display:inline-block; padding:7px 14px; background:#1847ED; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none; cursor:pointer; }
.btn-pay-fine:hover { background:#1235b2; }
.btn-reupload { display:inline-block; padding:7px 14px; background:#dc2626; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none; cursor:pointer; }
.btn-reupload:hover { background:#b91c1c; }
</style>
@endpush

@section('content')
<div class="container fines-page">
    <h1 class="page-title">Daftar Denda Saya</h1>

    <!-- Summary banner -->
    <div class="summary-banner">
        <div>
            <div class="lbl">Total Denda Belum Lunas</div>
            <div class="val">Rp{{ number_format($totalUnpaid, 0, ',', '.') }}</div>
        </div>
        <div>
            @if($totalUnpaid > 0)
                <span style="background:#fee2e2;color:#b91c1c;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:700">
                    Ada Denda Aktif
                </span>
            @else
                <span style="background:#dcfce7;color:#15803d;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:700">
                    Tidak Ada Kewajiban Denda
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;padding:14px;border-radius:10px;margin-bottom:20px;font-size:14px">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter form -->
    <div class="card">
        <form method="GET" action="{{ route('customer.fines.index') }}">
            <div class="filter-grid">
                <div class="form-field">
                    <label>Jenis Denda</label>
                    <select name="type">
                        <option value="">Semua Jenis</option>
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label>Status Pembayaran</label>
                    <select name="status">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-filter">Filter</button>
                </div>
            </div>
        </form>

        <!-- Fines Table with Horizontal & Vertical Scrolling -->
        <div class="table-scroll-wrapper">
            <div class="table-scroll-body">
                <table class="fines-table">
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Motor</th>
                            <th>Jenis Denda</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th>Status Denda</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fines as $fine)
                            @php
                                $booking = $fine->bookingItem?->booking;
                                $motor = $fine->bookingItem?->motor;
                                $fineModalData = [
                                    'id' => $fine->id,
                                    'booking_code' => $booking?->booking_code ?? '-',
                                    'customer_name' => auth()->user()->name,
                                    'motor' => $motor ? "{$motor->merk} {$motor->model} ({$motor->plate_number})" : '-',
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
                                <td>
                                    @if($booking)
                                        <a href="{{ route('customer.bookings.show', $booking) }}" style="color:#1847ED;font-weight:700">
                                            {{ $booking->booking_code }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($motor)
                                        <strong>{{ $motor->merk }} {{ $motor->model }}</strong>
                                        <small style="display:block;color:#889">{{ $motor->plate_number }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $fine->type->value === 'late' ? 'pending' : ($fine->type->value === 'damage' ? 'rejected' : 'cancelled') }}">
                                        {{ $fine->type->label() }}
                                    </span>
                                    @if($fine->isLate() && $fine->days_late > 0)
                                        <br><small style="color:#dc2626;font-weight:600">{{ $fine->days_late }} hari</small>
                                    @endif
                                </td>
                                <td style="max-width:240px">{{ $fine->description }}</td>
                                <td><strong style="color:#1a1a2e">{{ $fine->formatted_amount }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ $fine->status->value === 'paid' ? 'completed' : 'unpaid' }}">
                                        {{ $fine->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    @if($fine->isPaid())
                                        <span class="badge badge-completed">Lunas</span>
                                        <small style="display:block;color:#166534;margin-top:2px">{{ $fine->paid_at?->format('d/m/Y H:i') }}</small>
                                    @elseif($fine->isAwaitingVerification())
                                        <span class="badge badge-pending">Menunggu Verifikasi Admin</span>
                                        <small style="display:block;color:#854d0e;margin-top:2px">{{ $fine->payment?->payment_method }}</small>
                                    @elseif($fine->hasRejectedPayment())
                                        <span class="badge badge-rejected">Bukti Ditolak</span>
                                    @else
                                        <span class="badge badge-unpaid">Belum Dibayar</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start">
                                        <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                            Detail Denda &amp; Pembayaran
                                        </button>
                                        @if($fine->isPaid())
                                            <a href="{{ route('customer.fines.payment', $fine) }}" style="font-size:12px;color:#1847ED;font-weight:600;text-decoration:none">
                                                Halaman Bayar &rarr;
                                            </a>
                                        @elseif($fine->isAwaitingVerification())
                                            <a href="{{ route('customer.fines.payment', $fine) }}" style="font-size:12px;color:#ca8a04;font-weight:700;text-decoration:none">
                                                Cek Status Bukti &rarr;
                                            </a>
                                        @elseif($fine->hasRejectedPayment())
                                            <a href="{{ route('customer.fines.payment', $fine) }}" class="btn-reupload">
                                                Unggah Ulang Bukti
                                            </a>
                                        @else
                                            <a href="{{ route('customer.fines.payment', $fine) }}" class="btn-pay-fine">
                                                Bayar Sekarang
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:32px;color:#8a8a9a">
                                    Tidak ada data denda ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:20px">
            {{ $fines->withQueryString()->links() }}
        </div>
    </div>
</div>

@include('partials.fine-detail-modal')
@endsection
