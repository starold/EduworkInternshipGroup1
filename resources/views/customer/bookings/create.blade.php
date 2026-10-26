@extends('layouts.app')

@section('title', 'Pesan Motor')

@push('styles')
<style>
.create-layout { display:grid; grid-template-columns:1fr 360px; gap:28px; padding:40px 0; }
.page-title { font-size:24px; font-weight:800; color:#1a1a2e; margin-bottom:6px; }
.page-sub { font-size:14px; color:#8a8a9a; margin-bottom:28px; }
.card { background:#fff; border:1px solid #e2e4e9; border-radius:14px; padding:28px; margin-bottom:24px; }
.card-title { font-size:16px; font-weight:700; color:#1a1a2e; margin-bottom:20px; }
.form-group { margin-bottom:20px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#4a4a5a; margin-bottom:8px; }
.form-control { width:100%; padding:12px 16px; border:1.5px solid #e2e4e9; border-radius:8px; font-size:14px; font-family:'Inter',sans-serif; color:#1a1a2e; transition:all .2s; outline:none; }
.form-control:focus { border-color:#4758D6; box-shadow:0 0 0 3px rgba(71,88,214,.1); }
.form-control.is-invalid { border-color:#e74c3c; }
.invalid-feedback { color:#e74c3c; font-size:12px; margin-top:6px; display:block; }
.radio-group { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.radio-card { display:block; cursor:pointer; }
.radio-card input { display:none; }
.radio-card .radio-inner { padding:14px; border:1.5px solid #e2e4e9; border-radius:10px; text-align:center; transition:all .2s; }
.radio-card input:checked + .radio-inner { border-color:#4758D6; background:#f0f2ff; }
.radio-card .radio-inner h4 { font-size:14px; font-weight:700; color:#1a1a2e; margin-bottom:4px; }
.radio-card .radio-inner p { font-size:12px; color:#8a8a9a; }

/* Sidebar */
.sidebar-card { border:1px solid #e2e4e9; border-radius:14px; overflow:hidden; margin-bottom:20px; }
.sidebar-header { background:#f8f9fb; padding:14px 20px; font-size:14px; font-weight:700; color:#1a1a2e; border-bottom:1px solid #e2e4e9; }
.sidebar-body { padding:20px; }
.vehicle-img-wrap img { width:100%; aspect-ratio:4/3; object-fit:cover; border-radius:10px; margin-bottom:16px; }
.vehicle-name { font-size:18px; font-weight:800; color:#1a1a2e; margin-bottom:4px; }
.vehicle-merk { font-size:14px; color:#8a8a9a; margin-bottom:16px; }
.vehicle-price { font-size:22px; font-weight:800; color:#4758D6; margin-bottom:4px; }
.vehicle-price span { font-size:14px; color:#8a8a9a; font-weight:500; }
.vehicle-deposit { font-size:13px; color:#8a8a9a; margin-bottom:16px; }
.spec-row { display:flex; gap:8px; margin-bottom:8px; font-size:13px; color:#4a4a5a; }
.spec-row .tag { background:#f4f5f7; padding:4px 10px; border-radius:6px; font-weight:600; }
.summary-row { display:flex; justify-content:space-between; font-size:14px; margin-bottom:10px; }
.summary-row .lbl { color:#8a8a9a; }
.summary-row .val { color:#1a1a2e; font-weight:600; }
.summary-total { display:flex; justify-content:space-between; font-size:16px; font-weight:800; color:#1a1a2e; margin-top:12px; padding-top:12px; border-top:2px solid #e2e4e9; }

.btn-submit-booking { width:100%; padding:15px; background:#4758D6; color:#fff; font-size:15px; font-weight:800; border:none; border-radius:10px; cursor:pointer; margin-top:20px; transition:all .2s; }
.btn-submit-booking:hover { background:#3847B5; transform:translateY(-2px); box-shadow:0 8px 20px rgba(71,88,214,.4); }

@media(max-width:992px) { .create-layout { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="create-layout">
        <div>
            <h1 class="page-title">Buat Pesanan</h1>
            <p class="page-sub">Isi detail di bawah untuk melanjutkan pemesanan motor.</p>

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom:20px">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('customer.bookings.store', $motor) }}" id="booking-form">
                @csrf

                <div class="card">
                    <h3 class="card-title">Tanggal Sewa</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                        <div class="form-group">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', today()->format('Y-m-d')) }}" min="{{ today()->format('Y-m-d') }}" required>
                            @error('start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', today()->addDay()->format('Y-m-d')) }}" min="{{ today()->addDay()->format('Y-m-d') }}" required>
                            @error('end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Metode Pengambilan</h3>
                    <div class="radio-group">
                        <label class="radio-card">
                            <input type="radio" name="pickup_method" value="pickup" {{ old('pickup_method') !== 'delivery' ? 'checked' : '' }}>
                            <div class="radio-inner">
                                <h4>🏍 Ambil Sendiri</h4>
                                <p>Ambil di lokasi ED.RENT</p>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="pickup_method" value="delivery" {{ old('pickup_method') === 'delivery' ? 'checked' : '' }}>
                            <div class="radio-inner">
                                <h4>🚚 Diantar</h4>
                                <p>+Rp15.000 biaya antar</p>
                            </div>
                        </label>
                    </div>
                    <div id="delivery-address-wrap" style="margin-top:20px;display:{{ old('pickup_method') === 'delivery' ? 'block' : 'none' }}">
                        <label class="form-label">Alamat Pengiriman</label>
                        <textarea name="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror" rows="3" placeholder="Masukkan alamat lengkap...">{{ old('delivery_address') }}</textarea>
                        @error('delivery_address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Catatan Tambahan</h3>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan untuk admin (opsional)...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn-submit-booking">Konfirmasi Pesanan →</button>
            </form>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="sidebar-card">
                <div class="sidebar-header">Motor yang dipesan</div>
                <div class="sidebar-body">
                    <div class="vehicle-img-wrap">
                        <img src="{{ $motor->image_url }}" alt="{{ $motor->model }}">
                    </div>
                    <div class="vehicle-name">{{ $motor->merk }} {{ $motor->model }}</div>
                    <div class="vehicle-merk">{{ $motor->category->name }} · {{ $motor->year }} · {{ $motor->warna }}</div>
                    <div class="vehicle-price">Rp{{ number_format($motor->price_per_day, 0, ',', '.') }} <span>/hari</span></div>
                    <div class="vehicle-deposit">Deposit : Rp{{ number_format($motor->deposit, 0, ',', '.') }}</div>
                    <div class="spec-row">
                        <span class="tag">🏍 {{ $motor->plate_number }}</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-card">
                <div class="sidebar-header">Ringkasan Biaya</div>
                <div class="sidebar-body">
                    <div class="summary-row"><span class="lbl">Harga sewa</span><span class="val">Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}/hari</span></div>
                    <div class="summary-row"><span class="lbl">Durasi</span><span class="val" id="total-days-text">1 hari</span></div>
                    <div class="summary-row"><span class="lbl">Subtotal</span><span class="val" id="subtotal-text">Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}</span></div>
                    <div class="summary-row" id="delivery-fee-row" style="display:none"><span class="lbl">Biaya antar</span><span class="val">Rp15.000</span></div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span id="total-text">Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const pricePerDay = {{ $motor->price_per_day }};
const fmt = n => 'Rp' + n.toLocaleString('id-ID');

function calcDays() {
    const s = new Date(document.getElementById('start_date').value);
    const e = new Date(document.getElementById('end_date').value);
    const diff = (e - s) / (1000*60*60*24);
    return diff > 0 ? diff : 0;
}

function update() {
    const days = calcDays();
    const delivery = document.querySelector('input[name="pickup_method"]:checked').value === 'delivery' ? 15000 : 0;
    const subtotal = days * pricePerDay;
    const total = subtotal + delivery;

    document.getElementById('total-days-text').textContent = days + ' hari';
    document.getElementById('subtotal-text').textContent = fmt(subtotal);
    document.getElementById('total-text').textContent = fmt(total);
    document.getElementById('delivery-fee-row').style.display = delivery > 0 ? 'flex' : 'none';
}

document.getElementById('start_date').addEventListener('change', update);
document.getElementById('end_date').addEventListener('change', update);
document.querySelectorAll('input[name="pickup_method"]').forEach(r => {
    r.addEventListener('change', function() {
        document.getElementById('delivery-address-wrap').style.display = this.value === 'delivery' ? 'block' : 'none';
        update();
    });
});

update();
</script>
@endpush
