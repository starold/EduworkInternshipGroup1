@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@push('styles')
<style>
.success-page-wrap {
    min-height: 65vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
}
.success-card {
    text-align: center;
    max-width: 520px;
    margin: 0 auto;
}
.success-illustration {
    width: 140px;
    height: 140px;
    margin: 0 auto 28px;
    background: #dcfce7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    box-shadow: 0 10px 30px rgba(22, 163, 74, 0.15);
}
.success-title {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 14px;
}
.success-desc {
    font-size: 14px;
    color: var(--text-muted);
    line-height: 1.7;
    margin-bottom: 32px;
}
.btn-chat-admin {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 13px 36px;
    background: var(--primary);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    border-radius: var(--radius);
    text-decoration: none;
    transition: var(--transition);
}
.btn-chat-admin:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(24, 71, 237, 0.3);
}
.btn-order-list {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 12px;
    padding: 13px 28px;
    background: #fff;
    color: var(--text-body);
    border: 1.5px solid var(--border);
    font-size: 14px;
    font-weight: 700;
    border-radius: var(--radius);
    text-decoration: none;
    transition: var(--transition);
}
.btn-order-list:hover { border-color: var(--primary); color: var(--primary); }
</style>
@endpush

@section('content')
<div class="container success-page-wrap">
    <div class="success-card">
        <div class="success-illustration">
            👍
        </div>
        <h1 class="success-title">Yeay! Pembayaran Berhasil</h1>
        <p class="success-desc">
            Selamat, kamu berhasil booking motornya! Cek status transaksimu dan chat admin untuk konfirmasi pengambilan motor.
        </p>
        <div>
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20sudah%20melakukan%20pembayaran%20untuk%20booking%20{{ $booking->booking_code }}" target="_blank" class="btn-chat-admin">
                Chat Admin
            </a>
            <a href="{{ route('customer.bookings.index') }}" class="btn-order-list">
                Daftar Pesanan
            </a>
        </div>
    </div>
</div>
@endsection

