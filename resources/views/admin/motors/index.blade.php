@extends('layouts.admin')

@section('title', 'Kelola Motor')
@section('page-title', 'Kelola Motor')

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
.btn-sm { padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; }
.btn-edit { background:#f0f2ff; color:#4758D6; }
.btn-edit:hover { background:#4758D6; color:#fff; }
.btn-delete { background:#fef2f2; color:#e74c3c; }
.btn-delete:hover { background:#e74c3c; color:#fff; }
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
    <a href="{{ route('admin.motors.create') }}" class="btn-primary">+ Tambah Motor</a>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Motor</th>
                <th>Kategori</th>
                <th>Plat</th>
                <th>Harga/hari</th>
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
                            <a href="{{ route('admin.motors.edit', $motor) }}" class="btn-sm btn-edit">Edit</a>
                            <form method="POST" action="{{ route('admin.motors.destroy', $motor) }}" onsubmit="return confirm('Hapus motor ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-delete">Hapus</button>
                            </form>
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
@endsection
