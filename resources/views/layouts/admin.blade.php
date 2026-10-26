<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - ED.RENT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4758D6; --primary-dark: #3847B5;
            --sidebar-bg: #1a1a2e; --sidebar-width: 250px;
            --text-dark: #1a1a2e; --text-body: #4a4a5a; --text-muted: #8a8a9a;
            --border: #e2e4e9; --bg-light: #f8f9fb; --bg-white: #ffffff;
            --success: #25D366; --danger: #e74c3c; --warning: #f59e0b;
            --radius: 10px; --shadow: 0 2px 8px rgba(0,0,0,.08);
            --transition: all .2s;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; color:var(--text-body); background:var(--bg-light); min-height:100vh; display:flex; -webkit-font-smoothing:antialiased; }
        a { text-decoration:none; color:inherit; }

        /* Sidebar */
        .sidebar { width:var(--sidebar-width); background:var(--sidebar-bg); min-height:100vh; position:fixed; left:0; top:0; bottom:0; display:flex; flex-direction:column; z-index:200; }
        .sidebar-brand { padding:24px 20px; border-bottom:1px solid rgba(255,255,255,.08); }
        .sidebar-brand h1 { font-size:22px; font-weight:800; color:#fff; }
        .sidebar-brand h1 span { color:var(--primary); }
        .sidebar-brand p { font-size:12px; color:rgba(255,255,255,.4); margin-top:2px; }
        .sidebar-nav { flex:1; padding:20px 0; overflow-y:auto; }
        .nav-section-label { font-size:10px; font-weight:700; color:rgba(255,255,255,.3); text-transform:uppercase; letter-spacing:1px; padding:0 20px 8px; }
        .nav-item { display:flex; align-items:center; gap:12px; padding:11px 20px; color:rgba(255,255,255,.6); font-size:14px; font-weight:500; transition:var(--transition); cursor:pointer; }
        .nav-item:hover { color:#fff; background:rgba(255,255,255,.06); }
        .nav-item.active { color:#fff; background:var(--primary); }
        .nav-item .icon { font-size:18px; width:20px; text-align:center; }
        .sidebar-user { padding:16px 20px; border-top:1px solid rgba(255,255,255,.08); }
        .sidebar-user .uname { font-size:14px; font-weight:600; color:#fff; margin-bottom:4px; }
        .sidebar-user .urole { font-size:12px; color:rgba(255,255,255,.4); }
        .btn-logout { width:100%; margin-top:12px; padding:9px; background:rgba(255,255,255,.06); color:rgba(255,255,255,.6); border:1px solid rgba(255,255,255,.1); border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); }
        .btn-logout:hover { background:var(--danger); color:#fff; border-color:transparent; }

        /* Main */
        .main { margin-left:var(--sidebar-width); flex:1; display:flex; flex-direction:column; }
        .topbar { background:var(--bg-white); border-bottom:1px solid var(--border); padding:0 32px; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .topbar h2 { font-size:18px; font-weight:700; color:var(--text-dark); }
        .topbar-right { display:flex; align-items:center; gap:16px; }
        .topbar-badge { display:flex; align-items:center; gap:8px; font-size:14px; font-weight:600; color:var(--text-body); }
        .content { padding:32px; flex:1; }

        /* Cards, Tables, Alerts */
        .alert { padding:14px 18px; border-radius:var(--radius); margin-bottom:20px; font-size:14px; font-weight:500; }
        .alert-success { background:#d4edda; color:#155724; }
        .alert-danger { background:#f8d7da; color:#721c24; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h1>ED<span>.</span>RENT</h1>
        <p>Admin Panel</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="icon">📊</span> Dashboard
        </a>
        <a href="{{ route('admin.motors.index') }}" class="nav-item {{ request()->routeIs('admin.motors.*') ? 'active' : '' }}">
            <span class="icon">🏍</span> Kelola Motor
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="nav-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <span class="icon">📋</span> Pemesanan
        </a>
        <div class="nav-section-label" style="margin-top:16px">Akses Cepat</div>
        <a href="{{ route('home') }}" class="nav-item" target="_blank">
            <span class="icon">🌐</span> Lihat Website
        </a>
    </nav>
    <div class="sidebar-user">
        <div class="uname">{{ auth()->user()->name }}</div>
        <div class="urole">{{ auth()->user()->role->label() }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Keluar</button>
        </form>
    </div>
</aside>

<!-- Main Content -->
<div class="main">
    <header class="topbar">
        <h2>@yield('page-title', 'Dashboard')</h2>
        <div class="topbar-right">
            <div class="topbar-badge">👤 {{ auth()->user()->name }}</div>
        </div>
    </header>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
