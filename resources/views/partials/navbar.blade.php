<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <a href="{{ auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('home') }}" class="nav-brand">ED<span>.</span>RENT</a>

        <ul class="nav-links">
            @if(auth()->check() && auth()->user()->isAdmin())
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Home</a></li>
            @else
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            @endif
            @auth
                @if(auth()->user()->isAdmin())
                    <li><a href="{{ route('admin.motors.index') }}" class="{{ request()->routeIs('admin.motors.*') ? 'active' : '' }}">Daftar Kendaraan</a></li>
                    <li><a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">Daftar Pesanan</a></li>
                @else
                    <li><a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">Pilih Kendaraan</a></li>
                    <li><a href="{{ route('customer.bookings.index') }}" class="{{ request()->routeIs('customer.bookings.*') ? 'active' : '' }}">Daftar Pesanan</a></li>
                    <li><a href="{{ route('customer.fines.index') }}" class="{{ request()->routeIs('customer.fines.*') ? 'active' : '' }}">Denda Saya</a></li>
                @endif
            @else
                <li><a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">Pilih Kendaraan</a></li>
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
                            <span class="di-icon">👤</span> Profil Saya
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <span class="di-icon">📊</span> Dashboard
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.bookings.index') }}">
                                <span class="di-icon">📋</span> Daftar Pesanan
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.motors.index') }}">
                                <span class="di-icon">🏍</span> Kendaraan
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.fines.index') }}">
                                <span class="di-icon">⚠</span> Manajemen Denda
                            </a>
                        @else
                            <a class="dropdown-item" href="{{ route('customer.bookings.index') }}">
                                <span class="di-icon">📋</span> Daftar Pesanan
                            </a>
                            <a class="dropdown-item" href="{{ route('customer.fines.index') }}">
                                <span class="di-icon">⚠</span> Denda Saya
                            </a>
                            <a class="dropdown-item" href="{{ route('home') }}">
                                <span class="di-icon">🏍</span> Kendaraan
                            </a>
                        @endif
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
        <button class="mobile-menu-close" onclick="closeMobileMenu()" aria-label="Tutup"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        <div style="font-size:20px;font-weight:800;color:#0d0d0d">ED<span style="color:#1847ED">.</span>RENT</div>
        <div class="mobile-menu-links">
            @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Home</a>
            @else
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            @endif
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.motors.index') }}" class="{{ request()->routeIs('admin.motors.*') ? 'active' : '' }}">Daftar Kendaraan</a>
                    <a href="{{ route('admin.bookings.index') }}">Daftar Pesanan</a>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.fines.index') }}">Manajemen Denda</a>
                @else
                    <a href="{{ route('vehicles.index') }}">Pilih Kendaraan</a>
                    <a href="{{ route('customer.bookings.index') }}">Daftar Pesanan</a>
                    <a href="{{ route('customer.fines.index') }}">Denda Saya</a>
                @endif
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
