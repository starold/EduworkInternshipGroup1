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
        .badge-cancelled, .badge-secondary, .badge-other {
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
        .table-scroll-body table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }
        .table-scroll-body thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8f9fb;
            box-shadow: 0 1px 0 var(--border);
        }
    </style>
    @stack('styles')
</head>
<body>

@include('partials.navbar')

<!-- Profile Modal -->
@auth
<div class="modal-overlay" id="profileModal" onclick="if(event.target===this)closeProfileModal()">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Profil</span>
            <button class="modal-close" onclick="closeProfileModal()">×</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('profile.update') }}" id="profileForm" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                    <div class="modal-avatar" id="avatarPreviewContainer" style="overflow:hidden;flex-shrink:0">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" id="avatarPreviewImg" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                        @else
                            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80" alt="Avatar" id="avatarPreviewImg" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                        @endif
                    </div>
                    <input type="file" name="avatar" id="avatarFileInput" accept="image/*" style="display:none" onchange="previewProfileAvatar(this)">
                    <button type="button" class="modal-edit-btn" onclick="document.getElementById('avatarFileInput').click()" style="margin-bottom:0">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit gambar
                    </button>
                </div>
                <div class="mform-group">
                    <label class="mform-label">Nama Lengkap</label>
                    <input type="text" name="name" class="mform-control" placeholder="Nama Lengkap" value="{{ old('name', auth()->user()->name) }}" required>
                </div>
                <div class="mform-group">
                    <label class="mform-label">No WhatsApp</label>
                    <input type="text" name="phone" class="mform-control" placeholder="12345678910" value="{{ old('phone', auth()->user()->phone) }}">
                </div>
                <div class="mform-group">
                    <label class="mform-label">No. KTP</label>
                    <input type="text" name="ktp_number" class="mform-control" placeholder="12345678910" value="{{ old('ktp_number', auth()->user()->ktp_number) }}">
                </div>
                <div class="mform-group">
                    <label class="mform-label">No. SIM</label>
                    <input type="text" name="sim_number" class="mform-control" placeholder="12345678910" value="{{ old('sim_number', auth()->user()->sim_number) }}">
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
function previewProfileAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatarPreviewImg');
            if (img) img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
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
