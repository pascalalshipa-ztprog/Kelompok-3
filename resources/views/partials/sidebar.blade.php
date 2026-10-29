<div class="sidebar-backdrop" id="sidebar-backdrop" hidden></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-top">
        <div class="logo">
            <span class="logo-mark">
                <svg class="icon" width="22" height="22"><use href="#i-shield"></use></svg>
            </span>
            <span>
                <strong>SuaraKampus</strong>
                <small>Kelompok 3 • V1.0</small>
            </span>
        </div>
        <button class="button icon-button sidebar-close" id="sidebar-close" data-close-sidebar type="button" aria-label="Tutup navigasi sidebar">
            <svg class="icon" width="20" height="20"><use href="#i-close"></use></svg>
        </button>
    </div>

    <nav class="nav-list" aria-label="Menu Aplikasi">
        @if(Auth::check() && Auth::user()->isAdmin())
            <span class="nav-label">MENU ADMINISTRATOR</span>
            <a class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" style="text-decoration: none;">
                <svg class="icon" width="20" height="20"><use href="#i-grid"></use></svg>
                <span style="text-decoration: none;">Dashboard Admin</span>
            </a>
            <a class="nav-item {{ request()->routeIs('pengaduan.riwayat') ? 'active' : '' }}" href="{{ route('pengaduan.riwayat') }}" style="text-decoration: none;">
                <svg class="icon" width="20" height="20"><use href="#i-history"></use></svg>
                <span style="text-decoration: none;">Semua Pengaduan</span>
                <em id="sidebar-total-badge" data-total-badge>5</em>
            </a>
        @else
            <span class="nav-label">MENU UTAMA</span>
            <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" style="text-decoration: none;">
                <svg class="icon" width="20" height="20"><use href="#i-grid"></use></svg>
                <span style="text-decoration: none;">Dashboard</span>
            </a>
            <a class="nav-item student-only {{ request()->routeIs('pengaduan.create') ? 'active' : '' }}" href="{{ route('pengaduan.create') }}" style="text-decoration: none;">
                <svg class="icon" width="20" height="20"><use href="#i-plus"></use></svg>
                <span style="text-decoration: none;">Buat Pengaduan</span>
            </a>
            <a class="nav-item {{ request()->routeIs('pengaduan.riwayat') ? 'active' : '' }}" href="{{ route('pengaduan.riwayat') }}" style="text-decoration: none;">
                <svg class="icon" width="20" height="20"><use href="#i-history"></use></svg>
                <span style="text-decoration: none;">Riwayat Pengaduan</span>
                <em id="sidebar-total-badge" data-total-badge>5</em>
            </a>
        @endif
    </nav>

    <div class="sidebar-bottom">
        <div class="sidebar-user">
            @auth
                <span class="avatar">{{ Auth::user()->isAdmin() ? 'AK' : 'NM' }}</span>
                <span>
                    <strong>{{ Auth::user()->name }}</strong>
                    <small>
                        @if(Auth::user()->isAdmin())
                            Petugas Administrator
                        @else
                            {{ Auth::user()->identifier ?? '25170018' }} • Informatika
                        @endif
                    </small>
                </span>
            @else
                <span class="avatar">NM</span>
                <span>
                    <strong>Naufal Marzuki</strong>
                    <small>25170018 • Informatika</small>
                </span>
            @endauth
        </div>

        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid rgba(255, 255, 255, 0.08);">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button secondary" style="width: 100%; min-height: 34px; font-size: 11px; background: rgba(255, 255, 255, 0.08); color: #d0dde7; border-color: rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg class="icon" width="16" height="16"><use href="#i-logout"></use></svg>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="button primary" style="width: 100%; min-height: 34px; font-size: 11px; text-decoration: none;">
                    <span>Masuk ke Akun</span>
                </a>
            @endauth
        </div>
    </div>
</aside>
