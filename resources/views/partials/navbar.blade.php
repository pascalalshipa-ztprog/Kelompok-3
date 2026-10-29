<header class="topbar">
    <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
        <button class="button icon-button menu-button" id="menu-button" type="button" aria-label="Buka menu navigasi">
            <svg class="icon" width="22" height="22"><use href="#i-menu"></use></svg>
        </button>
        <div class="topbar-title">
            <span class="eyebrow" id="portal-label">
                @if(Auth::check() && Auth::user()->isAdmin())
                    PORTAL ADMINISTRATOR
                @else
                    PORTAL MAHASISWA
                @endif
            </span>
            <div class="topbar-page-label">
                @if (request()->routeIs('admin.dashboard'))
                    Dashboard Administrator
                @elseif (request()->routeIs('dashboard'))
                    Dashboard Aspirasi
                @elseif (request()->routeIs('pengaduan.create'))
                    Buat Pengaduan
                @elseif (request()->routeIs('pengaduan.riwayat'))
                    Riwayat Pengaduan
                @else
                    SuaraKampus
                @endif
            </div>
        </div>
    </div>

    <div class="topbar-actions">
        <!-- Pencarian Cepat Interaktif -->
        <div style="position: relative;">
            <label class="quick-search" for="quick-search-input">
                <svg class="icon" width="18" height="18"><use href="#i-search"></use></svg>
                <input id="quick-search-input" aria-label="Pencarian cepat pengaduan" placeholder="Cari pengaduan cepat..." autocomplete="off">
                <kbd id="search-shortcut">⌘ K</kbd>
            </label>

            <!-- Dropdown Hasil Pencarian Cepat -->
            <div id="quick-search-results" class="quick-search-popover" hidden>
                <div class="popover-header">
                    <span>HASIL PENCARIAN</span>
                    <small id="quick-search-count">0 ditemukan</small>
                </div>
                <div id="quick-search-list" class="quick-search-items"></div>
            </div>
        </div>

        <!-- Tombol Pengalih Mode Terang / Gelap (Light / Dark Mode) -->
        <button aria-label="Ganti Mode Terang / Gelap" class="button icon-button theme-toggle-btn" id="theme-toggle" type="button" title="Ganti Mode Tema (Terang/Gelap)">
            <svg class="icon theme-icon-sun" width="20" height="20"><use href="#i-sun"></use></svg>
            <svg class="icon theme-icon-moon" width="20" height="20"><use href="#i-moon"></use></svg>
        </button>

        <!-- Tombol Notifikasi & Popover Interaktif -->
        <div style="position: relative;">
            <button aria-label="Pusat Notifikasi" class="notification-button" id="notification-button" type="button" title="Lihat notifikasi terkini">
                <svg class="icon" width="20" height="20"><use href="#i-bell"></use></svg>
                <span id="unread-dot"></span>
            </button>

            <!-- Panel Notifikasi Popover -->
            <div id="notification-popover" class="notification-popover" hidden>
                <div class="notification-header">
                    <strong>Pemberitahuan Terbaru</strong>
                    <span class="live-badge" style="background: var(--teal-soft); color: var(--teal); font-size: 9px; padding: 3px 7px;">3 Baru</span>
                </div>
                <div class="notification-list">
                    <div class="notification-item" onclick="window.openFirstReport && window.openFirstReport()">
                        <div class="notif-icon blue"><svg class="icon" width="14" height="14"><use href="#i-clock"></use></svg></div>
                        <div class="notif-text">
                            <strong>PGD-2025-084 diverifikasi</strong>
                            <p>Laporan AC Gedung B telah dijadwalkan perbaikan oleh tim sarpras.</p>
                            <small>2 jam lalu</small>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notif-icon green"><svg class="icon" width="14" height="14"><use href="#i-check"></use></svg></div>
                        <div class="notif-text">
                            <strong>PGD-2025-071 selesai</strong>
                            <p>Pergantian router Wi-Fi ruang baca perpustakaan telah rampung.</p>
                            <small>1 hari lalu</small>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notif-icon green"><svg class="icon" width="14" height="14"><use href="#i-check"></use></svg></div>
                        <div class="notif-text">
                            <strong>PGD-2025-063 disetujui BAAK</strong>
                            <p>Nilai mata kuliah Pemrograman Web telah diperbarui pada portal SIAKAD.</p>
                            <small>3 hari lalu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profil Pengguna & Autentikasi -->
        @auth
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="avatar small" title="{{ Auth::user()->name }} ({{ Auth::user()->role }})">
                    {{ Auth::user()->isAdmin() ? 'AK' : 'NM' }}
                </span>
            </div>
        @else
            <a href="{{ route('login') }}" class="button primary" style="min-height: 34px; padding: 0 14px; font-size: 11px;">
                Masuk
            </a>
        @endauth
    </div>
</header>
