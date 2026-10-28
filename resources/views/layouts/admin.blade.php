<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - ED.RENT</title>
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
            --info: #1d4ed8;
            --info-bg: #dbeafe;
            --radius: 8px;
            --radius-lg: 12px;
            --shadow: 0 1px 6px rgba(0,0,0,.08);
            --shadow-md: 0 4px 20px rgba(0,0,0,.1);
            --transition: all .2s cubic-bezier(.4,0,.2,1);
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { font-family:'Inter',sans-serif; color:var(--text-body); background:var(--bg-light); min-height:100vh; display:flex; flex-direction:column; -webkit-font-smoothing:antialiased; }
        a { text-decoration:none; color:inherit; }
        img { max-width:100%; display:block; }
        ul { list-style:none; }
        button, input, select, textarea { font:inherit; font-family:'Inter',sans-serif; max-width:100%; min-width:0; }
        textarea { resize:vertical; }

        .container { max-width:1160px; margin:0 auto; padding:0 24px; width:100%; }

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
        .nav-links a { font-size:14px; font-weight:500; color:var(--text-body); transition:var(--transition); }
        .nav-links a:hover, .nav-links a.active { color:var(--primary); font-weight:600; }

        .nav-actions { display:flex; align-items:center; gap:12px; }
        .btn-wa-help {
            display:flex; align-items:center; gap:6px;
            font-size:13px; font-weight:500; color:var(--text-body);
            padding:7px 12px; border-radius:var(--radius); border:1px solid var(--border);
            transition:var(--transition); background:#fff;
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
            box-shadow:0 2px 8px rgba(24,71,237,.25);
        }
        .profile-dropdown {
            position:absolute; top:calc(100% + 10px); right:0;
            width:220px; background:#fff; border:1px solid var(--border);
            border-radius:var(--radius-lg); box-shadow:var(--shadow-md);
            padding:8px 0; display:none; z-index:100;
        }
        .profile-dropdown.open { display:block; }
        .profile-dropdown-header { padding:12px 16px 8px; border-bottom:1px solid var(--border); margin-bottom:6px; }
        .profile-dropdown-header .pname { font-size:14px; font-weight:700; color:var(--text-dark); }
        .profile-dropdown-header .pemail { font-size:12px; color:var(--text-muted); }
        .dropdown-item { display:flex; align-items:center; gap:10px; padding:10px 16px; font-size:13px; font-weight:500; color:var(--text-body); cursor:pointer; transition:var(--transition); }
        .dropdown-item:hover { background:var(--bg-light); color:var(--primary); }
        .dropdown-item .di-icon { font-size:15px; width:20px; text-align:center; }
        .dropdown-divider { border:none; border-top:1px solid var(--border); margin:6px 0; }

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
        .mobile-menu-links a:hover { background:var(--bg-light); color:var(--primary); }

        /* Main Content */
        .main-content { flex:1; width:100%; }
        .page-heading { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:24px; }
        .page-heading h1 { color:var(--text-dark); font-size:24px; font-weight:800; letter-spacing:-.3px; }
        .page-heading p { margin-top:4px; color:var(--text-muted); font-size:14px; }

        /* Flash messages */
        .alert { padding:14px 18px; border-radius:var(--radius); font-size:14px; font-weight:500; display:flex; align-items:center; gap:8px; margin-bottom:20px; }
        .alert-success { background:var(--success-bg); color:var(--success); border:1px solid #bbf7d0; }
        .alert-danger { background:var(--danger-bg); color:var(--danger); border:1px solid #fecaca; }

        /* Unified Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
        }
        .badge::before {
            content: '●';
            font-size: 8px;
        }
        /* Menunggu / Pending -> Kuning */
        .badge-pending, .badge-warning, .badge-not_returned {
            background: #fef9c3 !important;
            color: #854d0e !important;
        }
        /* Sedang disewa -> Biru */
        .badge-confirmed, .badge-rented, .badge-info, .badge-blue {
            background: #dbeafe !important;
            color: #1e40af !important;
        }
        /* Sudah kembali / Selesai rental / Completed / Paid -> Hijau */
        .badge-returned, .badge-completed, .badge-paid, .badge-success {
            background: #dcfce7 !important;
            color: #166534 !important;
        }
        /* Ditolak / Cancelled / Failed / Unpaid -> Merah / Abu-abu */
        .badge-rejected, .badge-unpaid, .badge-failed, .badge-danger, .badge-damage {
            background: #fee2e2 !important;
            color: #991b1b !important;
        }
        .badge-cancelled, .badge-secondary, .badge-other, .badge-late {
            background: #f3f4f6 !important;
            color: #4b5563 !important;
        }
        .badge-late {
            background: #fef9c3 !important;
            color: #854d0e !important;
        }

        /* Table Scroll Containers */
        .table-scroll-wrapper {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            border-radius: var(--radius-lg);
            background: #fff;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }
        .table-scroll-body {
            max-height: 560px;
            overflow-y: auto;
            width: 100%;
        }
        .table-scroll-body table, .custom-table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }
        .table-scroll-body thead th, .custom-table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8f9fb;
            box-shadow: 0 1px 0 var(--border);
        }

        /* Profile Modal */
        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:3000; display:none; align-items:flex-start; justify-content:center; padding-top:120px; }
        .modal-overlay.open { display:flex; }
        .modal-box { background:#fff; border-radius:var(--radius-lg); width:100%; max-width:420px; box-shadow:var(--shadow-md); overflow:hidden; }
        .modal-header { padding:20px 24px 0; display:flex; align-items:center; justify-content:space-between; }
        .modal-title { font-size:18px; font-weight:700; color:var(--text-dark); }
        .modal-close { background:none; border:none; font-size:22px; cursor:pointer; color:var(--text-muted); line-height:1; }
        .modal-body { padding:20px 24px 24px; }
        .modal-avatar { width:64px; height:64px; border-radius:50%; background:var(--primary); color:#fff; font-size:22px; font-weight:700; display:flex; align-items:center; justify-content:center; margin-bottom:6px; }
        .mform-group { margin-bottom:14px; }
        .mform-label { display:block; font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
        .mform-control { width:100%; padding:10px 14px; border:1.5px solid var(--border); border-radius:var(--radius); font-size:14px; color:var(--text-dark); outline:none; }
        .btn-save { width:100%; padding:12px; background:var(--primary); color:#fff; border:none; border-radius:var(--radius); font-size:14px; font-weight:700; cursor:pointer; margin-top:8px; }
        .btn-save:hover { background:var(--primary-dark); }

        /* Footer */
        .footer { background:#fff; border-top:1px solid var(--border); padding:48px 0 0; margin-top:auto; }
        .footer-grid { display:grid; grid-template-columns:1.6fr 1fr 1fr 1fr; gap:48px; padding-bottom:40px; }
        .footer-brand h3 { font-size:20px; font-weight:800; color:var(--text-dark); margin-bottom:8px; }
        .footer-brand h3 span { color:var(--primary); }
        .footer-brand p { font-size:13px; color:var(--text-muted); line-height:1.7; max-width:260px; }
        .footer-col h4 { font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:14px; }
        .footer-col li { margin-bottom:8px; }
        .footer-col a { font-size:13px; color:var(--text-muted); transition:var(--transition); }
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

@include('partials.navbar')

<main class="main-content">
    <div class="container" style="padding-top: 32px; padding-bottom: 48px;">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</main>

@include('partials.profile-modal')

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
                    <li><a href="{{ route('home') }}">How it works</a></li>
                    <li><a href="{{ route('vehicles.index') }}">Featured</a></li>
                    <li><a href="{{ route('vehicles.index') }}">Partnership</a></li>
                    <li><a href="{{ route('home') }}">Business Relation</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Community</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Blog</a></li>
                    <li><a href="{{ route('home') }}">Podcast</a></li>
                    <li><a href="{{ route('home') }}">Invite a friend</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Socials</h4>
                <ul>
                    <li><a href="https://discord.com" target="_blank">Discord</a></li>
                    <li><a href="https://instagram.com" target="_blank">Instagram</a></li>
                    <li><a href="https://twitter.com" target="_blank">Twitter</a></li>
                    <li><a href="https://facebook.com" target="_blank">Facebook</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy;{{ date('Y') }} ED.RENT. All rights reserved</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy &amp; Policy</a>
                <a href="#">Terms &amp; Condition</a>
            </div>
        </div>
    </div>
</footer>

<script>
function toggleProfile() {
    var d = document.getElementById('profileDropdown');
    if (d) d.classList.toggle('open');
}
function closeProfileDropdown() {
    var d = document.getElementById('profileDropdown');
    if (d) d.classList.remove('open');
}
function openProfileModal() {
    var m = document.getElementById('profileModal');
    if (m) m.classList.add('open');
}
function closeProfileModal() {
    var m = document.getElementById('profileModal');
    if (m) m.classList.remove('open');
}
function openMobileMenu() {
    var m = document.getElementById('mobileMenu');
    if (m) m.classList.add('open');
}
function closeMobileMenu() {
    var m = document.getElementById('mobileMenu');
    if (m) m.classList.remove('open');
}
document.addEventListener('click', function(e) {
    var dropdown = document.getElementById('profileDropdown');
    var btn = document.getElementById('profileBtn');
    if (dropdown && btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('open');
    }
});
</script>
@stack('scripts')
</body>
</html>
