@extends('layouts.admin')

@section('title', 'Daftar Pesanan')
@section('page-title', 'Daftar Pesanan')

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

.btn-update-booking {
    padding: 7px 18px;
    background: #4758D6;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
}
.btn-update-booking:hover { background: #3847B5; }
.btn-view-detail {
    padding: 6px 12px;
    background: #f8f9fb;
    color: #4a4a5a;
    border: 1px solid #e2e4e9;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    margin-left: 6px;
}
.btn-view-detail:hover { border-color: #4758D6; color: #4758D6; }

/* Update Modal */
.admin-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 3500;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.admin-modal-overlay.open { display: flex; }
.admin-modal-box {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 10px 30px rgba(0,0,0,.15);
    overflow: hidden;
}
.admin-modal-header {
    padding: 20px 24px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f0f0f5;
}
.admin-modal-title { font-size: 17px; font-weight: 800; color: #1a1a2e; }
.admin-modal-body { padding: 24px; }
.m-info-grid { display: grid; grid-template-columns: 100px 1fr; gap: 6px 10px; font-size: 13px; margin-bottom: 18px; }
.m-info-lbl { color: #8a8a9a; }
.m-info-val { font-weight: 600; color: #1a1a2e; }
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
                <th>Foto & Motor</th>
                <th>Kode Booking</th>
                <th>Tanggal Booking</th>
                <th>Pelanggan</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                @php
                    $motor = $booking->items->first()?->motor;
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px">
                            @if($motor)
                                <img src="{{ $motor->image_url }}" alt="{{ $motor->model }}" style="width:64px;height:48px;object-fit:cover;border-radius:6px;background:#f8f9fb">
                                <div>
                                    <strong style="color:#1a1a2e">{{ $motor->merk }} {{ $motor->model }}</strong><br>
                                    <small style="color:#8a8a9a">{{ $motor->category->name ?? '' }} · {{ $motor->plate_number }}</small>
                                </div>
                            @else
                                <span>-</span>
                            @endif
                        </div>
                    </td>
                    <td><strong style="color:#4758D6">{{ $booking->booking_code }}</strong></td>
                    <td>
                        {{ $booking->created_at->format('d M Y') }}<br>
                        <small style="color:#8a8a9a">{{ $booking->items->sum('total_days') }} hari</small>
                    </td>
                    <td>
                        <strong>{{ $booking->user->name }}</strong><br>
                        <small style="color:#8a8a9a">{{ $booking->user->phone ?? '-' }}</small>
                    </td>
                    <td><strong>Rp{{ number_format($booking->total_price, 0, ',', '.') }}</strong></td>
                    <td><span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span></td>
                    <td>
                        <div style="display:flex;align-items:center">
                            <button type="button" class="btn-update-booking" onclick="openUpdateModal({
                                actionUrl: '{{ route('admin.bookings.updateStatus', $booking) }}',
                                motorImg: '{{ $motor ? $motor->image_url : '' }}',
                                motorName: '{{ $motor ? $motor->merk . ' ' . $motor->model : 'Motor' }}',
                                motorCategory: '{{ $motor && $motor->category ? $motor->category->name : '' }}',
                                userName: '{{ $booking->user->name }}',
                                userPhone: '{{ $booking->user->phone ?? '-' }}',
                                bookingCode: '{{ $booking->booking_code }}',
                                bookingDate: '{{ $booking->created_at->format('d M Y') }}',
                                startDate: '{{ $booking->start_date->format('D, d M Y') }} · 17:00',
                                endDate: '{{ $booking->end_date->format('D, d M Y') }} · 17:00',
                                currentStatus: '{{ $booking->status->value }}'
                            })">Update</button>
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-view-detail">Detail</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:#8a8a9a">Tidak ada pemesanan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $bookings->withQueryString()->links() }}</div>

<!-- Modal Update Pemesanan (Admin - sesuai mockup) -->
<div class="admin-modal-overlay" id="updateBookingModal" onclick="if(event.target===this)closeUpdateModal()">
    <div class="admin-modal-box">
        <div class="admin-modal-header">
            <span class="admin-modal-title">Update Pemesanan</span>
            <button type="button" onclick="closeUpdateModal()" style="background:none;border:none;font-size:22px;color:#8a8a9a;cursor:pointer">×</button>
        </div>
        <div class="admin-modal-body">
            <form id="updateBookingForm" method="POST" action="">
                @csrf @method('PUT')

                <!-- Motor Info -->
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;background:#f8f9fb;padding:12px;border-radius:8px">
                    <img id="mBookImg" src="" alt="" style="width:70px;height:50px;object-fit:cover;border-radius:6px;background:#fff">
                    <div>
                        <div id="mBookMotor" style="font-size:15px;font-weight:800;color:#1a1a2e">Genio</div>
                        <div id="mBookCat" style="font-size:12px;color:#8a8a9a">Matic</div>
                    </div>
                </div>

                <!-- Customer Details -->
                <div style="font-size:13px;font-weight:700;color:#1a1a2e;margin-bottom:8px">Pemesan</div>
                <div class="m-info-grid">
                    <span class="m-info-lbl">Nama</span>
                    <span class="m-info-val" id="mBookUser">: -</span>
                    <span class="m-info-lbl">No whatsapp</span>
                    <span class="m-info-val" id="mBookPhone">: -</span>
                    <span class="m-info-lbl">Kode booking</span>
                    <span class="m-info-val" id="mBookCode" style="color:#4758D6">: -</span>
                    <span class="m-info-lbl">Tanggal booking</span>
                    <span class="m-info-val" id="mBookDate">: -</span>
                </div>

                <!-- Tanggal Sewa & Deadline Pemakaian -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;background:#f0f3ff;padding:12px;border-radius:8px;margin-bottom:20px">
                    <div>
                        <div style="font-size:11px;color:#8a8a9a;font-weight:600;text-transform:uppercase;margin-bottom:2px">Tanggal sewa</div>
                        <div id="mBookStart" style="font-size:13px;font-weight:700;color:#1a1a2e">-</div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#e74c3c;font-weight:700;text-transform:uppercase;margin-bottom:2px">Selesai sewa (Deadline)</div>
                        <div id="mBookEnd" style="font-size:13px;font-weight:700;color:#1a1a2e">-</div>
                    </div>
                </div>

                <!-- Status Update -->
                <div style="margin-bottom:24px">
                    <label style="display:block;font-size:12px;font-weight:700;color:#8a8a9a;text-transform:uppercase;margin-bottom:6px">Status</label>
                    <select name="status" id="mBookStatusSelect" class="filter-input" style="width:100%" required>
                        <option value="pending">Menunggu (Pending)</option>
                        <option value="confirmed">Dikonfirmasi (Confirmed)</option>
                        <option value="rejected">Ditolak (Rejected)</option>
                        <option value="cancelled">Dibatalkan (Cancelled)</option>
                        <option value="completed">Selesai (Completed)</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary" style="width:100%;padding:13px;justify-content:center">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openUpdateModal(data) {
    document.getElementById('updateBookingForm').action = data.actionUrl;
    document.getElementById('mBookImg').src = data.motorImg;
    document.getElementById('mBookMotor').textContent = data.motorName;
    document.getElementById('mBookCat').textContent = data.motorCategory;
    document.getElementById('mBookUser').textContent = ': ' + data.userName;
    document.getElementById('mBookPhone').textContent = ': ' + data.userPhone;
    document.getElementById('mBookCode').textContent = ': ' + data.bookingCode;
    document.getElementById('mBookDate').textContent = ': ' + data.bookingDate;
    document.getElementById('mBookStart').textContent = data.startDate;
    document.getElementById('mBookEnd').textContent = data.endDate;
    document.getElementById('mBookStatusSelect').value = data.currentStatus;

    document.getElementById('updateBookingModal').classList.add('open');
}

function closeUpdateModal() {
    document.getElementById('updateBookingModal').classList.remove('open');
}
</script>
@endpush
