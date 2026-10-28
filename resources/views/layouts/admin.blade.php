<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - ED.RENT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3662ed;
            --primary-dark: #244ed1;
            --text-dark: #172033;
            --text-body: #48566e;
            --text-muted: #8290a8;
            --border: #e7ebf2;
            --bg-page: #f5f7fa;
            --white: #fff;
            --success: #42bd57;
            --danger: #f06455;
            --warning: #efa928;
            --radius: 10px;
            --shadow: 0 3px 14px rgba(28, 46, 79, .07);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; color:var(--text-body); background:var(--bg-page); min-height:100vh; display:flex; flex-direction:column; -webkit-font-smoothing:antialiased; }
        a { text-decoration:none; color:inherit; }
        button, input, select, textarea { font:inherit; max-width:100%; min-width:0; }
        textarea { resize:vertical; }

        .site-header { height:58px; background:var(--white); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:24px; padding:0 28px; position:relative; z-index:20; }
        .header-start, .header-links, .header-actions { display:flex; align-items:center; }
        .header-start { gap:14px; }
        .brand { color:var(--primary); font-size:21px; font-weight:800; letter-spacing:-1px; white-space:nowrap; }
        .brand span { color:#172033; }
        .menu-toggle { display:none; border:0; background:transparent; color:var(--text-body); font-size:22px; cursor:pointer; padding:4px; }
        .header-links { justify-content:center; gap:36px; margin-left:auto; margin-right:28px; }
        .header-links a { color:#34425d; font-size:12px; font-weight:500; }
        .header-links a:hover { color:var(--primary); }
        .header-actions { gap:12px; }
        .help-link { display:inline-flex; align-items:center; gap:7px; padding:9px 12px; border:1px solid var(--border); border-radius:5px; background:#fff; font-size:11px; color:#34425d; }
        .help-link span { color:#1eaf60; font-size:15px; }
        .admin-chip { display:flex; align-items:center; gap:8px; padding:6px 10px; background:#f6f8fc; border-radius:6px; color:#34425d; font-size:11px; font-weight:600; }
        .admin-avatar { width:25px; height:25px; display:grid; place-items:center; border-radius:50%; color:var(--primary); background:#e9efff; font-size:12px; font-weight:700; }

        .admin-shell { display:flex; flex:1; min-height:calc(100vh - 58px); }
        .sidebar { width:200px; flex:0 0 200px; background:var(--white); display:flex; flex-direction:column; border-right:1px solid #eef0f4; }
        .sidebar-nav { flex:1; padding:22px 9px; }
        .nav-section-label { padding:0 9px 12px; color:#b0bbcd; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1px; }
        .nav-item { display:flex; align-items:center; gap:10px; min-height:39px; padding:9px 11px; margin-bottom:4px; border-radius:6px; color:#8190aa; font-size:12px; font-weight:500; transition:background .15s, color .15s; }
        .nav-item:hover { color:var(--primary); background:#f3f6ff; }
        .nav-item.active { color:#fff; background:var(--primary); box-shadow:0 4px 10px rgba(54,98,237,.18); }
        .nav-icon { width:16px; text-align:center; font-size:14px; }
        .sidebar-logout { padding:14px 18px 22px; }
        .sidebar-logout form { margin:0; }
        .btn-logout { display:flex; align-items:center; gap:9px; width:100%; padding:8px 4px; color:#8190aa; background:transparent; border:0; font-size:12px; text-align:left; cursor:pointer; }
        .btn-logout:hover { color:var(--danger); }

        .main { flex:1; min-width:0; display:flex; flex-direction:column; }
        .content { width:100%; max-width:1440px; margin:0 auto; padding:24px 28px 36px; flex:1; }
        .page-heading { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:18px; }
        .page-heading h1 { color:var(--text-dark); font-size:20px; font-weight:700; letter-spacing:-.4px; }
        .page-heading p { margin-top:4px; color:var(--text-muted); font-size:12px; }
        .admin-footer { background:#fff; padding:28px 32px 0; border-top:1px solid var(--border); }
        .footer-main { display:grid; grid-template-columns:minmax(230px,2fr) repeat(3,minmax(100px,1fr)); gap:28px; padding-bottom:22px; }
        .footer-brand { color:var(--primary); font-size:19px; font-weight:800; letter-spacing:-.7px; }
        .footer-description { margin-top:8px; max-width:260px; color:#79869a; font-size:11px; line-height:1.6; }
        .footer-column h3 { margin-bottom:12px; color:var(--text-dark); font-size:12px; font-weight:700; }
        .footer-column a { display:block; margin-bottom:9px; color:#79869a; font-size:11px; }
        .footer-column a:hover { color:var(--primary); }
        .footer-bottom { display:flex; justify-content:space-between; gap:16px; padding:13px 0 15px; border-top:1px solid var(--border); color:#334056; font-size:10px; }
        .footer-legal { display:flex; gap:28px; }
        .alert { padding:12px 16px; border-radius:var(--radius); margin-bottom:18px; font-size:13px; font-weight:500; }
        .alert-success { background:#e5f7e9; color:#24783a; }
        .alert-danger { background:#fff0ee; color:#a83229; }

        @media(max-width:900px) {
            .site-header { padding:0 18px; }
            .header-links { gap:18px; margin-right:12px; }
            .content { padding:22px 18px 30px; }
        }
        @media(max-width:700px) {
            .site-header { height:56px; gap:12px; padding:0 14px; }
            .menu-toggle { display:block; }
            .header-links { display:none; }
            .help-link { display:none; }
            .admin-chip { font-size:0; padding:4px; }
            .admin-avatar { font-size:12px; }
            .admin-shell { min-height:calc(100vh - 56px); }
            .sidebar { position:fixed; z-index:30; top:56px; bottom:0; left:0; width:220px; transform:translateX(-100%); transition:transform .2s ease; box-shadow:8px 0 22px rgba(20,35,60,.08); }
            .sidebar.is-open { transform:translateX(0); }
            .content { padding:20px 14px 26px; }
            .footer-main { grid-template-columns:1fr 1fr; gap:22px; }
            .footer-about { grid-column:1 / -1; }
            .admin-footer { padding:22px 18px 0; }
        }
        @media(max-width:420px) {
            .footer-bottom { align-items:flex-start; flex-direction:column; }
            .footer-legal { gap:18px; }
        }
    </style>
    @stack('styles')
</head>
<body>
<header class="site-header">
    <div class="header-start">
        <button type="button" class="menu-toggle" id="admin-menu-toggle" aria-label="Buka menu" aria-expanded="false">☰</button>
        <a class="brand" href="{{ route('admin.dashboard') }}">ED<span>.</span>RENT</a>
    </div>
    <nav class="header-links" aria-label="Navigasi website">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('vehicles.index') }}">Pilih Kendaraan</a>
        <a href="{{ route('customer.bookings.index') }}">Daftar Pesanan</a>
    </nav>
    <div class="header-actions">
        <a class="help-link" href="https://wa.me/" target="_blank" rel="noopener noreferrer"><span>◉</span> Butuh bantuan?</a>
        <div class="admin-chip">
            <span class="admin-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span>{{ auth()->user()->name }} · {{ auth()->user()->role->label() }}</span>
        </div>
    </div>
</header>

<div class="admin-shell">
    <aside class="sidebar" id="admin-sidebar">
        <nav class="sidebar-nav">
            <div class="nav-section-label">Main Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">⌂</span> Dashboard
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="nav-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                <span class="nav-icon">▣</span> Daftar Pesanan
            </a>
            <a href="{{ route('admin.motors.index') }}" class="nav-item {{ request()->routeIs('admin.motors.*') ? 'active' : '' }}">
                <span class="nav-icon">▰</span> Kendaraan
            </a>
        </nav>
        <div class="sidebar-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout"><span class="nav-icon">↪</span> Log Out</button>
            </form>
        </div>
    </aside>

    <main class="main">
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>

        <footer class="admin-footer">
            <div class="footer-main">
                <div class="footer-about">
                    <a href="{{ route('admin.dashboard') }}" class="footer-brand">ED.RENT</a>
                    <p class="footer-description">Pilihan kendaraan, harga transparan, dan layanan 24 jam. Siap menemani setiap petualangan Anda.</p>
                </div>
                <div class="footer-column">
                    <h3>About</h3>
                    <a href="{{ route('home') }}">How it works</a>
                    <a href="{{ route('vehicles.index') }}">Featured</a>
                    <a href="{{ route('vehicles.index') }}">Partnership</a>
                    <a href="{{ route('home') }}">Business Relation</a>
                </div>
                <div class="footer-column">
                    <h3>Community</h3>
                    <a href="{{ route('home') }}">Events</a>
                    <a href="{{ route('home') }}">Blog</a>
                    <a href="{{ route('home') }}">Podcast</a>
                    <a href="{{ route('home') }}">Invite a friend</a>
                </div>
                <div class="footer-column">
                    <h3>Socials</h3>
                    <a href="https://discord.com">Discord</a>
                    <a href="https://instagram.com">Instagram</a>
                    <a href="https://twitter.com">Twitter</a>
                    <a href="https://facebook.com">Facebook</a>
                </div>
            </div>
            <div class="footer-bottom">
                <span>©{{ now()->year }} ED.RENT. All rights reserved</span>
                <div class="footer-legal">
                    <a href="{{ route('home') }}">Privacy &amp; Policy</a>
                    <a href="{{ route('home') }}">Terms &amp; Condition</a>
                </div>
            </div>
        </footer>
    </main>
</div>

<script>
const menuToggle = document.getElementById('admin-menu-toggle');
const adminSidebar = document.getElementById('admin-sidebar');

menuToggle?.addEventListener('click', () => {
    const isOpen = adminSidebar.classList.toggle('is-open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
});
</script>
@stack('scripts')
</body>
</html>
