<!-- Modal Detail Denda & Pembayaran -->
<div id="fineDetailModal" class="custom-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div class="modal-content-card" style="background:#fff;border-radius:14px;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;padding:26px;box-shadow:0 10px 40px rgba(0,0,0,.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #e8e8f0;padding-bottom:14px;margin-bottom:18px">
            <div>
                <h3 style="font-size:18px;font-weight:800;color:#0d0d0d;margin-bottom:2px" id="fdm-title">Detail Denda &amp; Pembayaran</h3>
                <span style="font-size:12px;color:#9a9aaa" id="fdm-subtitle">Informasi lengkap kewajiban denda dan transaksi</span>
            </div>
            <button type="button" onclick="closeFineDetailModal()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#9a9aaa;line-height:1">&times;</button>
        </div>

        <!-- Bagian 1: Detail Denda -->
        <div style="margin-bottom:20px;background:#f8f9fc;border:1px solid #eef0f5;border-radius:10px;padding:16px">
            <h4 style="font-size:13px;font-weight:700;color:#1847ED;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">
                📌 Rincian Denda
            </h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px">
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">ID Denda:</span>
                    <strong id="fdm-fine-id" style="color:#0d0d0d">-</strong>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Kode Booking:</span>
                    <strong id="fdm-booking-code" style="color:#0d0d0d">-</strong>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Nama Pelanggan:</span>
                    <strong id="fdm-customer-name" style="color:#0d0d0d">-</strong>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Kendaraan Sewa:</span>
                    <strong id="fdm-motor" style="color:#0d0d0d">-</strong>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Jenis Denda:</span>
                    <span id="fdm-type" class="badge badge-pending">-</span>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Status Denda:</span>
                    <span id="fdm-fine-status" class="badge badge-unpaid">-</span>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Tanggal Denda Dibuat:</span>
                    <strong id="fdm-created-at" style="color:#0d0d0d">-</strong>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Nominal Denda:</span>
                    <strong id="fdm-amount" style="color:#dc2626;font-size:15px">-</strong>
                </div>
            </div>
            <div style="margin-top:12px;padding-top:10px;border-top:1px dashed #e2e4e9">
                <span style="color:#8a8a9a;font-size:11px;display:block;margin-bottom:3px">Deskripsi Denda:</span>
                <p id="fdm-description" style="font-size:13px;color:#334;line-height:1.5;margin:0">-</p>
            </div>
        </div>

        <!-- Bagian 2: Detail Pembayaran -->
        <div style="margin-bottom:20px;background:#fff;border:1px solid #e8e8f0;border-radius:10px;padding:16px">
            <h4 style="font-size:13px;font-weight:700;color:#1847ED;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">
                💳 Rincian Pembayaran
            </h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px">
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Metode Pembayaran:</span>
                    <strong id="fdm-payment-method" style="color:#0d0d0d">-</strong>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Nominal Dibayar:</span>
                    <strong id="fdm-paid-amount" style="color:#0d0d0d">-</strong>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Waktu Pengajuan:</span>
                    <strong id="fdm-payment-submitted" style="color:#0d0d0d">-</strong>
                </div>
                <div>
                    <span style="color:#8a8a9a;font-size:11px;display:block">Waktu Terverifikasi:</span>
                    <strong id="fdm-payment-verified" style="color:#0d0d0d">-</strong>
                </div>
                <div style="grid-column:1 / -1">
                    <span style="color:#8a8a9a;font-size:11px;display:block;margin-bottom:4px">Status Verifikasi:</span>
                    <span id="fdm-payment-status" class="badge badge-pending">-</span>
                </div>
            </div>

            <!-- Alasan Penolakan Jika Ada -->
            <div id="fdm-rejection-box" style="display:none;margin-top:12px;padding:10px;border-radius:8px;background:#fee2e2;border:1px solid #fecaca;color:#991b1b;font-size:12px">
                <strong>Alasan Penolakan:</strong>
                <p id="fdm-rejection-reason" style="margin-top:4px;color:#b91c1c"></p>
            </div>

            <!-- Foto Bukti Pembayaran -->
            <div style="margin-top:14px;padding-top:12px;border-top:1px dashed #e2e4e9">
                <span style="color:#8a8a9a;font-size:12px;font-weight:600;display:block;margin-bottom:8px">Foto Bukti Transfer:</span>
                <div id="fdm-proof-container" style="text-align:center;background:#f8f9fa;border-radius:8px;padding:12px;border:1px solid #eef0f4">
                    <img id="fdm-proof-img" src="" alt="Bukti Transfer" style="max-width:100%;max-height:240px;border-radius:6px;display:inline-block;box-shadow:0 2px 8px rgba(0,0,0,.08)">
                    <div style="margin-top:8px">
                        <a id="fdm-proof-link" href="#" target="_blank" style="color:#1847ED;font-size:12px;font-weight:700">
                            🔍 Buka Foto Ukuran Penuh &nearr;
                        </a>
                    </div>
                </div>
                <div id="fdm-no-proof" style="display:none;padding:16px;text-align:center;color:#9a9aaa;font-size:13px;background:#f8f9fa;border-radius:8px">
                    Belum ada bukti pembayaran yang diunggah.
                </div>
            </div>
        </div>

        <!-- Bagian Tindakan (Actions) -->
        <div style="display:flex;justify-content:flex-end;gap:10px;align-items:center;border-top:1px solid #e8e8f0;padding-top:16px" id="fdm-actions-wrap">
            <button type="button" onclick="closeFineDetailModal()" style="padding:9px 18px;background:#f0f2f5;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;color:#4a4a5a">
                Tutup
            </button>

            @if(auth()->check() && auth()->user()->isAdmin())
                <!-- Form Verifikasi Admin -->
                <form id="fdm-verify-form" method="POST" action="" style="display:none">
                    @csrf
                    <button type="submit" style="padding:9px 18px;background:#16a34a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer" onclick="return confirm('Konfirmasi bahwa pembayaran denda ini telah diterima lunas?')">
                        &check; Konfirmasi Pembayaran Lunas
                    </button>
                </form>

                <!-- Tombol Tolak Admin -->
                <button type="button" id="fdm-reject-btn" style="display:none;padding:9px 18px;background:#fee2e2;color:#dc2626;border:1px solid #dc2626;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer" onclick="handleAdminRejectFromModal()">
                    &cross; Tolak Pembayaran
                </button>
            @else
                <!-- Tombol Customer Bayar / Unggah Ulang -->
                <a id="fdm-customer-pay-btn" href="#" style="display:none;padding:9px 18px;background:#1847ED;color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">
                    Bayar Denda / Unggah Bukti
                </a>
            @endif
        </div>
    </div>
