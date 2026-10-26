<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ED.RENT') - Rental Motor Jogja</title>
    <meta name="description" content="@yield('description', 'ED.RENT - Rental motor terpercaya di Yogyakarta. Harga transparan, pelayanan 24 jam.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1847ED;
            --primary-dark: #1339c4;
            --primary-light: #e8eeff;
            --text-dark: #0d0d0d;
            --text-body: #4a4a5a;
            --text-muted: #9a9aaa;
            --bg-white: #ffffff;
            --bg-light: #f7f8fa;
            --border: #e8e8f0;
            --success: #16a34a;
            --success-bg: #dcfce7;
            --warning: #ca8a04;
            --warning-bg: #fef9c3;
            --danger: #dc2626;
            --danger-bg: #fee2e2;
            --info-bg: #dbeafe;
            --info: #1d4ed8;
            --radius: 8px;
            --radius-lg: 12px;
            --shadow: 0 1px 6px rgba(0,0,0,.08);
            --shadow-md: 0 4px 20px rgba(0,0,0,.1);
            --transition: all .2s cubic-bezier(.4,0,.2,1);
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { font-family:'Inter',sans-serif; color:var(--text-body); background:var(--bg-white); min-height:100vh; -webkit-font-smoothing:antialiased; }
        a { text-decoration:none; color:inherit; }
        img { max-width:100%; display:block; }
        ul { list-style:none; }
        input, select, button, textarea { font-family:'Inter',sans-serif; }

        .container { max-width:1160px; margin:0 auto; padding:0 24px; }

        /* ---- NAVBAR ---- */
        .navbar {
            background:#fff;
            border-bottom:1px solid var(--border);
            position:sticky; top:0; z-index:1000;
            height:60px;
        }
        .navbar .container { height:100%; display:flex; align-items:center; justify-content:space-between; }
        .nav-brand { font-size:20px; font-weight:800; color:var(--text-dark); letter-spacing:-.3px; }
        .nav-brand span { color:var(--primary); }

        .nav-links { display:flex; align-items:center; gap:32px; }
        .nav-links a { font-size:14px; font-weight:500; color:var(--text-body); }
        .nav-links a:hover, .nav-links a.active { color:var(--primary); }
        .nav-links a.active { font-weight:600; }

        .nav-actions { display:flex; align-items:center; gap:12px; }
        .btn-wa-help {
            display:flex; align-items:center; gap:6px;
            font-size:13px; font-weight:500; color:var(--text-body);
            padding:7px 12px; border-radius:var(--radius); border:1px solid var(--border);
        }
        .btn-wa-help:hover { border-color:var(--primary); color:var(--primary); }
        .btn-wa-help svg { width:16px; height:16px; fill:#16a34a; }
        .btn-login { padding:8px 20px; background:var(--primary); color:#fff; font-size:13px; font-weight:700; border-radius:var(--radius); border:none; cursor:pointer; }
        .btn-login:hover { background:var(--primary-dark); }

        /* Profile dropdown */
        .nav-profile { position:relative; }
        .profile-trigger {
            width:36px; height:36px; border-radius:50%; background:var(--primary);
            color:#fff; font-size:14px; font-weight:700; border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
        }
        .profile-dropdown {
            position:absolute; top:calc(100% + 10px); right:0;
            width:200px; background:#fff; border:1px solid var(--border);
            border-radius:var(--radius-lg); box-shadow:var(--shadow-md);
            padding:8px 0; display:none; z-index:100;
        }
        .profile-dropdown.open { display:block; }
        .profile-dropdown-header { padding:12px 16px 8px; border-bottom:1px solid var(--border); margin-bottom:6px; }
        .profile-dropdown-header .pname { font-size:14px; font-weight:700; color:var(--text-dark); }
        .profile-dropdown-header .pemail { font-size:12px; color:var(--text-muted); }
        .dropdown-item { display:flex; align-items:center; gap:10px; padding:10px 16px; font-size:14px; color:var(--text-body); cursor:pointer; }
        .dropdown-item:hover { background:var(--bg-light); color:var(--primary); }
        .dropdown-item .di-icon { font-size:16px; width:20px; text-align:center; }
        .dropdown-divider { border:none; border-top:1px solid var(--border); margin:6px 0; }

        /* Flash messages */
        .flash-container { padding:12px 0 0; }
        .alert { padding:12px 18px; border-radius:var(--radius); font-size:14px; font-weight:500; display:flex; align-items:center; gap:8px; }
        .alert-success { background:var(--success-bg); color:var(--success); }
        .alert-danger { background:var(--danger-bg); color:var(--danger); }

        /* Hamburger Mobile */
        .nav-hamburger { display:none; flex-direction:column; gap:5px; cursor:pointer; background:none; border:none; padding:4px; }
        .nav-hamburger span { display:block; width:22px; height:2px; background:var(--text-dark); border-radius:2px; transition:var(--transition); }

        /* Mobile Menu Overlay */
        .mobile-menu { position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:2000; display:none; }
        .mobile-menu.open { display:block; }
        .mobile-menu-panel { position:absolute; top:0; right:0; width:280px; height:100%; background:#fff; padding:24px; transform:translateX(100%); transition:transform .3s; }
        .mobile-menu.open .mobile-menu-panel { transform:translateX(0); }
        .mobile-menu-close { position:absolute; top:16px; right:16px; background:none; border:none; font-size:22px; cursor:pointer; color:var(--text-body); }
        .mobile-menu-links { margin-top:32px; display:flex; flex-direction:column; gap:4px; }
        .mobile-menu-links a { display:block; padding:12px 8px; font-size:15px; font-weight:500; color:var(--text-dark); border-radius:var(--radius); }
        .mobile-menu-links a:hover { background:var(--bg-light); }

        /* Profile Modal */
        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:3000; display:none; align-items:flex-start; justify-content:center; padding-top:120px; }
        .modal-overlay.open { display:flex; }
        .modal-box { background:#fff; border-radius:var(--radius-lg); width:100%; max-width:420px; box-shadow:var(--shadow-md); overflow:hidden; }
        .modal-header { padding:20px 24px 0; display:flex; align-items:center; justify-content:space-between; }
        .modal-title { font-size:18px; font-weight:700; color:var(--text-dark); }
        .modal-close { background:none; border:none; font-size:22px; cursor:pointer; color:var(--text-muted); line-height:1; }
        .modal-body { padding:20px 24px 24px; }
        .modal-avatar { width:64px; height:64px; border-radius:50%; background:var(--primary); color:#fff; font-size:22px; font-weight:700; display:flex; align-items:center; justify-content:center; margin-bottom:6px; }
        .modal-edit-btn { font-size:13px; color:var(--primary); font-weight:500; background:none; border:none; cursor:pointer; display:flex; align-items:center; gap:4px; margin-bottom:20px; }
        .mform-group { margin-bottom:14px; }
        .mform-label { display:block; font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
        .mform-control { width:100%; padding:10px 14px; border:1.5px solid var(--border); border-radius:var(--radius); font-size:14px; color:var(--text-dark); outline:none; }
        .mform-control:focus { border-color:var(--primary); }
        .btn-save { width:100%; padding:12px; background:var(--primary); color:#fff; border:none; border-radius:var(--radius); font-size:14px; font-weight:700; cursor:pointer; margin-top:8px; }
        .btn-save:hover { background:var(--primary-dark); }

        /* Footer */
        .footer { background:#fff; border-top:1px solid var(--border); padding:48px 0 0; margin-top:auto; }
        .footer-grid { display:grid; grid-template-columns:1.6fr 1fr 1fr 1fr; gap:48px; padding-bottom:40px; }
        .footer-brand h3 { font-size:20px; font-weight:800; color:var(--text-dark); margin-bottom:8px; }
        .footer-brand h3 span { color:var(--primary); }
        .footer-brand p { font-size:13px; color:var(--text-muted); line-height:1.7; max-width:220px; }
        .footer-col h4 { font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:14px; }
        .footer-col li { margin-bottom:8px; }
        .footer-col a { font-size:13px; color:var(--text-muted); }
        .footer-col a:hover { color:var(--primary); }
        .footer-bottom { border-top:1px solid var(--border); padding:16px 0; display:flex; justify-content:space-between; align-items:center; }
        .footer-bottom p { font-size:12px; color:var(--text-muted); }
        .footer-bottom-links { display:flex; gap:20px; }
        .footer-bottom-links a { font-size:12px; color:var(--text-muted); }

        @media(max-width:768px) {
            .nav-links, .nav-actions { display:none; }
            .nav-hamburger { display:flex; }
            .footer-grid { grid-template-columns:1fr 1fr; gap:28px; }
        }
        @media(max-width:480px) {
            .footer-grid { grid-template-columns:1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <a href="{{ route('home') }}" class="nav-brand">ED<span>.</span>RENT</a>

        <ul class="nav-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">Pilih Kendaraan</a></li>
            @auth
            <li><a href="{{ route('customer.bookings.index') }}" class="{{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}">Daftar Pesanan</a></li>
            @endauth
        </ul>

        <div class="nav-actions">
            <a href="https://wa.me/6281234567890" target="_blank" class="btn-wa-help">
                <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Butuh bantuan?
            </a>

            @auth
                <div class="nav-profile">
                    <button class="profile-trigger" id="profileBtn" onclick="toggleProfile()">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="profile-dropdown-header">
                            <div class="pname">{{ auth()->user()->name }}</div>
                            <div class="pemail">{{ auth()->user()->email }}</div>
                        </div>
                        <a class="dropdown-item" href="#" onclick="openProfileModal(); closeProfileDropdown()">
                            <span class="di-icon">👤</span> Profil saya
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}" @if(!auth()->user()->isAdmin() && !auth()->user()->isOwner()) style="display:none" @endif>
                            <span class="di-icon">📊</span> Dashboard
                        </a>
                        <a class="dropdown-item" href="{{ route('customer.bookings.index') }}">
                            <span class="di-icon">📋</span> Daftar Pesanan
                        </a>
                        <a class="dropdown-item" href="{{ route('home') }}">
                            <span class="di-icon">🏍</span> Kendaraan
                        </a>
                        <hr class="dropdown-divider">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item" style="width:100%;border:none;background:none;text-align:left;cursor:pointer;color:#dc2626">
                                <span class="di-icon">🚪</span> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-login">Masuk</a>
            @endauth
        </div>

        <!-- Hamburger -->
        <button class="nav-hamburger" onclick="openMobileMenu()">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu" onclick="closeMobileMenu()">
    <div class="mobile-menu-panel" onclick="event.stopPropagation()">
        <button class="mobile-menu-close" onclick="closeMobileMenu()">×</button>
        <div style="font-size:20px;font-weight:800;color:#0d0d0d">ED<span style="color:#1847ED">.</span>RENT</div>
        <div class="mobile-menu-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('home') }}">Pilih Kendaraan</a>
            @auth
            <a href="{{ route('customer.bookings.index') }}">Daftar Pesanan</a>
            <a href="#" onclick="openProfileModal(); closeMobileMenu()">Profil Saya</a>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:8px">
                @csrf
                <button type="submit" style="width:100%;padding:12px 8px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;text-align:left">
                    Keluar
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" style="background:#1847ED;color:#fff;padding:12px 16px;border-radius:8px;font-weight:700;text-align:center">Masuk</a>
            <a href="{{ route('register') }}">Daftar</a>
            @endauth
        </div>
    </div>
</div>

<!-- Profile Modal -->
@auth
<div class="modal-overlay" id="profileModal" onclick="if(event.target===this)closeProfileModal()">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Profil</span>
            <button class="modal-close" onclick="closeProfileModal()">×</button>
        </div>
        <div class="modal-body">
            <div style="display:flex;flex-direction:column;align-items:flex-start;margin-bottom:16px">
                <div class="modal-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <button class="modal-edit-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit profil
                </button>
            </div>
            <form method="POST" action="#" id="profileForm">
                @csrf @method('PUT')
                <div class="mform-group">
                    <label class="mform-label">Nama Lengkap</label>
                    <input type="text" class="mform-control" placeholder="Nama Lengkap" value="{{ auth()->user()->name }}">
                </div>
                <div class="mform-group">
                    <label class="mform-label">No WhatsApp</label>
                    <input type="text" class="mform-control" placeholder="08xxxxxxxxxx" value="{{ auth()->user()->phone }}">
                </div>
                <div class="mform-group">
                    <label class="mform-label">No. KTP</label>
                    <input type="text" class="mform-control" placeholder="1234567890">
                </div>
                <div class="mform-group">
                    <label class="mform-label">No. SIM</label>
                    <input type="text" class="mform-control" placeholder="1234567890">
                </div>
                <button type="submit" class="btn-save">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endauth

<!-- Flash Messages -->
@if(session('success') || session('error'))
<div class="container">
    <div class="flash-container">
        @if(session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">✗ {{ session('error') }}</div>
        @endif
    </div>
</div>
@endif

<!-- Main Content -->
<main>@yield('content')</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>ED<span>.</span>RENT</h3>
                <p>Pilihan terlengkap, harga transparan, dan layanan 24 jam. Siap menemani setiap petualangan Anda.</p>
            </div>
            <div class="footer-col">
                <h4>About</h4>
                <ul>
                    <li><a href="#">How it works</a></li>
                    <li><a href="#">Featured</a></li>
                    <li><a href="#">Partnership</a></li>
                    <li><a href="#">Business Relation</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Community</h4>
                <ul>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Podcast</a></li>
                    <li><a href="#">Invite a friend</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Socials</h4>
                <ul>
                    <li><a href="#">Discord</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Facebook</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>©{{ date('Y') }} ED.RENT. All rights reserved</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy & Policy</a>
                <a href="#">Terms & Condition</a>
            </div>
        </div>
    </div>
</footer>

<script>
function toggleProfile() {
    document.getElementById('profileDropdown').classList.toggle('open');
}
function closeProfileDropdown() {
    document.getElementById('profileDropdown').classList.remove('open');
}
function openProfileModal() {
    document.getElementById('profileModal').classList.add('open');
}
function closeProfileModal() {
    document.getElementById('profileModal').classList.remove('open');
}
function openMobileMenu() {
    document.getElementById('mobileMenu').classList.add('open');
}
function closeMobileMenu() {
    document.getElementById('mobileMenu').classList.remove('open');
}
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('profileDropdown');
    const btn = document.getElementById('profileBtn');
    if (dropdown && btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('open');
    }
});
</script>
@stack('scripts')
</body>
</html>
