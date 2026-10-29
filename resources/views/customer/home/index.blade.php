@extends('layouts.app')

@section('title', 'ED.RENT - Rental Motor Yogyakarta')

@push('styles')
<style>
/* ---- HERO ---- */
.hero {
    position:relative;
    height:420px;
    overflow:hidden;
    background:#1a1a2e;
}
.hero-img {
    position:absolute; inset:0; width:100%; height:100%;
    object-fit:cover; object-position:center;
    opacity:.65;
}
.hero-overlay {
    position:absolute; inset:0;
    background:linear-gradient(to right, rgba(0,0,0,.7) 40%, rgba(0,0,0,.1));
}
.hero-content {
    position:relative; z-index:2;
    height:100%; display:flex; align-items:center;
}
.hero-text { max-width:520px; color:#fff; }
.hero-text h1 { font-size:38px; font-weight:800; line-height:1.2; margin-bottom:12px; }
.hero-text p { font-size:15px; color:rgba(255,255,255,.8); line-height:1.7; }

/* ---- SEARCH SECTION ---- */
.search-section { background:#fff; border-bottom:1px solid #e8e8f0; padding:20px 0; }
.search-bar { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
.search-field { flex:1; min-width:180px; }
.search-field label { display:block; font-size:11px; font-weight:700; color:#9a9aaa; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
.search-input-wrap { display:flex; align-items:center; gap:8px; border:1.5px solid #e8e8f0; border-radius:8px; padding:10px 14px; background:#fff; }
.search-input-wrap svg { width:16px; height:16px; flex-shrink:0; color:#9a9aaa; }
.search-input-wrap select,
.search-input-wrap input { border:none; outline:none; font-size:14px; font-weight:500; color:#0d0d0d; flex:1; background:transparent; }
.search-input-wrap select option { color:#0d0d0d; }
.search-divider { width:1px; height:40px; background:#e8e8f0; }
.btn-search { padding:12px 28px; background:#1847ED; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; white-space:nowrap; height:48px; }
.btn-search:hover { background:#1339c4; }

/* ---- SECTION ---- */
.section { padding:56px 0; }
.section-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; }
.section-title { font-size:22px; font-weight:800; color:#0d0d0d; }
.section-link { font-size:13px; color:#1847ED; font-weight:600; margin-top:4px; }
.section-sub { font-size:14px; color:#9a9aaa; margin-bottom:28px; }

/* ---- 3 STEPS ---- */
.steps-section { background:#f7f8fa; padding:56px 0; }
.steps-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:28px; }
.step-card { background:#fff; border:1px solid #e8e8f0; border-radius:12px; padding:24px; }
.step-num-wrap { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
.step-num-icon { width:40px; height:40px; background:#e8eeff; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; }
.step-card h3 { font-size:15px; font-weight:700; color:#0d0d0d; margin-bottom:6px; }
.step-card p { font-size:13px; color:#9a9aaa; line-height:1.6; }

/* ---- MOTOR CARDS ---- */
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

/* ---- TERMS ---- */
.terms-section { padding:56px 0; border-top:1px solid #e8e8f0; }
.terms-list { list-style:decimal; padding-left:20px; }
.terms-list li { font-size:14px; color:#4a4a5a; line-height:1.8; margin-bottom:10px; }
.terms-list li strong { color:#0d0d0d; }

@media(max-width:1024px) { .motors-grid { grid-template-columns:repeat(3, 1fr); } }
@media(max-width:768px) {
    .hero { height:300px; }
    .hero-text h1 { font-size:26px; }
    .search-bar { flex-direction:column; }
    .search-field { width:100%; }
    .search-divider { display:none; }
    .steps-grid { grid-template-columns:1fr; }
    .motors-grid { grid-template-columns:repeat(2, 1fr); }
}
@media(max-width:480px) {
    .motors-grid { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')

<!-- Hero Banner -->
<section class="hero">
    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=80" alt="Rental Motor" class="hero-img" onerror="this.src='https://via.placeholder.com/1200x420/1a1a2e/fff?text=ED.RENT'">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="container">
            <div class="hero-text">
                <h1>Rental Motor Cepat & Aman,<br>Mulai Rp75.000/hari</h1>
                <p>Pilihan terlengkap, harga transparan, dan layanan 24 jam.<br>Siap menemani setiap petualangan Anda.</p>
            </div>
        </div>
    </div>
</section>

<!-- Search Bar -->
<div class="search-section">
    <div class="container">
        <form method="GET" action="{{ route('home') }}" class="search-bar">
            <div class="search-field">
                <label>Tipe kendaraan</label>
                <div class="search-input-wrap">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <select name="category">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="search-divider"></div>
            <div class="search-field">
                <label>Cari kendaraan</label>
                <div class="search-input-wrap">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" name="search" placeholder="Nama motor..." value="{{ request('search') }}">
                </div>
            </div>
            <button type="submit" class="btn-search">Ayo Cari</button>
        </form>
    </div>
</div>

<!-- 3 Langkah Mudah -->
<section class="steps-section">
    <div class="container">
        <div class="section-header" style="margin-bottom:6px">
            <h2 class="section-title">Mulai Petualangan Anda dalam 3 Langkah Mudah</h2>
        </div>
        <p class="section-sub">Tidak perlu ribet! Proses penyewaan motor tercepat, paling aman, dan transparan.</p>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num-wrap">
                    <div class="step-num-icon">📋</div>
                </div>
                <h3>Pilih & Booking</h3>
                <p>Pilih motor impian Anda dan tentukan tanggal rental.</p>
            </div>
            <div class="step-card">
                <div class="step-num-wrap">
                    <div class="step-num-icon">💳</div>
                </div>
                <h3>Konfirmasi & Pembayaran</h3>
                <p>Selesaikan pembayaran dan dapatkan konfirmasi melalui email/WhatsApp.</p>
            </div>
            <div class="step-card">
                <div class="step-num-wrap">
                    <div class="step-num-icon">🏍</div>
                </div>
                <h3>Ambil / Diantar</h3>
                <p>Ambil motor di lokasi kami, atau konfirmasi melalui WhatsApp untuk diantar ke lokasi Anda.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pilihan Motor Terpopuler -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">Pilihan Motor Terpopuler</h2>
                <p class="section-sub">Pilih kendaraan yang paling sesuai dengan gaya perjalanan dan budget Anda. Semua motor terawat, siap jalan!</p>
            </div>
            <a href="{{ route('home') }}?view=all" class="section-link">Lihat semua →</a>
        </div>

        @if($motors->count() > 0)
            <div class="motors-grid">
                @foreach($motors->take(8) as $motor)
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
        @else
            <div style="text-align:center;padding:60px;color:#9a9aaa">
                <div style="font-size:48px;margin-bottom:12px">🏍</div>
                <p>Belum ada motor tersedia saat ini.</p>
            </div>
        @endif
    </div>
</section>

<!-- Syarat & Ketentuan -->
<div class="terms-section">
    <div class="container">
        <h2 class="section-title" style="margin-bottom:8px">Syarat & Ketentuan Rental Motor</h2>
        <p style="font-size:14px;color:#9a9aaa;margin-bottom:24px">Transparansi adalah kunci. Pastikan Anda memenuhi beberapa persyaratan dasar sebelum melakukan pemesanan.</p>
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

@endsection
