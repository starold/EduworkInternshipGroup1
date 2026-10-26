<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - ED.RENT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; min-height:100vh; display:flex; -webkit-font-smoothing:antialiased; }
        a { text-decoration:none; color:inherit; }

        .auth-wrapper { display:flex; width:100%; min-height:100vh; }

        /* Left Panel - Hero Image */
        .auth-hero {
            flex:1;
            background:linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position:relative;
            overflow:hidden;
            display:flex;
            align-items:flex-end;
            padding:48px;
        }
        .auth-hero img {
            position:absolute; inset:0; width:100%; height:100%;
            object-fit:cover; opacity:.5;
        }
        .auth-hero-content { position:relative; z-index:1; }
        .auth-hero-brand { font-size:28px; font-weight:800; color:#fff; margin-bottom:32px; }
        .auth-hero-brand span { color:#4758D6; }
        .auth-hero-title { font-size:40px; font-weight:800; color:#fff; line-height:1.2; }

        /* Right Panel - Form */
        .auth-form-panel {
            width:480px;
            min-width:400px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            padding:48px;
            background:#fff;
        }
        .auth-form-panel .mobile-brand {
            display:none;
            font-size:24px; font-weight:800; color:#1a1a2e; margin-bottom:24px;
        }
        .auth-form-panel .mobile-brand span { color:#4758D6; }

        .auth-title { font-size:28px; font-weight:800; color:#1a1a2e; margin-bottom:8px; }
        .auth-subtitle { font-size:14px; color:#8a8a9a; margin-bottom:32px; line-height:1.6; }

        .form-group { margin-bottom:20px; }
        .form-label { display:block; font-size:13px; font-weight:600; color:#4a4a5a; margin-bottom:8px; }
        .form-control {
            width:100%; padding:12px 16px; border:1.5px solid #e2e4e9;
            border-radius:8px; font-size:14px; font-family:'Inter',sans-serif;
            color:#1a1a2e; transition:all .2s; outline:none;
        }
        .form-control:focus { border-color:#4758D6; box-shadow:0 0 0 3px rgba(71,88,214,.1); }
        .form-control.is-invalid { border-color:#e74c3c; }
        .invalid-feedback { color:#e74c3c; font-size:12px; margin-top:6px; display:block; }

        .form-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
        .form-row .form-label { margin-bottom:0; }
        .form-link { font-size:13px; color:#4758D6; font-weight:500; }
        .form-link:hover { text-decoration:underline; }

        .remember-row { display:flex; align-items:center; gap:8px; margin-bottom:24px; }
        .remember-row input { accent-color:#4758D6; }
        .remember-row label { font-size:13px; color:#4a4a5a; cursor:pointer; }

        .btn-submit {
            width:100%; padding:14px; background:#4758D6; color:#fff;
            font-size:15px; font-weight:700; border:none; border-radius:8px;
            cursor:pointer; transition:all .3s; margin-bottom:20px;
        }
        .btn-submit:hover { background:#3847B5; transform:translateY(-1px); box-shadow:0 6px 20px rgba(71,88,214,.35); }

        .auth-divider { text-align:center; color:#8a8a9a; font-size:13px; margin-bottom:16px; position:relative; }
        .auth-divider::before, .auth-divider::after {
            content:''; position:absolute; top:50%; width:40%; height:1px; background:#e2e4e9;
        }
        .auth-divider::before { left:0; }
        .auth-divider::after { right:0; }

        .btn-google {
            width:100%; padding:12px; background:#fff; color:#4a4a5a;
            font-size:14px; font-weight:600; border:1.5px solid #e2e4e9;
            border-radius:8px; cursor:pointer; display:flex; align-items:center;
            justify-content:center; gap:10px; margin-bottom:20px; transition:all .2s;
        }
        .btn-google:hover { border-color:#4758D6; background:#f8f9ff; }
        .btn-google img { width:20px; }

        .auth-footer { text-align:center; font-size:14px; color:#8a8a9a; }
        .auth-footer a { color:#4758D6; font-weight:600; }

        .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px; }
        .alert-danger { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }

        @media(max-width:768px) {
            .auth-hero { display:none; }
            .auth-form-panel { width:100%; padding:32px 24px; }
            .auth-form-panel .mobile-brand { display:block; }
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-hero">
        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80" alt="Rental Motor" onerror="this.style.display='none'">
        <div class="auth-hero-content">
            <div class="auth-hero-brand">ED<span>.</span>RENT</div>
            <h1 class="auth-hero-title">Rental Motor<br>Cepat & Aman,<br>Mulai Rp75.000/<br>hari</h1>
        </div>
    </div>
    <div class="auth-form-panel">
        <div class="mobile-brand">ED<span>.</span>RENT</div>
        @yield('form')
    </div>
</div>
</body>
</html>
