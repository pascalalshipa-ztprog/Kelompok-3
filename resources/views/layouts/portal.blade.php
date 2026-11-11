<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f7f3">
    <title>@yield('title') | SuaraKampus</title>
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}?v={{ filemtime(public_path('css/portal.css')) }}">
</head>
<body class="portal-page">
    <div class="portal-shell">
        <aside class="sidebar" id="portal-sidebar" aria-label="Sidebar navigasi">
            <a class="brand brand--sidebar" href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard') }}">
                <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3 19 6v5c0 4.5-2.8 7.8-7 10-4.2-2.2-7-5.5-7-10V6l7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span><strong>SuaraKampus</strong><small>Portal Pengaduan</small></span>
            </a>
            <p class="nav-caption">{{ auth()->user()->isAdmin() ? 'ADMINISTRASI' : 'RUANG MAHASISWA' }}</p>
            <nav class="side-nav" aria-label="Navigasi utama">
                <a class="{{ request()->routeIs('dashboard', 'admin.dashboard') ? 'is-active' : '' }}"
                   @if (request()->routeIs('dashboard', 'admin.dashboard')) aria-current="page" @endif
                   href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard') }}">
                    <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="m4 10 8-7 8 7v9a2 2 0 0 1-2 2h-4v-6h-4v6H6a2 2 0 0 1-2-2v-9Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span class="nav-label">Ringkasan</span><span class="nav-chevron" aria-hidden="true">›</span>
                </a>
                @unless (auth()->user()->isAdmin())
                    <a class="{{ request()->routeIs('complaints.create', 'complaints.edit') ? 'is-active' : '' }}" @if (request()->routeIs('complaints.create', 'complaints.edit')) aria-current="page" @endif href="{{ route('complaints.create') }}">
                        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                        <span class="nav-label">Buat Pengaduan</span><span class="nav-chevron" aria-hidden="true">›</span>
                    </a>
                @endunless
                <a class="{{ (auth()->user()->isAdmin() ? request()->routeIs('admin.complaints.*') : request()->routeIs('complaints.index', 'complaints.show')) ? 'is-active' : '' }}" @if (auth()->user()->isAdmin() ? request()->routeIs('admin.complaints.*') : request()->routeIs('complaints.index', 'complaints.show')) aria-current="page" @endif href="{{ route(auth()->user()->isAdmin() ? 'admin.complaints.index' : 'complaints.index') }}">
                    <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 3h8l4 4v14H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3v5h5m-10 4h6m-6 4h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span class="nav-label">{{ auth()->user()->isAdmin() ? 'Semua Pengaduan' : 'Riwayat Pengaduan' }}</span><span class="nav-chevron" aria-hidden="true">›</span>
                </a>
                @if (auth()->user()->isAdmin())
                    <a class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}" @if (request()->routeIs('admin.users.*')) aria-current="page" @endif href="{{ route('admin.users.index') }}">
                        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-6.5a4 4 0 0 1 0 7.5m2 3a3.5 3.5 0 0 1 2 3.2V20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <span class="nav-label">Manajemen Pengguna</span><span class="nav-chevron" aria-hidden="true">›</span>
                    </a>
                @endif
            </nav>
            <div class="sidebar-tip">
                <span class="sidebar-tip-icon" aria-hidden="true">✦</span>
                <strong>{{ auth()->user()->isAdmin() ? 'Pusat kendali kampus' : 'Suaramu berarti' }}</strong>
                <p>{{ auth()->user()->isAdmin() ? 'Kelola laporan dan pengguna dalam satu tempat.' : 'Aspirasi yang baik membantu kampus terus berkembang.' }}</p>
            </div>
            <div class="sidebar-bottom">
                <a class="profile-mini profile-link" href="{{ route(auth()->user()->isAdmin() ? 'admin.profile.edit' : 'profile.edit') }}" aria-label="Buka profil {{ auth()->user()->name }}">
                    <span class="profile-avatar">
                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ route(auth()->user()->isAdmin() ? 'admin.profile.photo' : 'profile.photo') }}" alt="">
                        @else
                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </span>
                    <span class="profile-info"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->isAdmin() ? 'Administrator' : 'Mahasiswa' }}</small></span>
                    <span class="profile-status" title="Sesi aktif" aria-label="Sesi aktif"></span>
                </a>
                <form id="portal-logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit"><span class="logout-icon" aria-hidden="true">↗</span><span>Keluar dari akun</span></button>
                </form>
            </div>
        </aside>

        <div class="portal-main">
            <header class="topbar">
                <span class="topbar-section">Portal Pengaduan <span>/</span> @yield('title')</span>
                <span class="topbar-date">{{ now()->timezone('Asia/Jakarta')->translatedFormat('l, d M Y') }}</span>
            </header>
            <main class="content">
                @if (session('status'))
                    <p class="notice" role="status">{{ session('status') }}</p>
                @endif
                @if ($errors->any())
                    <div class="notice notice--error" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                @yield('content')
            </main>
            <footer class="portal-footer">Copyright © 2026 Ztecno</footer>
        </div>
    </div>
</body>
</html>