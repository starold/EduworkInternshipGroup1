@extends('layouts.admin')

@section('title', 'Tambah Motor')
@section('page-title', 'Tambah Motor')

@push('styles')
<style>
.form-card { background:#fff; border:1px solid #e2e4e9; border-radius:12px; padding:32px; max-width:720px; }
.form-group { margin-bottom:22px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#4a4a5a; margin-bottom:8px; }
.form-label span { color:#e74c3c; }
.form-control { width:100%; padding:11px 14px; border:1.5px solid #e2e4e9; border-radius:8px; font-size:14px; font-family:'Inter',sans-serif; color:#1a1a2e; outline:none; transition:all .2s; }
.form-control:focus { border-color:#4758D6; box-shadow:0 0 0 3px rgba(71,88,214,.1); }
.form-control.is-invalid { border-color:#e74c3c; }
.invalid-feedback { color:#e74c3c; font-size:12px; margin-top:6px; display:block; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
.form-actions { display:flex; gap:12px; margin-top:8px; }
.btn-submit { padding:12px 28px; background:#4758D6; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; }
.btn-submit:hover { background:#3847B5; }
.btn-back { padding:12px 22px; background:#f8f9fb; color:#4a4a5a; border:1.5px solid #e2e4e9; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none; }
.btn-back:hover { border-color:#4758D6; color:#4758D6; }
.img-preview { max-width:200px; border-radius:10px; margin-top:12px; display:none; }
</style>
@endpush

@section('content')
<div class="form-card">
    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom:24px">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.motors.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Merek <span>*</span></label>
                <input type="text" name="merk" class="form-control @error('merk') is-invalid @enderror" value="{{ old('merk') }}" placeholder="Honda, Yamaha...">
                @error('merk') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Model <span>*</span></label>
                <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}" placeholder="Beat, NMAX, CBR...">
                @error('model') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Warna <span>*</span></label>
                <input type="text" name="warna" class="form-control @error('warna') is-invalid @enderror" value="{{ old('warna') }}" placeholder="Hitam, Putih, Merah...">
                @error('warna') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Tahun <span>*</span></label>
                <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}">
                @error('year') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Kategori <span>*</span></label>
                <select name="motor_category_id" class="form-control @error('motor_category_id') is-invalid @enderror">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('motor_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('motor_category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Plat <span>*</span></label>
                <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number') }}" placeholder="AB 1234 CD">
                @error('plate_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Harga/hari (Rp) <span>*</span></label>
                <input type="number" name="price_per_day" class="form-control @error('price_per_day') is-invalid @enderror" value="{{ old('price_per_day') }}" min="0" placeholder="75000">
                @error('price_per_day') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deposit (Rp) <span>*</span></label>
                <input type="number" name="deposit" class="form-control @error('deposit') is-invalid @enderror" value="{{ old('deposit') }}" min="0" placeholder="300000">
                @error('deposit') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror">
                <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Perawatan</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi motor...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Foto Motor</label>
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="img-input">
            @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
            <img id="img-preview" class="img-preview">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Simpan Motor</button>
            <a href="{{ route('admin.motors.index') }}" class="btn-back">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('img-input').addEventListener('change', function(e) {
    const preview = document.getElementById('img-preview');
    const file = e.target.files[0];
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
});
</script>
@endpush
