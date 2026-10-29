@extends('layouts.app')

@section('title', 'Daftar Kendaraan')

@push('styles')
<style>
.page-section { padding:40px 0; }
.page-title { font-size:26px; font-weight:800; color:#0d0d0d; margin-bottom:6px; }
.page-sub { font-size:14px; color:#9a9aaa; margin-bottom:28px; }

/* Filter Bar */
.filter-bar {
    display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;
    background:#fff; border:1px solid #e8e8f0; border-radius:10px;
    padding:16px; margin-bottom:28px;
}
.filter-field { flex:1; min-width:160px; }
.filter-field label { display:block; font-size:11px; font-weight:700; color:#9a9aaa; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
.filter-input-wrap { display:flex; align-items:center; gap:8px; border:1.5px solid #e8e8f0; border-radius:8px; padding:9px 12px; }
.filter-input-wrap svg { width:15px; height:15px; flex-shrink:0; color:#9a9aaa; }
.filter-input-wrap select, .filter-input-wrap input { border:none; outline:none; font-size:13px; color:#0d0d0d; flex:1; background:transparent; min-width:0; }
.filter-divider { width:1px; height:40px; background:#e8e8f0; flex-shrink:0; }
.btn-filter { padding:11px 24px; background:#1847ED; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap; height:44px; display:flex; align-items:center; gap:6px; }
.btn-filter:hover { background:#1339c4; }
.btn-reset { padding:11px 16px; background:#f7f8fa; color:#9a9aaa; border:1.5px solid #e8e8f0; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; height:44px; text-decoration:none; display:flex; align-items:center; }
.btn-reset:hover { border-color:#1847ED; color:#1847ED; }

/* Results header */
.results-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.results-count { font-size:14px; color:#9a9aaa; }
.results-count strong { color:#0d0d0d; }

/* Motor Grid */
.motors-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; }
.motor-card { background:#fff; border:1px solid #e8e8f0; border-radius:12px; overflow:hidden; transition:all .2s; }
.motor-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.1); }
.motor-card-img { aspect-ratio:4/3; overflow:hidden; background:#f7f8fa; display:flex; align-items:center; justify-content:center; padding:12px; }
.motor-card-img img { width:100%; height:100%; object-fit:contain; transition:transform .3s; }
.motor-card:hover .motor-card-img img { transform:scale(1.06); }
.motor-card-body { padding:14px; }
.motor-card-name { font-size:14px; font-weight:700; color:#0d0d0d; margin-bottom:2px; }
.motor-card-type { font-size:12px; color:#9a9aaa; margin-bottom:10px; }
.motor-card-meta { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
.motor-plate { font-size:11px; color:#9a9aaa; font-weight:600; background:#f7f8fa; padding:3px 8px; border-radius:6px; }
.motor-status { font-size:11px; font-weight:700; color:#16a34a; display:flex; align-items:center; gap:4px; }
.motor-status::before { content:'●'; font-size:8px; }
.motor-card-footer { display:flex; justify-content:space-between; align-items:center; }
.motor-price { font-size:14px; font-weight:800; color:#0d0d0d; }
.motor-price-sub { font-size:11px; color:#9a9aaa; font-weight:400; }
.btn-book { padding:7px 16px; background:#1847ED; color:#fff; font-size:12px; font-weight:700; border-radius:6px; border:none; cursor:pointer; }
.btn-book:hover { background:#1339c4; }

/* Empty */
.empty-state { text-align:center; padding:80px 0; color:#9a9aaa; }
.empty-state h3 { font-size:18px; color:#4a4a5a; margin-bottom:8px; }

/* Terms */
.terms-section { padding:48px 0; border-top:1px solid #e8e8f0; }
.terms-list { list-style:decimal; padding-left:20px; }
.terms-list li { font-size:14px; color:#4a4a5a; line-height:1.8; margin-bottom:10px; }
.terms-list li strong { color:#0d0d0d; }

@media(max-width:1024px) { .motors-grid { grid-template-columns:repeat(3,1fr); } }
@media(max-width:768px) {
    .filter-bar { flex-direction:column; }
    .filter-field { width:100%; }
    .filter-divider { display:none; }
    .motors-grid { grid-template-columns:repeat(2,1fr); }
}
@media(max-width:480px) { .motors-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="page-section">
    <div class="container">
        <h1 class="page-title">Daftar Kendaraan</h1>
        <p class="page-sub">Pilih kendaraan yang paling sesuai dengan gaya perjalanan dan budget Anda. Semua motor terawat, siap jalan!</p>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('home') }}" class="filter-bar">
            <div class="filter-field">
                <label>Tipe kendaraan</label>
                <div class="filter-input-wrap">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19l-7-7 7-7"/><path d="M15 5l7 7-7 7"/></svg>
                    <select name="category">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="filter-divider"></div>
            <div class="filter-field">
                <label>Cari kendaraan</label>
                <div class="filter-input-wrap">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" name="search" placeholder="Nama motor..." value="{{ request('search') }}">
                </div>
            </div>
            <button type="submit" class="btn-filter">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Ayo Cari
            </button>
            @if(request('search') || request('category'))
                <a href="{{ route('home') }}" class="btn-reset">Reset</a>
            @endif
        </form>

        <!-- Results Bar -->
        <div class="results-bar">
            <p class="results-count">Menampilkan <strong>{{ $motors->total() }}</strong> kendaraan</p>
        </div>

        <!-- Motors Grid -->
        @if($motors->count() > 0)
            <div class="motors-grid">
                @foreach($motors as $motor)
                    <div class="motor-card">
                        <a href="{{ route('motors.show', $motor) }}" style="text-decoration:none;color:inherit">
                            <div class="motor-card-img">
                                <img src="{{ $motor->image_url }}" alt="{{ $motor->model }}">
                            </div>
                            <div class="motor-card-body">
                                <div class="motor-card-name">{{ $motor->merk }} {{ $motor->model }}</div>
                                <div class="motor-card-type">{{ $motor->category->name }}</div>
                                <div class="motor-card-meta">
                                    <span class="motor-plate">🏍 {{ $motor->plate_number }}</span>
                                    <span class="motor-status">Tersedia</span>
                                </div>
                                <div class="motor-card-footer">
                                    <div class="motor-price">
                                        Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}
                                        <span class="motor-price-sub">/ hari</span>
                                    </div>
                                    <span class="btn-book">Booking</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div style="margin-top:32px">{{ $motors->withQueryString()->links() }}</div>
        @else
            <div class="empty-state">
                <div style="font-size:48px;margin-bottom:12px">🏍</div>
                <h3>Motor tidak ditemukan</h3>
                <p>Coba kata kunci lain atau hapus filter</p>
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
@endsection
