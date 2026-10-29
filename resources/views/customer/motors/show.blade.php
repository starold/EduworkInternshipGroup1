@extends('layouts.app')

@section('title', $motor->merk . ' ' . $motor->model . ' - Detail Kendaraan')

@push('styles')
<style>
.detail-container { padding: 36px 0 60px; }
.detail-back { display:inline-flex; align-items:center; gap:8px; font-size:14px; font-weight:600; color:var(--text-muted); margin-bottom:24px; text-decoration:none; }
.detail-back:hover { color:var(--primary); }

.detail-main-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 36px;
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 40px;
    margin-bottom: 50px;
}
.motor-gallery {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--bg-light);
    border-radius: var(--radius-lg);
    padding: 30px;
    min-height: 340px;
}
.motor-gallery img {
    max-width: 100%;
    max-height: 280px;
    object-fit: contain;
    transition: transform .3s;
}
.motor-gallery img:hover { transform: scale(1.03); }

.detail-info { display: flex; flex-direction: column; }
.motor-header { margin-bottom: 20px; }
.motor-title-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.motor-title { font-size: 26px; font-weight: 800; color: var(--text-dark); }
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: var(--success);
    background: var(--success-bg);
    padding: 4px 12px;
    border-radius: 50px;
}
.status-pill.rented { color: var(--warning); background: var(--warning-bg); }
.motor-cat-badge { font-size: 13px; color: var(--text-muted); font-weight: 500; }

.specs-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 24px;
    padding: 18px 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
}
.spec-item { display: flex; flex-direction: column; }
.spec-label { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-bottom: 2px; }
.spec-val { font-size: 14px; font-weight: 700; color: var(--text-dark); }

.desc-section { margin-bottom: 20px; }
.desc-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
.desc-text { font-size: 13px; color: var(--text-body); line-height: 1.7; }

.equipment-section { margin-bottom: 24px; }
.equipment-list { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.equipment-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-body); }
.equipment-item svg { width: 16px; height: 16px; color: var(--primary); flex-shrink: 0; }

.price-action-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 20px;
    border-top: 1px solid var(--border);
}
.price-tag { display: flex; flex-direction: column; }
.price-amount { font-size: 24px; font-weight: 800; color: var(--primary); }
.price-unit { font-size: 12px; color: var(--text-muted); }
.btn-book-now {
    padding: 13px 36px;
    background: var(--primary);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    transition: var(--transition);
}
.btn-book-now:hover { background: var(--primary-dark); transform: translateY(-1px); }

/* Duration Modal */
.duration-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 3500;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.duration-modal-overlay.open { display: flex; }
.duration-modal-card {
    background: #fff;
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 440px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
}
.duration-modal-header {
    padding: 20px 24px 14px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}
.duration-modal-title { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 4px; }
.duration-modal-sub { font-size: 13px; color: var(--text-muted); }
.duration-modal-body { padding: 10px 24px 24px; }

.duration-options { display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px; }
.duration-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: var(--transition);
}
.duration-option:hover { border-color: var(--primary); background: #f9faff; }
.duration-option.selected { border-color: var(--primary); background: #f0f3ff; }
.duration-info { display: flex; flex-direction: column; }
.duration-name { font-size: 14px; font-weight: 700; color: var(--text-dark); }
.duration-date { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
.duration-radio { accent-color: var(--primary); width: 18px; height: 18px; }

.btn-checkout-duration {
    width: 100%;
    padding: 14px;
    background: var(--primary);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    transition: var(--transition);
}
.btn-checkout-duration:hover { background: var(--primary-dark); }

/* Recommendations Section */
.recom-section { margin-top: 20px; }
.recom-title { font-size: 20px; font-weight: 800; color: var(--text-dark); margin-bottom: 24px; }
.recom-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.motor-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    padding: 16px;
    display: flex;
    flex-direction: column;
    transition: var(--transition);
}
.motor-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: var(--primary); }
.motor-card-img {
    height: 140px;
    width: 100%;
    object-fit: contain;
    margin-bottom: 12px;
}
.motor-card-name { font-size: 16px; font-weight: 700; color: var(--text-dark); }
.motor-card-cat { font-size: 12px; color: var(--text-muted); margin-bottom: 10px; }
.motor-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 12px;
    border-top: 1px solid var(--border);
}
.motor-card-price { font-size: 15px; font-weight: 800; color: var(--primary); }
.btn-card-book {
    padding: 7px 16px;
    background: var(--primary);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    border-radius: var(--radius);
    text-decoration: none;
}
.btn-card-book:hover { background: var(--primary-dark); }

