@extends('layouts.admin')

@section('title', 'Manajemen Denda')
@section('page-title', 'Manajemen Denda Rental')

@push('styles')
<style>
.filter-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; padding:20px; margin-bottom:20px; }
.filter-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) 120px; gap:16px; align-items:flex-end; }
.form-field label { display:block; font-size:12px; font-weight:700; color:#4a4a5a; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px; }
.form-field input, .form-field select { width:100%; padding:9px 12px; border:1.5px solid #e2e4e9; border-radius:8px; font-size:13px; }
.btn-filter { width:100%; padding:10px; background:#1847ED; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; }
.btn-filter:hover { background:#1235b2; }

.table-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; overflow:hidden; }
.custom-table { width:100%; border-collapse:collapse; font-size:13px; }
.custom-table th { background:#f8f9fb; padding:14px 16px; text-align:left; color:#1a1a2e; font-weight:700; border-bottom:1px solid #e2e4e9; white-space:nowrap; }
.custom-table td { padding:14px 16px; border-bottom:1px solid #f0f0f5; color:#334; vertical-align:middle; }
.custom-table tr:last-child td { border-bottom:none; }

.btn-detail-fine { display:inline-flex; align-items:center; gap:5px; margin-top:6px; padding:6px 10px; background:#eff6ff; color:#1847ED; border:1px solid #bfdbfe; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; text-decoration:none; transition:all .15s ease; }
.btn-detail-fine:hover { background:#1847ED; color:#fff; }

.btn-verify { padding:6px 12px; background:#16a34a; color:#fff; border:none; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; }
.btn-verify:hover { background:#15803d; }
.btn-reject { padding:6px 12px; background:#fff; color:#dc2626; border:1px solid #dc2626; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; }
.btn-reject:hover { background:#dc2626; color:#fff; }

.custom-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
.custom-modal.active { display:flex; }
.modal-content-card { background:#fff; border-radius:12px; width:100%; max-width:440px; padding:24px; box-shadow:0 10px 30px rgba(0,0,0,.2); }
.modal-header-line { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
.modal-header-line h3 { font-size:17px; font-weight:700; color:#1a1a2e; }
.modal-close-btn { background:none; border:none; font-size:20px; cursor:pointer; color:#999; }
.modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
.btn-secondary { padding:9px 18px; background:#f0f2f5; border:none; border-radius:6px; font-weight:600; cursor:pointer; color:#556; }
.btn-primary { padding:9px 18px; background:#1847ED; border:none; border-radius:6px; font-weight:600; cursor:pointer; color:#fff; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#445; margin-bottom:6px; }
.form-group textarea { width:100%; padding:10px; border:1px solid #dcdfe5; border-radius:6px; font-size:14px; }

.pagination-wrap { padding:16px 20px; border-top:1px solid #e2e4e9; }
</style>
@endpush

@section('content')
<!-- Filter bar -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.fines.index') }}">
        <div class="filter-grid">
            <div class="form-field">
                <label>Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode booking, nama, motor...">
            </div>
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
            <div class="form-field">
                <label>Tanggal Dibuat</label>
                <input type="date" name="date" value="{{ request('date') }}">
            </div>
            <div>
                <button type="submit" class="btn-filter">Filter</button>
            </div>
        </div>
    </form>
</div>

<!-- Table Card with Horizontal & Vertical Scrolling -->
<div class="table-card">
    <div class="table-scroll-wrapper">
        <div class="table-scroll-body">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Pelanggan</th>
                        <th>Motor</th>
                        <th>Jenis Denda</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Status Denda</th>
                        <th>Bukti Transfer &amp; Pembayaran</th>
                        <th>Aksi Admin</th>
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
                                'customer_name' => $booking?->user?->name ?? '-',
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
                                    <a href="{{ route('admin.bookings.show', $booking) }}" style="color:#1847ED;font-weight:700">
                                        {{ $booking->booking_code }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $booking?->user?->name ?? '-' }}</td>
                            <td>
                                @if($motor)
                                    <strong>{{ $motor->merk }} {{ $motor->model }}</strong><br>
                                    <small style="color:#889">{{ $motor->plate_number }}</small>
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
                            <td style="max-width:200px">{{ $fine->description }}</td>
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
                                    <div>
                                        <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                            Detail Denda &amp; Pembayaran
                                        </button>
                                    </div>
                                @elseif($fine->isAwaitingVerification())
                                    <span class="badge badge-pending">Menunggu Verifikasi</span>
                                    <small style="display:block;color:#854d0e;font-weight:600;margin-top:2px">{{ $fine->payment?->payment_method }}</small>
                                    <div>
                                        <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                            Detail Denda &amp; Pembayaran
                                        </button>
                                    </div>
                                @elseif($fine->hasRejectedPayment())
                                    <span class="badge badge-rejected">Bukti Ditolak</span>
                                    <small style="display:block;color:#991b1b;margin-top:2px">{{ Str::limit($fine->payment?->rejection_reason, 20) }}</small>
                                    <div>
                                        <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                            Detail Denda &amp; Pembayaran
                                        </button>
                                    </div>
                                @else
                                    <span class="badge badge-unpaid">Belum Bayar</span>
                                    <div>
                                        <button type="button" class="btn-detail-fine" onclick='openFineDetailModal(@json($fineModalData))'>
                                            Detail Denda &amp; Pembayaran
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($fine->isAwaitingVerification())
                                    <div style="display:flex;gap:4px">
                                        <form method="POST" action="{{ route('admin.fines.verify', $fine) }}" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn-verify" title="Verifikasi Lunas" onclick="return confirm('Konfirmasi bahwa pembayaran denda ini telah diterima?')">
                                                &check; Lunas
                                            </button>
                                        </form>
                                        <button type="button" class="btn-reject" title="Tolak Bukti" onclick="openRejectModal({{ $fine->id }})">
                                            &cross; Tolak
                                        </button>
                                    </div>
                                @else
                                    @if($booking)
                                        <a href="{{ route('admin.bookings.show', $booking) }}" style="padding:6px 12px;background:#f0f2f5;border-radius:6px;font-weight:600;font-size:12px;color:#1847ED;text-decoration:none">
                                            Detail
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:32px;color:#8a8a9a">
                                Belum ada data denda ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrap">
        {{ $fines->withQueryString()->links() }}
    </div>
</div>

<!-- Modal Tolak Pembayaran Denda -->
<div id="rejectFineModal" class="custom-modal">
    <div class="modal-content-card">
        <div class="modal-header-line">
            <h3>Tolak Bukti Pembayaran</h3>
            <button type="button" class="modal-close-btn" onclick="closeRejectModal()">&times;</button>
        </div>

        <form id="rejectFineForm" method="POST" action="">
            @csrf
            <div class="form-group">
                <label>Alasan Penolakan <span style="color:#e74c3c">*</span></label>
                <textarea name="rejection_reason" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn-primary" style="background:#dc2626">Tolak Bukti</button>
            </div>
        </form>
    </div>
</div>

@include('partials.fine-detail-modal')

<script>
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
