@extends('layouts.app')

@section('title', 'Daftar Pesanan')

@push('styles')
<style>
.page-section { padding:40px 0; }
.page-title { font-size:26px; font-weight:800; color:#0d0d0d; margin-bottom:6px; }
.page-sub { font-size:14px; color:#9a9aaa; margin-bottom:28px; }

/* Orders Table */
.orders-table-wrap { background:#fff; border:1px solid #e8e8f0; border-radius:12px; overflow:hidden; margin-bottom:48px; }
table { width:100%; border-collapse:collapse; }
thead { background:#f7f8fa; }
th { padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#9a9aaa; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #e8e8f0; }
td { padding:16px; font-size:14px; color:#4a4a5a; border-bottom:1px solid #f0f0f8; vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#fafbfd; }

/* Motor thumbnail in table */
.tbl-motor-wrap { display:flex; align-items:center; gap:12px; }
.tbl-motor-img { width:80px; height:56px; object-fit:contain; border-radius:8px; background:#f7f8fa; border:1px solid #e8e8f0; padding:4px; flex-shrink:0; }
.tbl-motor-info { }
.tbl-motor-name { font-size:14px; font-weight:700; color:#0d0d0d; }
.tbl-motor-type { font-size:12px; color:#9a9aaa; }

/* Status Badges */
.badge { display:inline-flex; align-items:center; gap:4px; padding:5px 10px; border-radius:50px; font-size:11px; font-weight:700; white-space:nowrap; }
.badge::before { content:'●'; font-size:8px; }
.badge-pending { background:#fef9c3; color:#ca8a04; }
.badge-confirmed { background:#dcfce7; color:#16a34a; }
.badge-cancelled { background:#f3f4f6; color:#6b7280; }
.badge-rejected { background:#fee2e2; color:#dc2626; }
.badge-completed { background:#dbeafe; color:#1d4ed8; }

/* Price */
.tbl-price { font-size:14px; font-weight:800; color:#0d0d0d; }

/* Action Buttons */
.btn-detail { display:inline-flex; align-items:center; gap:6px; padding:7px 18px; background:#1847ED; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; }
.btn-detail:hover { background:#1339c4; }
.btn-update { display:inline-flex; align-items:center; gap:6px; padding:7px 18px; background:#fff; color:#1847ED; border:1.5px solid #1847ED; border-radius:6px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; }
.btn-update:hover { background:#1847ED; color:#fff; }

/* Empty State */
.empty-state { text-align:center; padding:80px 0; color:#9a9aaa; }
.empty-state h3 { font-size:18px; color:#4a4a5a; margin-bottom:8px; }
.btn-cari { display:inline-block; margin-top:16px; padding:12px 28px; background:#1847ED; color:#fff; border-radius:8px; font-size:14px; font-weight:700; }

/* Popular Motors Section */
.section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.section-title { font-size:20px; font-weight:800; color:#0d0d0d; }
.section-link { font-size:13px; color:#1847ED; font-weight:600; }
.motors-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; }
.motor-card { background:#fff; border:1px solid #e8e8f0; border-radius:12px; overflow:hidden; transition:all .2s; }
.motor-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.08); }
.motor-card-img { aspect-ratio:4/3; background:#f7f8fa; display:flex; align-items:center; justify-content:center; padding:12px; }
.motor-card-img img { width:100%; height:100%; object-fit:contain; }
.motor-card-body { padding:14px; }
.motor-card-name { font-size:14px; font-weight:700; color:#0d0d0d; margin-bottom:2px; }
.motor-card-type { font-size:12px; color:#9a9aaa; margin-bottom:10px; }
.motor-status { font-size:11px; font-weight:700; color:#16a34a; display:flex; align-items:center; gap:4px; margin-bottom:10px; }
.motor-status::before { content:'●'; font-size:8px; }
.motor-plate { font-size:11px; color:#9a9aaa; font-weight:600; background:#f7f8fa; padding:3px 8px; border-radius:6px; }
.motor-card-footer { display:flex; justify-content:space-between; align-items:center; margin-top:10px; }
.motor-price { font-size:14px; font-weight:800; color:#0d0d0d; }
.motor-price-sub { font-size:11px; color:#9a9aaa; font-weight:400; }
.btn-book { padding:7px 16px; background:#1847ED; color:#fff; font-size:12px; font-weight:700; border-radius:6px; border:none; cursor:pointer; }
.btn-book:hover { background:#1339c4; }

/* Terms */
.terms-section { padding:48px 0; border-top:1px solid #e8e8f0; }
.terms-list { list-style:decimal; padding-left:20px; }
.terms-list li { font-size:14px; color:#4a4a5a; line-height:1.8; margin-bottom:10px; }
.terms-list li strong { color:#0d0d0d; }

@media(max-width:768px) {
    .orders-table-wrap { overflow-x:auto; }
    table { min-width:700px; }
    .motors-grid { grid-template-columns:repeat(2,1fr); }
}
</style>
@endpush

@section('content')
<div class="page-section">
    <div class="container">
        <h1 class="page-title">Daftar Pesanan</h1>
        <p class="page-sub">Semua riwayat dan detail pesanan motor Anda dalam satu tempat. Pastikan semua data sudah benar.</p>

        @if($bookings->count() > 0)
            <div class="orders-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kendaraan</th>
                            <th>Kode Booking</th>
                            <th>Tanggal Booking</th>
                            <th>Status</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            @php
                                $motor = $booking->items->first()?->motor;
                                $item = $booking->items->first();
                                $statusMap = [
                                    'pending' => ['label' => 'Belum bayar', 'class' => 'badge-pending'],
                                    'confirmed' => ['label' => 'Sudah bayar', 'class' => 'badge-confirmed'],
                                    'rejected' => ['label' => 'Ditolak', 'class' => 'badge-rejected'],
                                    'cancelled' => ['label' => 'Dibatalkan', 'class' => 'badge-cancelled'],
                                    'completed' => ['label' => 'Selesai rental', 'class' => 'badge-completed'],
                                ];
                                $st = $statusMap[$booking->status->value] ?? ['label' => $booking->status->label(), 'class' => 'badge-pending'];
                                // Override with payment check
                                if ($booking->payment && $booking->payment->isPaid()) {
                                    if ($booking->status->value === 'confirmed') {
                                        $st = ['label' => 'Sudah bayar', 'class' => 'badge-confirmed'];
                                    } elseif ($booking->status->value === 'pending') {
                                        $st = ['label' => 'Tunggu konfirmasi', 'class' => 'badge-pending'];
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="tbl-motor-wrap">
                                        @if($motor)
                                            <img src="{{ $motor->image_url }}" alt="{{ $motor->model }}" class="tbl-motor-img">
                                            <div class="tbl-motor-info">
                                                <div class="tbl-motor-name">{{ $motor->merk }} - {{ $motor->model }}</div>
                                                <div class="tbl-motor-type">{{ $motor->category->name ?? '' }}</div>
                                            </div>
                                        @else
                                            <span style="color:#9a9aaa">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:13px;font-weight:700;color:#0d0d0d">{{ $booking->booking_code }}</div>
                                </td>
                                <td>
                                    <div style="font-size:13px;color:#0d0d0d">{{ $booking->start_date->format('d-m-Y') }}</div>
                                    <div style="font-size:11px;color:#9a9aaa">{{ $item ? $item->total_days . ' hari' : '' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                                </td>
                                <td>
                                    <div class="tbl-price">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    <button type="button" class="btn-detail" 
                                        onclick="showBookingDetail({
                                            motorImage: '{{ $motor ? $motor->image_url : '' }}',
                                            motorModel: '{{ $motor ? $motor->model : 'Motor' }}',
                                            motorCategory: '{{ $motor && $motor->category ? $motor->category->name : '' }}',
                                            userName: '{{ auth()->user()->name }}',
                                            userPhone: '{{ auth()->user()->phone ?? '-' }}',
                                            bookingCode: '{{ $booking->booking_code }}',
                                            bookingDate: '{{ $booking->created_at->format('d M Y') }}',
                                            startDate: '{{ $booking->start_date->format('D, d M Y') }} · 17:00',
                                            endDate: '{{ $booking->end_date->format('D, d M Y') }} · 17:00',
                                            statusLabel: '{{ $st['label'] }}',
                                            statusClass: '{{ $st['class'] }}',
                                            paymentUrl: '{{ route('customer.payments.show', $booking) }}',
                                            isPending: {{ $booking->status->value === 'pending' ? 'true' : 'false' }}
                                        })">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-bottom:32px">{{ $bookings->links() }}</div>
        @else
            <div class="empty-state">
                <div style="font-size:48px;margin-bottom:12px">📋</div>
                <h3>Belum ada pesanan</h3>
                <p>Yuk sewa motor sekarang dan jelajahi Yogyakarta!</p>
                <a href="{{ route('home') }}" class="btn-cari">Pilih Motor</a>
            </div>
        @endif

        <!-- Pilihan Motor Terpopuler -->
        <div class="section-header">
            <h2 class="section-title">Pilihan Motor Terpopuler</h2>
            <a href="{{ route('home') }}" class="section-link">Lihat semua →</a>
        </div>
        <p style="font-size:14px;color:#9a9aaa;margin-bottom:20px">Pilih kendaraan yang paling sesuai dengan gaya perjalanan dan budget Anda. Semua motor terawat, siap jalan!</p>

        @if(isset($popularMotors) && $popularMotors->count() > 0)
            <div class="motors-grid">
                @foreach($popularMotors->take(4) as $motor)
                    <div class="motor-card">
                        <div class="motor-card-img">
                            <img src="{{ $motor->image_url }}" alt="{{ $motor->model }}">
                        </div>
                        <div class="motor-card-body">
                            <div class="motor-card-name">{{ $motor->model }}</div>
                            <div class="motor-card-type">{{ $motor->category->name }}</div>
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <span class="motor-status">Tersedia</span>
                                <span class="motor-plate">🏍 {{ $motor->plate_number }}</span>
                            </div>
                            <div class="motor-card-footer">
                                <div class="motor-price">
                                    Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}
                                    <span class="motor-price-sub">/ hari</span>
                                </div>
                                <a href="{{ route('customer.bookings.create', $motor) }}" class="btn-book">Booking</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Terms -->
        <div class="terms-section">
            <h2 style="font-size:20px;font-weight:800;color:#0d0d0d;margin-bottom:8px">Syarat & Ketentuan Rental Motor</h2>
            <p style="font-size:14px;color:#9a9aaa;margin-bottom:20px">Transparansi adalah kunci. Pastikan Anda memenuhi beberapa persyaratan dasar sebelum melakukan pemesanan.</p>
            <ol class="terms-list">
                <li>Lakukan pemesanan minimal <strong>1x24 jam</strong> sebelum waktu penggunaan. Setelah pesanan dikonfirmasi, penyewa wajib melakukan konfirmasi pengambilan motor melalui nomor WhatsApp atau datang ke lokasi ED.RENT.</li>
                <li>Membawa <strong>2 kartu identitas</strong> (SIM, KK, KTP, ID Kerja atau ID Pelajar).</li>
                <li>Menyertakan akun sosial media dan <strong>nomor WhatsApp</strong> yang aktif.</li>
                <li>Pada saat serah terima kendaraan, pihak penyedia rental akan melakukan <strong>pengecekan keahlian data</strong> dan kondisi kendaraan.</li>
                <li>Pihak penyedia rental <strong>berhak membatalkan</strong> apabila data tidak sesuai dan syarat sewa tidak dapat dilengkapi.</li>
                <li>Apabila pembayaran telah selesai dan kendaraan yang dipesan tidak tersedia, penyewa <strong>berhak memilih kendaraan lain</strong> yang tersedia tanpa dikenakan biaya tambahan.</li>
            </ol>
        </div>
    </div>
</div>

<!-- Modal Detail Pemesanan (Customer) -->
<div class="modal-overlay" id="bookingDetailModal" onclick="if(event.target===this)closeBookingDetailModal()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:3200;align-items:center;justify-content:center;padding:20px">
    <div class="modal-box" style="background:#fff;border-radius:12px;width:100%;max-width:520px;box-shadow:0 10px 30px rgba(0,0,0,.15);overflow:hidden">
        <div style="padding:20px 24px 14px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f0f0f5">
            <h3 style="font-size:17px;font-weight:800;color:#0d0d0d">Detail Pemesanan</h3>
            <button type="button" onclick="closeBookingDetailModal()" style="background:none;border:none;font-size:22px;color:#9a9aaa;cursor:pointer;line-height:1">×</button>
        </div>
        <div style="padding:24px">
            <!-- Motor & Pemesan Section -->
            <div style="display:grid;grid-template-columns:140px 1fr;gap:20px;margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid #f0f0f5">
                <div>
                    <div style="width:140px;height:100px;background:#f7f8fa;border-radius:8px;padding:6px;display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                        <img id="mDetailMotorImg" src="" alt="Motor" style="max-width:100%;max-height:100%;object-fit:contain">
                    </div>
                    <div id="mDetailMotorModel" style="font-size:15px;font-weight:800;color:#0d0d0d">Genio</div>
                    <div id="mDetailMotorCat" style="font-size:12px;color:#9a9aaa">Matic</div>
                </div>
                <div style="font-size:13px">
                    <div style="font-weight:700;color:#0d0d0d;margin-bottom:8px">Pemesan</div>
                    <div style="display:grid;grid-template-columns:100px 1fr;gap:6px 8px;color:#4a4a5a">
                        <span style="color:#9a9aaa">Nama</span>
                        <span id="mDetailUserName" style="font-weight:600;color:#0d0d0d">: -</span>
                        <span style="color:#9a9aaa">No. whatsapp</span>
                        <span id="mDetailUserPhone" style="font-weight:600;color:#0d0d0d">: -</span>
                        <span style="color:#9a9aaa">Kode booking</span>
                        <span id="mDetailBookingCode" style="font-weight:700;color:#1847ED">: -</span>
                        <span style="color:#9a9aaa">Tanggal booking</span>
                        <span id="mDetailBookingDate" style="font-weight:600;color:#0d0d0d">: -</span>
                    </div>
                </div>
            </div>

            <!-- Tanggal Sewa & Deadline Pemakaian -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;background:#f8f9fb;padding:14px;border-radius:8px;margin-bottom:20px">
                <div>
                    <div style="font-size:11px;color:#9a9aaa;font-weight:600;text-transform:uppercase;margin-bottom:4px">Tanggal Sewa</div>
                    <div id="mDetailStartDate" style="font-size:13px;font-weight:700;color:#0d0d0d">-</div>
                </div>
                <div>
                    <div style="font-size:11px;color:#dc2626;font-weight:700;text-transform:uppercase;margin-bottom:4px">Deadline Pemakaian (Selesai)</div>
                    <div id="mDetailEndDate" style="font-size:13px;font-weight:700;color:#0d0d0d">-</div>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <span style="font-size:12px;color:#9a9aaa;margin-right:8px">Status:</span>
                    <span id="mDetailStatus" class="badge badge-pending">Pending</span>
                </div>
                <div id="mDetailActionWrap">
                    <a id="mDetailPayBtn" href="#" class="btn-detail" style="display:none;background:#1847ED">Bayar Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showBookingDetail(data) {
    document.getElementById('mDetailMotorImg').src = data.motorImage || '';
    document.getElementById('mDetailMotorModel').textContent = data.motorModel || 'Motor';
    document.getElementById('mDetailMotorCat').textContent = data.motorCategory || '';
    document.getElementById('mDetailUserName').textContent = ': ' + data.userName;
    document.getElementById('mDetailUserPhone').textContent = ': ' + data.userPhone;
    document.getElementById('mDetailBookingCode').textContent = ': ' + data.bookingCode;
    document.getElementById('mDetailBookingDate').textContent = ': ' + data.bookingDate;
    document.getElementById('mDetailStartDate').textContent = data.startDate;
    document.getElementById('mDetailEndDate').textContent = data.endDate;
    
    const statusEl = document.getElementById('mDetailStatus');
    statusEl.textContent = data.statusLabel;
    statusEl.className = 'badge ' + data.statusClass;

    const payBtn = document.getElementById('mDetailPayBtn');
    if (data.isPending) {
        payBtn.href = data.paymentUrl;
        payBtn.style.display = 'inline-flex';
    } else {
        payBtn.style.display = 'none';
    }

    const modal = document.getElementById('bookingDetailModal');
    modal.style.display = 'flex';
}

function closeBookingDetailModal() {
    document.getElementById('bookingDetailModal').style.display = 'none';
}
</script>
@endpush
@endsection