</div>

<script>
function openFineDetailModal(data) {
    document.getElementById('fdm-fine-id').textContent = '#' + data.id;
    document.getElementById('fdm-booking-code').textContent = data.booking_code || '-';
    document.getElementById('fdm-customer-name').textContent = data.customer_name || '-';
    document.getElementById('fdm-motor').textContent = data.motor || '-';
    document.getElementById('fdm-created-at').textContent = data.created_at || '-';
    document.getElementById('fdm-amount').textContent = data.amount || '-';
    document.getElementById('fdm-description').textContent = data.description || '-';

    // Type badge
    var typeBadge = document.getElementById('fdm-type');
    typeBadge.textContent = data.type_label || data.type || '-';
    typeBadge.className = 'badge badge-' + (data.type === 'late' ? 'pending' : (data.type === 'damage' ? 'rejected' : 'cancelled'));

    // Status denda badge
    var fineStatusBadge = document.getElementById('fdm-fine-status');
    fineStatusBadge.textContent = data.fine_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS';
    fineStatusBadge.className = 'badge badge-' + (data.fine_status === 'paid' ? 'completed' : 'unpaid');

    // Pembayaran
    document.getElementById('fdm-payment-method').textContent = data.payment_method || 'Belum dipilih';
    document.getElementById('fdm-paid-amount').textContent = data.payment_amount || data.amount || '-';
    document.getElementById('fdm-payment-submitted').textContent = data.payment_submitted_at || '-';
    document.getElementById('fdm-payment-verified').textContent = data.payment_verified_at || '-';

    var payStatusBadge = document.getElementById('fdm-payment-status');
    if (data.fine_status === 'paid') {
        payStatusBadge.textContent = 'LUNAS / TERVERIFIKASI';
        payStatusBadge.className = 'badge badge-completed';
    } else if (data.is_awaiting_verification) {
        payStatusBadge.textContent = 'MENUNGGU VERIFIKASI ADMIN';
        payStatusBadge.className = 'badge badge-pending';
    } else if (data.is_rejected) {
        payStatusBadge.textContent = 'BUKTI DITOLAK';
        payStatusBadge.className = 'badge badge-rejected';
    } else {
        payStatusBadge.textContent = 'BELUM ADA PEMBAYARAN';
        payStatusBadge.className = 'badge badge-cancelled';
    }

    // Rejection reason
    var rejBox = document.getElementById('fdm-rejection-box');
    if (data.rejection_reason) {
        document.getElementById('fdm-rejection-reason').textContent = data.rejection_reason;
        rejBox.style.display = 'block';
    } else {
        rejBox.style.display = 'none';
    }

    // Proof photo
    var proofContainer = document.getElementById('fdm-proof-container');
    var noProofBox = document.getElementById('fdm-no-proof');
    var proofImg = document.getElementById('fdm-proof-img');
    var proofLink = document.getElementById('fdm-proof-link');

    if (data.proof_url) {
        proofImg.src = data.proof_url;
        proofLink.href = data.proof_url;
        proofContainer.style.display = 'block';
        noProofBox.style.display = 'none';
    } else {
        proofContainer.style.display = 'none';
        noProofBox.style.display = 'block';
    }

    // Admin verify & reject actions
    var verifyForm = document.getElementById('fdm-verify-form');
    var rejectBtn = document.getElementById('fdm-reject-btn');
    if (verifyForm && rejectBtn) {
        if (data.is_awaiting_verification) {
            verifyForm.action = '/admin/fines/' + data.id + '/verify';
            verifyForm.style.display = 'inline-block';
            rejectBtn.style.display = 'inline-block';
            rejectBtn.setAttribute('data-fine-id', data.id);
        } else {
            verifyForm.style.display = 'none';
            rejectBtn.style.display = 'none';
        }
    }

    // Customer pay action
    var customerPayBtn = document.getElementById('fdm-customer-pay-btn');
    if (customerPayBtn) {
        if (data.fine_status !== 'paid') {
            customerPayBtn.href = '/fines/' + data.id + '/payment';
            customerPayBtn.style.display = 'inline-block';
            customerPayBtn.textContent = data.is_rejected ? 'Unggah Ulang Bukti Transfer' : (data.is_awaiting_verification ? 'Lihat Halaman Pembayaran' : 'Bayar Denda Sekarang');
        } else {
            customerPayBtn.style.display = 'none';
        }
    }

    var modal = document.getElementById('fineDetailModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeFineDetailModal() {
    var modal = document.getElementById('fineDetailModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function handleAdminRejectFromModal() {
    var rejectBtn = document.getElementById('fdm-reject-btn');
    var fineId = rejectBtn.getAttribute('data-fine-id');
    closeFineDetailModal();
    if (typeof openRejectModal === 'function') {
        openRejectModal(fineId);
    }
}
</script>
