@extends('layouts.app')

@section('title', 'Pembayaran Gagal')

@push('styles')
<style>
.expired-page-wrap {
    min-height: 65vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
}
.expired-card {
    text-align: center;
    max-width: 520px;
    margin: 0 auto;
}
.expired-illustration {
    width: 140px;
    height: 140px;
    margin: 0 auto 28px;
    background: #fee2e2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    box-shadow: 0 10px 30px rgba(220, 38, 38, 0.15);
}
.expired-title {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 14px;
}
.expired-desc {
    font-size: 14px;
    color: var(--text-muted);
    line-height: 1.7;
    margin-bottom: 32px;
}
.btn-retry-pay {
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
.btn-retry-pay:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(24, 71, 237, 0.3);
}
.btn-chat-help {
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
.btn-chat-help:hover { border-color: var(--primary); color: var(--primary); }
</style>
@endpush

@section('content')
<div class="container expired-page-wrap">
    <div class="expired-card">
        <div class="expired-illustration">
            ⏰
        </div>
        <h1 class="expired-title">Ups, Waktunya Habis!</h1>
        <p class="expired-desc">
            Masa pembayaran program mu sudah lewat nih. Kamu masih bisa gabung kok, daftar ulang sekarang atau hubungi admin untuk bantuan.
        </p>
        <div>
            <a href="{{ route('vehicles.index') }}" class="btn-retry-pay">
                Ulangi Pembayaran
            </a>
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20terkait%20booking%20{{ $booking->booking_code }}" target="_blank" class="btn-chat-help">
                Hubungi Admin
            </a>
        </div>
    </div>
</div>
@endsection

