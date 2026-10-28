@extends('layouts.app')

@section('title', 'Upload Bukti Pembayaran')

@push('styles')
<style>
.payment-layout { display:grid; grid-template-columns:1fr 340px; gap:28px; padding:40px 0; }
.page-title { font-size:24px; font-weight:800; color:#1a1a2e; margin-bottom:24px; }
.card { background:#fff; border:1px solid #e2e4e9; border-radius:14px; padding:28px; margin-bottom:24px; }
.card-title { font-size:16px; font-weight:700; color:#1a1a2e; margin-bottom:20px; }

/* Payment Steps */
.steps { display:flex; align-items:center; gap:0; margin-bottom:32px; }
.step { flex:1; text-align:center; padding:16px; background:#f8f9fb; border:1px solid #e2e4e9; position:relative; }
.step:first-child { border-radius:10px 0 0 10px; }
.step:last-child { border-radius:0 10px 10px 0; }
.step.active { background:#4758D6; border-color:#4758D6; color:#fff; }
.step .step-num { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; opacity:.6; }
.step .step-label { font-size:13px; font-weight:700; }
.step.active .step-num { opacity:.8; color:#fff; }

/* Bank accounts */
.banks-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px; }
.bank-card { padding:16px; border:1.5px solid #e2e4e9; border-radius:10px; cursor:pointer; transition:all .2s; }
.bank-card:hover { border-color:#4758D6; }
.bank-card.selected { border-color:#4758D6; background:#f0f2ff; }
.bank-name { font-size:14px; font-weight:700; color:#1a1a2e; margin-bottom:4px; }
.bank-number { font-size:16px; font-weight:800; color:#4758D6; margin-bottom:2px; letter-spacing:.5px; }
.bank-holder { font-size:12px; color:#8a8a9a; }
.copy-btn { float:right; font-size:11px; color:#4758D6; font-weight:600; background:#f0f2ff; border:none; border-radius:4px; padding:3px 8px; cursor:pointer; }

.form-group { margin-bottom:20px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#4a4a5a; margin-bottom:8px; }
.form-control { width:100%; padding:12px 16px; border:1.5px solid #e2e4e9; border-radius:8px; font-size:14px; font-family:'Inter',sans-serif; color:#1a1a2e; transition:all .2s; outline:none; }
.form-control:focus { border-color:#4758D6; box-shadow:0 0 0 3px rgba(71,88,214,.1); }
.invalid-feedback { color:#e74c3c; font-size:12px; margin-top:6px; display:block; }

.upload-zone { border:2px dashed #e2e4e9; border-radius:10px; padding:40px; text-align:center; cursor:pointer; transition:all .2s; }
.upload-zone:hover { border-color:#4758D6; background:#f8f9ff; }
.upload-zone.has-file { border-color:#4758D6; background:#f0f2ff; }
.upload-zone p { font-size:14px; color:#8a8a9a; margin-top:8px; }
.upload-zone strong { font-size:15px; color:#4758D6; }
.preview-img { max-width:100%; max-height:300px; border-radius:10px; margin-top:16px; display:none; }

.btn-submit-pay { width:100%; padding:15px; background:#4758D6; color:#fff; font-size:15px; font-weight:800; border:none; border-radius:10px; cursor:pointer; margin-top:8px; transition:all .2s; }
.btn-submit-pay:hover { background:#3847B5; transform:translateY(-2px); box-shadow:0 8px 20px rgba(71,88,214,.4); }

/* Sidebar summary */
.sidebar-card { border:1px solid #e2e4e9; border-radius:14px; overflow:hidden; margin-bottom:20px; }
.sidebar-header { background:#f8f9fb; padding:14px 20px; font-size:14px; font-weight:700; color:#1a1a2e; border-bottom:1px solid #e2e4e9; }
.sidebar-body { padding:20px; }
.summary-row { display:flex; justify-content:space-between; font-size:14px; margin-bottom:10px; }
.summary-row .lbl { color:#8a8a9a; }
.summary-row .val { color:#1a1a2e; font-weight:600; }
.summary-total { display:flex; justify-content:space-between; font-size:16px; font-weight:800; color:#1a1a2e; margin-top:12px; padding-top:12px; border-top:2px solid #e2e4e9; }

/* Timer */
.timer-box { background:linear-gradient(135deg,#1a1a2e,#16213e); color:#fff; border-radius:14px; padding:24px; text-align:center; margin-bottom:20px; }
.timer-label { font-size:13px; color:rgba(255,255,255,.6); margin-bottom:8px; }
.timer-value { font-size:36px; font-weight:800; color:#FFD700; letter-spacing:2px; }
.timer-sub { font-size:12px; color:rgba(255,255,255,.5); margin-top:6px; }

@media(max-width:992px) { .payment-layout { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="payment-layout">
        <div>
            <h1 class="page-title">Upload Bukti Pembayaran</h1>

            <!-- Steps -->
            <div class="steps">
                <div class="step">
                    <div class="step-num">Langkah 1</div>
                    <div class="step-label">Detail Pesanan</div>
                </div>
                <div class="step active">
                    <div class="step-num">Langkah 2</div>
                    <div class="step-label">Pembayaran</div>
                </div>
                <div class="step">
                    <div class="step-num">Langkah 3</div>
                    <div class="step-label">Konfirmasi</div>
                </div>
            </div>

            @if($booking->payment && $booking->payment->isFailed())
                <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:10px;padding:16px;margin-bottom:20px">
                    <strong style="color:#991b1b;font-size:14px;display:block;margin-bottom:4px">⚠ Bukti Pembayaran Sebelumnya Ditolak</strong>
                    <p style="font-size:13px;color:#b91c1c;margin:0 0 6px 0">
                        <strong>Alasan:</strong> {{ $booking->payment->rejection_reason }}
                    </p>
                    <p style="font-size:12px;color:#7f1d1d;margin:0">
                        Silahkan periksa kembali nominal/rekening transfer dan upload ulang foto bukti transfer yang valid sebelum tenggat waktu.
                    </p>
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:14px;border-radius:10px;margin-bottom:20px;font-size:14px">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Bank Accounts -->
            <div class="card">
                <h3 class="card-title">Pilih Rekening Tujuan</h3>
                <div class="banks-grid">
                    @php
                        $banks = [
                            ['name' => 'BCA', 'number' => '1234567890', 'holder' => 'ED RENT'],
                            ['name' => 'BRI', 'number' => '0987654321', 'holder' => 'ED RENT'],
                            ['name' => 'Mandiri', 'number' => '1122334455', 'holder' => 'ED RENT'],
                            ['name' => 'GoPay', 'number' => '081234567890', 'holder' => 'ED RENT'],
                        ];
                    @endphp
                    @foreach($banks as $i => $bank)
                        <div class="bank-card {{ $i === 0 ? 'selected' : '' }}" onclick="selectBank(this, '{{ $bank['name'] }}')">
                            <div class="bank-name">{{ $bank['name'] }}</div>
                            <div class="bank-number">
                                {{ $bank['number'] }}
                                <button class="copy-btn" onclick="event.stopPropagation(); copyText('{{ $bank['number'] }}', this)">Salin</button>
                            </div>
                            <div class="bank-holder">a/n {{ $bank['holder'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div style="background:#fffbeb;border:1px solid #fef3c7;border-radius:10px;padding:14px;font-size:13px;color:#92400e;line-height:1.7">
                    💡 Transfer tepat sesuai jumlah total yang tertera. Simpan bukti transfer untuk diupload.
                </div>
            </div>

            <!-- Upload Form -->
            <div class="card">
                <h3 class="card-title">Upload Bukti Transfer</h3>
                <form method="POST" action="{{ route('customer.payments.upload', $booking) }}" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="payment_method" id="payment_method_input" value="BCA">

                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method_select" class="form-control @error('payment_method') is-invalid @enderror">
                            @foreach($banks as $bank)
                                <option value="{{ $bank['name'] }}">{{ $bank['name'] }}</option>
                            @endforeach
                        </select>
                        @error('payment_method') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto Bukti Transfer <span style="color:#e74c3c">*</span></label>
                        <div class="upload-zone" id="upload-zone" onclick="document.getElementById('proof-input').click()">
                            <div style="font-size:36px;margin-bottom:8px">📸</div>
                            <strong>Klik untuk upload bukti</strong>
                            <p>JPG, JPEG, atau PNG · Maks 2MB</p>
                        </div>
                        <input type="file" id="proof-input" name="payment_proof" accept="image/jpg,image/jpeg,image/png" style="display:none" required>
                        <img id="proof-preview" class="preview-img">
                        @error('payment_proof') <span class="invalid-feedback" style="display:block;margin-top:8px">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn-submit-pay">Upload & Kirim Bukti →</button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Timer -->
            <div class="timer-box">
                <div class="timer-label">⏱ Batas waktu pembayaran</div>
                <div class="timer-value" id="countdown">24:00:00</div>
                <div class="timer-sub">Bayar sebelum waktu habis</div>
            </div>

            <!-- Booking Summary -->
            <div class="sidebar-card">
                <div class="sidebar-header">Ringkasan Pesanan</div>
                <div class="sidebar-body">
                    <div style="margin-bottom:14px">
                        <div style="font-size:12px;color:#8a8a9a;margin-bottom:4px">Kode Booking</div>
                        <div style="font-size:15px;font-weight:700;color:#1a1a2e">{{ $booking->booking_code }}</div>
                    </div>
                    @foreach($booking->items as $item)
                        <div style="display:flex;gap:12px;align-items:center;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f0f0f5">
                            <img src="{{ $item->motor->image_url }}" alt="{{ $item->motor->model }}" style="width:60px;height:44px;object-fit:cover;border-radius:8px">
                            <div>
                                <div style="font-size:14px;font-weight:700;color:#1a1a2e">{{ $item->motor->model }}</div>
                                <div style="font-size:12px;color:#8a8a9a">{{ $item->total_days }} hari</div>
                            </div>
                        </div>
                    @endforeach

                    <div class="summary-row"><span class="lbl">Subtotal</span><span class="val">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</span></div>
                    <div class="summary-row"><span class="lbl">Biaya Antar</span><span class="val">{{ $booking->delivery_fee > 0 ? 'Rp'.number_format($booking->delivery_fee,0,',','.') : '-' }}</span></div>
                    <div class="summary-total">
                        <span>Total Bayar</span>
                        <span style="color:#4758D6">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Countdown timer (24 hours from booking creation)
const createdAt = new Date('{{ $booking->created_at->toIso8601String() }}');
const expiry = new Date(createdAt.getTime() + 24 * 60 * 60 * 1000);

function updateTimer() {
    const now = new Date();
    const diff = expiry - now;
    if (diff <= 0) {
        document.getElementById('countdown').textContent = 'KADALUARSA';
        document.getElementById('countdown').style.color = '#ff6b6b';
        return;
    }
    const h = Math.floor(diff / 3600000).toString().padStart(2, '0');
    const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
    const s = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
    document.getElementById('countdown').textContent = `${h}:${m}:${s}`;
}
setInterval(updateTimer, 1000);
updateTimer();

// Bank selection
function selectBank(el, name) {
    document.querySelectorAll('.bank-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('payment_method_select').value = name;
}

// Copy to clipboard
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Disalin!';
        setTimeout(() => btn.textContent = orig, 2000);
    });
}

// File upload preview
document.getElementById('proof-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const preview = document.getElementById('proof-preview');
    const zone = document.getElementById('upload-zone');
    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
    zone.classList.add('has-file');
    zone.querySelector('strong').textContent = file.name;
});
</script>
@endpush