@media(max-width: 900px) {
    .detail-main-card { grid-template-columns: 1fr; }
    .recom-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 480px) {
    .recom-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="container detail-container">
    <a href="{{ route('vehicles.index') }}" class="detail-back">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Kendaraan
    </a>

    <!-- Main Detail Card -->
    <div class="detail-main-card">
        <div class="motor-gallery">
            <img src="{{ $motor->image_url }}" alt="{{ $motor->merk }} {{ $motor->model }}">
        </div>

        <div class="detail-info">
            <div class="motor-header">
                <div class="motor-title-row">
                    <h1 class="motor-title">{{ $motor->merk }} {{ $motor->model }}</h1>
                    @if($motor->isAvailable())
                        <span class="status-pill">• Tersedia</span>
                    @else
                        <span class="status-pill rented">• Sedang Disewa</span>
                    @endif
                </div>
                <div class="motor-cat-badge">{{ $motor->category->name }} · {{ $motor->year }}</div>
            </div>

            <!-- Specs Grid -->
            <div class="specs-grid">
                <div class="spec-item">
                    <span class="spec-label">Brand</span>
                    <span class="spec-val">{{ $motor->merk }}</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Tipe</span>
                    <span class="spec-val">{{ $motor->category->name }}</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Kapasitas Tangki</span>
                    <span class="spec-val">{{ $motor->warna ? $motor->warna : '4.2 L' }}</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">No. Plat</span>
                    <span class="spec-val">{{ $motor->plate_number }}</span>
                </div>
            </div>

            <!-- Description -->
            <div class="desc-section">
                <div class="desc-title">Deskripsi</div>
                <div class="desc-text">
                    {{ $motor->description ?: 'Motor bebek/matic yang terkenal sangat irit bahan bakar dan tangguh. Pilihan ideal untuk Anda yang mencari kendaraan praktis, lincah, dan handal untuk mobilitas sehari-hari di Yogyakarta.' }}
                </div>
            </div>

            <!-- Equipment -->
            <div class="equipment-section">
                <div class="desc-title" style="margin-bottom:8px">Perlengkapan</div>
                <div class="equipment-list">
                    <div class="equipment-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                        Bensin 1 Liter
                    </div>
                    <div class="equipment-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                        Helm SNI 2 buah
                    </div>
                    <div class="equipment-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                        STNK Asli
                    </div>
                    <div class="equipment-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                        Sarung tangan 1 buah
                    </div>
                </div>
            </div>

            <!-- Price & Booking -->
            <div class="price-action-bar">
                <div class="price-tag">
                    <span class="price-amount">Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}</span>
                    <span class="price-unit">/ hari</span>
                </div>
                @if($motor->isAvailable())
                    <button type="button" class="btn-book-now" onclick="openDurationModal()">Booking</button>
                @else
                    <button type="button" class="btn-book-now" style="background:#cbd5e1;cursor:not-allowed" disabled>Tidak Tersedia</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Recommendations Grid -->
    @if(isset($recommendations) && $recommendations->count() > 0)
    <div class="recom-section">
        <h2 class="recom-title">Rekomendasi Kendaraan</h2>
        <div class="recom-grid">
            @foreach($recommendations as $rec)
                <div class="motor-card">
                    <img src="{{ $rec->image_url }}" alt="{{ $rec->model }}" class="motor-card-img">
                    <div class="motor-card-name">{{ $rec->merk }} {{ $rec->model }}</div>
                    <div class="motor-card-cat">{{ $rec->category->name }} · {{ $rec->plate_number }}</div>
                    <div class="motor-card-footer">
                        <span class="motor-card-price">Rp{{ number_format($rec->price_per_day, 0, ',', '.') }}/hr</span>
                        <a href="{{ route('motors.show', $rec) }}" class="btn-card-book">Booking</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Modal Pilih Durasi Sewa -->
<div class="duration-modal-overlay" id="durationModal" onclick="if(event.target===this)closeDurationModal()">
    <div class="duration-modal-card">
        <div class="duration-modal-header">
            <div>
                <div class="duration-modal-title">Pilih Durasi Sewa</div>
                <div class="duration-modal-sub">Durasi sewa (Maks 5 hari)</div>
            </div>
            <button type="button" onclick="closeDurationModal()" style="background:none;border:none;font-size:22px;color:var(--text-muted);cursor:pointer">×</button>
        </div>
        <div class="duration-modal-body">
            <form action="{{ route('customer.bookings.create', $motor) }}" method="GET">
                <div class="duration-options">
                    @php
                        \Carbon\Carbon::setLocale('id');
                        $now = now();
                    @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @php
                            $endDate = $now->copy()->addDays($i);
                            $dateLabel = $endDate->translatedFormat('D, d F');
                        @endphp
                        <label class="duration-option {{ $i === 1 ? 'selected' : '' }}" onclick="selectDurationOption(this)">
                            <div class="duration-info">
                                <span class="duration-name">{{ $i }} hari</span>
                                <span class="duration-date">Selesai: {{ $dateLabel }}</span>
                            </div>
                            <input type="radio" name="days" value="{{ $i }}" class="duration-radio" {{ $i === 1 ? 'checked' : '' }}>
                        </label>
                    @endfor
                </div>
                <button type="submit" class="btn-checkout-duration">Checkout</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDurationModal() {
    document.getElementById('durationModal').classList.add('open');
}
function closeDurationModal() {
    document.getElementById('durationModal').classList.remove('open');
}
function selectDurationOption(el) {
    document.querySelectorAll('.duration-option').forEach(opt => opt.classList.remove('selected'));
    el.classList.add('selected');
    const radio = el.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}
</script>
@endpush

