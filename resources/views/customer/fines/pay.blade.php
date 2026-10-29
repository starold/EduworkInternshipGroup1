@extends('layouts.app')

@section('title', 'Pembayaran Denda')

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
.sidebar-card { border:1px solid #e2e4e9; border-radius:14px; overflow:hidden; margin-bottom:20px; background:#fff; }
.sidebar-header { background:#f8f9fb; padding:14px 20px; font-size:14px; font-weight:700; color:#1a1a2e; border-bottom:1px solid #e2e4e9; }
.sidebar-body { padding:20px; }
.summary-row { display:flex; justify-content:space-between; font-size:14px; margin-bottom:10px; }
.summary-row .lbl { color:#8a8a9a; }
.summary-row .val { color:#1a1a2e; font-weight:600; }
.summary-total { display:flex; justify-content:space-between; font-size:16px; font-weight:800; color:#1a1a2e; margin-top:12px; padding-top:12px; border-top:2px solid #e2e4e9; }

.badge { display:inline-flex; padding:4px 10px; border-radius:50px; font-size:11px; font-weight:700; text-transform:uppercase; }
.badge-unpaid { background:#f8d7da; color:#721c24; }
.badge-paid { background:#d4edda; color:#155724; }
.badge-pending { background:#fff3cd; color:#856404; }
.badge-failed { background:#f8d7da; color:#721c24; }

@media(max-width:992px) { .payment-layout { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="payment-layout">
        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                <h1 class="page-title" style="margin-bottom:0">Pembayaran Denda</h1>
                <a href="{{ route('customer.bookings.show', $booking) }}" style="color:#4758D6;font-size:14px;font-weight:600">← Kembali ke Pesanan</a>
            </div>

            <!-- Steps identical to rental payment -->
            <div class="steps">
                <div class="step">
                    <div class="step-num">Langkah 1</div>
                    <div class="step-label">Rincian Denda</div>
                </div>
                <div class="step {{ ! $fine->isPaid() ? 'active' : '' }}">
                    <div class="step-num">Langkah 2</div>
                    <div class="step-label">Unggah Bukti</div>
                </div>
                <div class="step {{ $fine->isPaid() ? 'active' : '' }}">
                    <div class="step-num">Langkah 3</div>
                    <div class="step-label">Verifikasi Admin</div>
                </div>
            </div>

            @if(session('success'))
                <div style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;padding:14px;border-radius:10px;margin-bottom:20px;font-size:14px">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:14px;border-radius:10px;margin-bottom:20px;font-size:14px">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:14px;border-radius:10px;margin-bottom:20px;font-size:14px">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Status Alerts -->
            @if($fine->isPaid())
                <div class="card" style="background:#f0fdf4;border-color:#bbf7d0;text-align:center;padding:32px">
                    <div style="font-size:48px;margin-bottom:12px">✅</div>
                    <h3 style="color:#166534;font-size:18px;margin-bottom:6px">Denda Telah Lunas</h3>
                    <p style="color:#15803d;font-size:14px">
                        Pembayaran telah diverifikasi oleh admin pada {{ $fine->paid_at?->format('d M Y, H:i') }}.
                    </p>
                    @if($fine->payment?->proof_url)
                        <div style="margin-top:16px">
                            <a href="{{ $fine->payment->proof_url }}" target="_blank" style="color:#4758D6;font-size:13px;font-weight:600">
                                Lihat Bukti Pembayaran ↗
                            </a>
                        </div>
                    @endif
                </div>
            @elseif($fine->isAwaitingVerification())
                <div class="card" style="background:#fffbeb;border-color:#fef3c7;padding:24px">
                    <div style="display:flex;gap:14px;align-items:flex-start">
                        <div style="font-size:32px">⏳</div>
                        <div>
                            <h3 style="color:#92400e;font-size:16px;margin-bottom:4px">Menunggu Verifikasi Admin</h3>
                            <p style="color:#b45309;font-size:14px;line-height:1.6">
                                Bukti transfer telah Anda kirimkan menggunakan metode <strong>{{ $fine->payment?->payment_method }}</strong> pada {{ $fine->payment?->updated_at->format('d M Y, H:i') }}. Pembayaran sedang diverifikasi oleh admin kami.
                            </p>
                            @if($fine->payment?->proof_url)
                                <div style="margin-top:12px">
                                    <img src="{{ $fine->payment->proof_url }}" alt="Bukti Transfer" style="max-height:180px;border-radius:8px;border:1px solid #fde68a">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Rejection alert if prior payment was rejected -->
                @if($fine->hasRejectedPayment())
                    <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:10px;padding:16px;margin-bottom:20px;color:#991b1b">
                        <strong style="font-size:14px">⚠ Pembayaran Sebelumnya Ditolak</strong>
                        <p style="font-size:13px;margin-top:4px;color:#b91c1c">
                            Alasan penolakan: <em>"{{ $fine->payment?->rejection_reason ?: 'Bukti transfer tidak jelas / tidak valid.' }}"</em>
                        </p>
                        <p style="font-size:13px;margin-top:4px">Silakan periksa kembali transfer Anda dan unggah bukti pembayaran yang valid di bawah ini.</p>
                    </div>
                @endif

                <!-- Bank Accounts (reusable exact box) -->
                <div class="card">
                    <h3 class="card-title">Pilih Rekening Tujuan</h3>
                    <div class="banks-grid">
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
                        💡 Transfer tepat sesuai nominal denda: <strong>{{ $fine->formatted_amount }}</strong>. Simpan foto resi/bukti transfer untuk diunggah.
                    </div>
                </div>

                <!-- Upload Form -->
                <div class="card">
                    <h3 class="card-title">Upload Bukti Pembayaran Denda</h3>
                    <form method="POST" action="{{ route('customer.fines.uploadProof', $fine) }}" enctype="multipart/form-data">
                        @csrf

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
                                <strong>Klik untuk upload bukti pembayaran</strong>
                                <p>Format: JPG, JPEG, atau PNG · Maksimal 2MB</p>
                            </div>
                            <input type="file" id="proof-input" name="payment_proof" accept="image/jpg,image/jpeg,image/png" style="display:none" required>
                            <img id="proof-preview" class="preview-img">
                            @error('payment_proof') <span class="invalid-feedback" style="display:block;margin-top:8px">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn-submit-pay">Konfirmasi Pembayaran Denda</button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar Summary -->
        <div>
            <div class="sidebar-card">
                <div class="sidebar-header">Detail Kewajiban Denda</div>
                <div class="sidebar-body">
                    <div style="margin-bottom:14px">
                        <div style="font-size:12px;color:#8a8a9a;margin-bottom:4px">Kode Booking</div>
                        <div style="font-size:15px;font-weight:700;color:#1a1a2e">{{ $booking->booking_code }}</div>
                    </div>

                    <div style="display:flex;gap:12px;align-items:center;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f0f0f5">
                        <img src="{{ $fine->bookingItem->motor->image_url }}" alt="{{ $fine->bookingItem->motor->model }}" style="width:60px;height:44px;object-fit:cover;border-radius:8px">
                        <div>
                            <div style="font-size:14px;font-weight:700;color:#1a1a2e">{{ $fine->bookingItem->motor->merk }} {{ $fine->bookingItem->motor->model }}</div>
                            <div style="font-size:12px;color:#8a8a9a">Plat: {{ $fine->bookingItem->motor->plate_number }}</div>
                        </div>
                    </div>

                    <div class="summary-row"><span class="lbl">ID Denda</span><span class="val">#{{ $fine->id }}</span></div>
                    <div class="summary-row"><span class="lbl">Jenis Denda</span><span class="val">{{ $fine->type->label() }}</span></div>
                    <div class="summary-row"><span class="lbl">Status Denda</span><span class="val"><span class="badge badge-{{ $fine->status->value }}">{{ $fine->status->label() }}</span></span></div>
                    <div class="summary-row"><span class="lbl">Waktu Dibuat</span><span class="val">{{ $fine->created_at->format('d/m/Y') }}</span></div>

                    <div style="margin-top:10px;padding-top:10px;border-top:1px dashed #e2e4e9">
                        <div style="font-size:12px;color:#8a8a9a;margin-bottom:4px">Deskripsi Denda</div>
                        <div style="font-size:13px;color:#334;line-height:1.5">{{ $fine->description }}</div>
                    </div>

                    <div class="summary-total">
                        <span>Nominal Denda</span>
                        <span style="color:#dc2626">{{ $fine->formatted_amount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectBank(el, name) {
    document.querySelectorAll('.bank-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('payment_method_select').value = name;
}

function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Disalin!';
        setTimeout(() => btn.textContent = orig, 2000);
    });
}

const proofInput = document.getElementById('proof-input');
if (proofInput) {
    proofInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const preview = document.getElementById('proof-preview');
        const zone = document.getElementById('upload-zone');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        zone.classList.add('has-file');
        zone.querySelector('strong').textContent = file.name;
    });
}
</script>
@endpush
