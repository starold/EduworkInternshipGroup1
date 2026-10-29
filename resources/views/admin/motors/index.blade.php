@extends('layouts.admin')

@section('title', 'Kelola Kendaraan')
@section('page-title', 'Daftar Kendaraan')

@push('styles')
<style>
.page-actions { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.filter-form { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
.filter-input { padding:9px 14px; border:1.5px solid #e2e4e9; border-radius:8px; font-size:14px; font-family:'Inter',sans-serif; outline:none; }
.filter-input:focus { border-color:#4758D6; }
.btn-primary { padding:10px 22px; background:#4758D6; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
.btn-primary:hover { background:#3847B5; }
.table-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; overflow:hidden; }
table { width:100%; border-collapse:collapse; }
th { background:#f8f9fb; padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#8a8a9a; text-transform:uppercase; letter-spacing:.5px; }
td { padding:14px 16px; font-size:14px; color:#4a4a5a; border-top:1px solid #f0f0f5; vertical-align:middle; }
tr:hover td { background:#fafbfc; }
.motor-thumb { width:72px; height:52px; object-fit:cover; border-radius:8px; }
.badge { display:inline-flex; padding:4px 10px; border-radius:50px; font-size:11px; font-weight:700; }
.badge-available { background:#d4edda; color:#155724; }
.badge-rented { background:#fff3cd; color:#856404; }
.badge-maintenance { background:#cce5ff; color:#004085; }
.badge-inactive { background:#e2e3e5; color:#383d41; }
.actions { display:flex; gap:8px; }
.btn-sm { padding:6px 14px; border-radius:6px; font-size:13px; font-weight:600; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; }
.btn-detail-admin { background:#4758D6; color:#fff; }
.btn-detail-admin:hover { background:#3847B5; }
.btn-delete { background:#fef2f2; color:#e74c3c; border:1px solid #fecaca; }
.btn-delete:hover { background:#e74c3c; color:#fff; }

/* Modal Detail Kendaraan (sesuai mockup) */
.motor-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 3500;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow-y: auto;
}
.motor-modal-overlay.open { display: flex; }
.motor-modal-box {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 680px;
    box-shadow: 0 10px 30px rgba(0,0,0,.15);
    overflow: hidden;
    margin: auto;
}
.motor-modal-header {
    padding: 20px 24px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f0f0f5;
}
.motor-modal-title { font-size: 17px; font-weight: 800; color: #1a1a2e; }
.motor-modal-body { padding: 24px; max-height: 80vh; overflow-y: auto; }

.m-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px; }
.m-form-group { display: flex; flex-direction: column; }
.m-form-group.full { grid-column: span 2; }
.m-label { font-size: 12px; font-weight: 600; color: #8a8a9a; margin-bottom: 6px; }
.m-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e2e4e9;
    border-radius: 8px;
    font-size: 14px;
    color: #1a1a2e;
    outline: none;
}
.m-input:focus { border-color: #4758D6; }
.m-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e2e4e9;
    border-radius: 8px;
    font-size: 13px;
    color: #1a1a2e;
    outline: none;
    resize: vertical;
    font-family: inherit;
}
.m-textarea:focus { border-color: #4758D6; }

.motor-upload-box {
    width: 100%;
    height: 120px;
    border: 2px dashed #e2e4e9;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #f8f9fb;
    overflow: hidden;
    position: relative;
}
.motor-upload-box:hover { border-color: #4758D6; }
.motor-upload-preview {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 6px;
    display: none;
}

.modal-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid #f0f0f5;
}
.btn-modal-delete {
    padding: 11px 24px;
    background: #fff;
    color: #e74c3c;
    border: 1.5px solid #e74c3c;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
}
.btn-modal-delete:hover { background: #e74c3c; color: #fff; }
.btn-modal-save {
    padding: 11px 32px;
    background: #4758D6;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
}
.btn-modal-save:hover { background: #3847B5; }
</style>
@endpush

@section('content')
<div class="page-actions">
    <form method="GET" class="filter-form">
        <input type="text" name="search" class="filter-input" placeholder="Cari motor..." value="{{ request('search') }}">
        <select name="status" class="filter-input">
            <option value="">Semua Status</option>
            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="rented" {{ request('status') === 'rented' ? 'selected' : '' }}>Disewa</option>
            <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Perawatan</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
        <select name="category" class="filter-input">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary" style="background:#f8f9fb;color:#4a4a5a;border:1.5px solid #e2e4e9">Filter</button>
    </form>
    <button type="button" class="btn-primary" onclick="openCreateMotorModal()">+ Tambah Motor</button>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama Kendaraan</th>
                <th>Kategori</th>
                <th>No. Plat</th>
                <th>Harga / Hari</th>
                <th>Deposit</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($motors as $motor)
                <tr>
                    <td>
                        <img src="{{ $motor->image_url }}" alt="{{ $motor->model }}" class="motor-thumb">
                    </td>
                    <td>
                        <strong>{{ $motor->merk }} {{ $motor->model }}</strong><br>
                        <small style="color:#8a8a9a">{{ $motor->year }} · {{ $motor->warna }}</small>
                    </td>
                    <td>{{ $motor->category->name }}</td>
                    <td><strong>{{ $motor->plate_number }}</strong></td>
                    <td>Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($motor->deposit, 0, ',', '.') }}</td>
                    <td><span class="badge badge-{{ $motor->status->value }}">{{ $motor->status->label() }}</span></td>
                    <td>
                        <div class="actions">
                            <button type="button" class="btn-sm btn-detail-admin" onclick="openEditMotorModal({
                                updateUrl: '{{ route('admin.motors.update', $motor) }}',
                                deleteUrl: '{{ route('admin.motors.destroy', $motor) }}',
                                imgUrl: '{{ $motor->image_url }}',
                                merk: '{{ $motor->merk }}',
                                model: '{{ $motor->model }}',
                                categoryId: '{{ $motor->motor_category_id }}',
                                plateNumber: '{{ $motor->plate_number }}',
                                pricePerDay: '{{ $motor->price_per_day }}',
                                deposit: '{{ $motor->deposit }}',
                                capacity: '{{ $motor->warna }}',
                                year: '{{ $motor->year }}',
                                status: '{{ $motor->status->value }}',
                                description: '{{ addslashes($motor->description ?? '') }}'
                            })">Detail</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:#8a8a9a">Tidak ada motor</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">{{ $motors->withQueryString()->links() }}</div>

<!-- Modal Detail Kendaraan (Admin - sesuai mockup) -->
<div class="motor-modal-overlay" id="motorDetailModal" onclick="if(event.target===this)closeMotorModal()">
    <div class="motor-modal-box">
        <div class="motor-modal-header">
            <span class="motor-modal-title" id="motorModalTitle">Detail Kendaraan</span>
            <button type="button" onclick="closeMotorModal()" style="background:none;border:none;font-size:22px;color:#8a8a9a;cursor:pointer">×</button>
        </div>
        <div class="motor-modal-body">
            <form id="motorDetailForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div id="methodSpoof"></div>

                <!-- Top: Image upload & Deskripsi -->
                <div class="m-form-row" style="margin-bottom:20px">
                    <div class="m-form-group">
                        <label class="m-label">Gambar</label>
                        <div class="motor-upload-box" onclick="document.getElementById('motorImgInput').click()">
                            <img id="motorImgPreview" class="motor-upload-preview" alt="Preview">
                            <div id="motorUploadPlaceholder" style="text-align:center;padding:12px;color:#8a8a9a">
                                <div style="font-size:28px;margin-bottom:4px">🖼</div>
                                <span style="font-size:12px;color:#4758D6;font-weight:600">Pilih foto motor</span>
                            </div>
                        </div>
                        <input type="file" name="image" id="motorImgInput" accept="image/*" style="display:none" onchange="previewMotorImage(this)">
                    </div>
                    <div class="m-form-group">
                        <label class="m-label">Deskripsi</label>
                        <textarea name="description" id="mMotorDesc" class="m-textarea" rows="4" placeholder="Deskripsi motor..."></textarea>
                    </div>
                </div>

                <!-- Fields Grid -->
                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-label">Nama Kendaraan (Model) *</label>
                        <input type="text" name="model" id="mMotorModel" class="m-input" placeholder="Vega Force, Beat..." required>
                    </div>
                    <div class="m-form-group">
                        <label class="m-label">Tipe (Kategori) *</label>
                        <select name="motor_category_id" id="mMotorCategory" class="m-input" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-label">Brand *</label>
                        <input type="text" name="merk" id="mMotorMerk" class="m-input" placeholder="Yamaha, Honda..." required>
                    </div>
                    <div class="m-form-group">
                        <label class="m-label">No. Plat *</label>
                        <input type="text" name="plate_number" id="mMotorPlate" class="m-input" placeholder="AB 3456 GH" required>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-label">Harga per hari (Rp) *</label>
                        <input type="number" name="price_per_day" id="mMotorPrice" class="m-input" placeholder="75000" min="0" required>
                    </div>
                    <div class="m-form-group">
                        <label class="m-label">Kapasitas Tangki / Warna</label>
                        <input type="text" name="warna" id="mMotorCapacity" class="m-input" placeholder="4.2 L / Hitam">
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-label">Deposit (Rp) *</label>
                        <input type="number" name="deposit" id="mMotorDeposit" class="m-input" placeholder="300000" min="0" required>
                    </div>
                    <div class="m-form-group">
                        <label class="m-label">Status *</label>
                        <select name="status" id="mMotorStatus" class="m-input" required>
                            <option value="available">Tersedia (Available)</option>
                            <option value="rented">Disewa (Rented)</option>
                            <option value="maintenance">Perawatan (Maintenance)</option>
                            <option value="inactive">Tidak Aktif (Inactive)</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="year" id="mMotorYear" value="{{ date('Y') }}">

                <!-- Buttons -->
                <div class="modal-actions">
                    <button type="button" id="btnDeleteMotorModal" class="btn-modal-delete" style="display:none" onclick="submitDeleteMotor()">Hapus</button>
                    <button type="submit" class="btn-modal-save">Simpan</button>
                </div>
            </form>

            <form id="deleteMotorHiddenForm" method="POST" action="" style="display:none">
                @csrf @method('DELETE')
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentDeleteUrl = '';

function openCreateMotorModal() {
    document.getElementById('motorModalTitle').textContent = 'Tambah Kendaraan Baru';
    document.getElementById('motorDetailForm').action = "{{ route('admin.motors.store') }}";
    document.getElementById('methodSpoof').innerHTML = '';
    
    // Reset inputs
    document.getElementById('mMotorModel').value = '';
    document.getElementById('mMotorMerk').value = '';
    document.getElementById('mMotorPlate').value = '';
    document.getElementById('mMotorPrice').value = '';
    document.getElementById('mMotorCapacity').value = '';
    document.getElementById('mMotorDeposit').value = '300000';
    document.getElementById('mMotorDesc').value = '';
    document.getElementById('mMotorStatus').value = 'available';

    // Reset preview
    document.getElementById('motorImgPreview').style.display = 'none';
    document.getElementById('motorUploadPlaceholder').style.display = 'block';

    document.getElementById('btnDeleteMotorModal').style.display = 'none';
    document.getElementById('motorDetailModal').classList.add('open');
}

function openEditMotorModal(data) {
    document.getElementById('motorModalTitle').textContent = 'Detail Kendaraan';
    document.getElementById('motorDetailForm').action = data.updateUrl;
    document.getElementById('methodSpoof').innerHTML = '@method("PUT")';

    document.getElementById('mMotorModel').value = data.model;
    document.getElementById('mMotorMerk').value = data.merk;
    document.getElementById('mMotorCategory').value = data.categoryId;
    document.getElementById('mMotorPlate').value = data.plateNumber;
    document.getElementById('mMotorPrice').value = data.pricePerDay;
    document.getElementById('mMotorDeposit').value = data.deposit;
    document.getElementById('mMotorCapacity').value = data.capacity;
    document.getElementById('mMotorYear').value = data.year;
    document.getElementById('mMotorStatus').value = data.status;
    document.getElementById('mMotorDesc').value = data.description;

    if (data.imgUrl) {
        document.getElementById('motorImgPreview').src = data.imgUrl;
        document.getElementById('motorImgPreview').style.display = 'block';
        document.getElementById('motorUploadPlaceholder').style.display = 'none';
    }

    currentDeleteUrl = data.deleteUrl;
    document.getElementById('btnDeleteMotorModal').style.display = 'inline-block';

    document.getElementById('motorDetailModal').classList.add('open');
}

function closeMotorModal() {
    document.getElementById('motorDetailModal').classList.remove('open');
}

function previewMotorImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('motorImgPreview').src = e.target.result;
            document.getElementById('motorImgPreview').style.display = 'block';
            document.getElementById('motorUploadPlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function submitDeleteMotor() {
    if (confirm('Apakah Anda yakin ingin menghapus kendaraan ini?')) {
        const f = document.getElementById('deleteMotorHiddenForm');
        f.action = currentDeleteUrl;
        f.submit();
    }
}
</script>
@endpush
