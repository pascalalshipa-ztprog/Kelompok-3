<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | SuaraKampus Kelompok 3</title>

    <!-- Asset CSS Aplikasi -->
    <link rel="stylesheet" href="{{ asset('css/suara-kampus.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script>
        // Inisialisasi tema instan tanpa jeda putih
        (function() {
            const savedTheme = localStorage.getItem('suara_kampus_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    @stack('styles')
</head>
<body>
    <!-- SVG Sprite Icons -->
    <svg aria-hidden="true" class="svg-sprite">
        <defs>
            <symbol id="i-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></symbol>
            <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
            <symbol id="i-history" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8M3 3v5h5M12 7v5l3 2"/></symbol>
            <symbol id="i-help" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9.7 9a2.4 2.4 0 1 1 3.5 2.2c-.8.4-1.2.9-1.2 1.8M12 17h.01"/></symbol>
            <symbol id="i-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6v-.2h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"/></symbol>
            <symbol id="i-logout" viewBox="0 0 24 24"><path d="m10 17 5-5-5-5M15 12H3M15 3h5a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-5"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></symbol>
            <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></symbol>
            <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14m-5-5 5 5-5 5"/></symbol>
            <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></symbol>
            <symbol id="i-file" viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6zM14 3v5h5M9 13h6M9 17h4"/></symbol>
            <symbol id="i-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6m3-3h-6"/></symbol>
            <symbol id="i-download" viewBox="0 0 24 24"><path d="M12 3v12m-5-5 5 5 5-5M5 21h14"/></symbol>
            <symbol id="i-chevron" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></symbol>
            <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3 4 6v5c0 5 3.4 8.4 8 10 4.6-1.6 8-5 8-10V6z"/><path d="m9 12 2 2 4-4"/></symbol>
            <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></symbol>
            <symbol id="i-upload" viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M5 20h14"/></symbol>
            <symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M12 11v5"/></symbol>
            <symbol id="i-map-pin" viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></symbol>
            <symbol id="i-paperclip" viewBox="0 0 24 24"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.48-8.48a4 4 0 0 1 5.66 5.66l-8.49 8.48a2 2 0 0 1-2.83-2.83l7.78-7.78"/></symbol>
            <symbol id="i-check-circle" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></symbol>
            <symbol id="i-alert" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/></symbol>
            <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></symbol>
            <symbol id="i-moon" viewBox="0 0 24 24"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></symbol>
        </defs>
    </svg>

    <div class="app-shell" id="app-shell">
        <!-- Sidebar Navigasi -->
        @include('partials.sidebar')

        <div class="main-shell">
            <!-- Navbar Topbar -->
            @include('partials.navbar')

            <!-- Content Dinamis -->
            <main class="main-content">
                @if (session('success'))
                    <div class="alert" style="background: var(--green-soft); color: var(--green); border: 1px solid #bce6d5; display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                        <svg class="icon" width="20" height="20"><use href="#i-check-circle"></use></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert" style="background: var(--coral-soft); color: var(--coral); border: 1px solid #f2cfc7; display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                        <svg class="icon" width="20" height="20"><use href="#i-alert"></use></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Backdrop & Detail Drawer Pengaduan -->
    <div class="drawer-backdrop" id="drawer-backdrop" hidden></div>
    <aside class="report-drawer" id="report-drawer" hidden aria-label="Detail Pengaduan">
        <div class="drawer-header">
            <div>
                <span class="date-label" data-drawer="id">PGD-2025-000</span>
                <strong data-drawer="category">Kategori Pengaduan</strong>
            </div>
            <button aria-label="Tutup Detail" class="button icon-button" id="close-drawer" type="button">
                <svg class="icon" width="20" height="20"><use href="#i-close"></use></svg>
            </button>
        </div>

        <h2 data-drawer="title">Judul Pengaduan Mahasiswa</h2>

        <div class="drawer-meta">
            <span>
                <small>Status Laporan</small>
                <div data-drawer="status">
                    <span class="status status-diproses"><span class="status-dot"></span>Diproses</span>
                </div>
            </span>
            <span>
                <small>Tanggal Diajukan</small>
                <strong data-drawer="date">22 Mei 2025</strong>
            </span>
        </div>

        <div class="drawer-section">
            <span class="drawer-label">LOKASI KEJADIAN</span>
            <p data-drawer="location">Gedung B Lantai 3, Ruang B.301</p>
        </div>

        <div class="drawer-section">
            <span class="drawer-label">DESKRIPSI LENGKAP LAPORAN</span>
            <p data-drawer="description">Deskripsi pengaduan mahasiswa...</p>
        </div>

        <div class="drawer-section" id="drawer-attachment-section">
            <span class="drawer-label">DOKUMEN / FOTO PENDUKUNG</span>
            <div id="drawer-attachment-box" style="display: flex; align-items: center; gap: 8px; background: var(--canvas); padding: 10px 14px; border-radius: 8px; font-size: 11px;">
                <svg class="icon" width="16" height="16"><use href="#i-paperclip"></use></svg>
                <span id="drawer-attachment-name">lampiran-pengaduan.jpg</span>
            </div>
        </div>

        <div class="drawer-section">
            <span class="drawer-label">ALUR PENYELESAIAN (TIMELINE)</span>
            <div class="timeline" id="drawer-timeline">
                <div class="done"><i></i><span><strong>Laporan Diterima</strong><small>22 Mei 2025 • 09.42 WIB</small></span></div>
                <div class="done"><i></i><span><strong>Verifikasi Petugas</strong><small>Laporan telah diverifikasi oleh biro terkait</small></span></div>
                <div><i></i><span><strong>Tindak Lanjut Teknisi</strong><small>Dalam proses pengerjaan lapangan</small></span></div>
            </div>
        </div>

        <div class="drawer-section">
            <span class="drawer-label">TANGGAPAN RESMI KAMPUS</span>
            <div class="official-response">
                <div class="response-avatar">KM</div>
                <div>
                    <strong>Bagian Pelayanan Kemahasiswaan & Sarpras</strong>
                    <p data-drawer="response">Petugas teknisi telah menjadwalkan pengecekan langsung ke lokasi terkait.</p>
                </div>
            </div>
        </div>

        <!-- Panel Khusus Administrator: Update Status Langsung -->
        @if(Auth::check() && Auth::user()->isAdmin())
            <div class="drawer-section admin-action-box" id="admin-action-box" style="margin-top: 18px;">
                <span class="drawer-label" style="color: var(--teal); font-weight: 800;">AKSI ADMINISTRATOR: KONTROL STATUS</span>
                <div class="field" style="margin-bottom: 12px;">
                    <span>Perbarui Status Laporan:</span>
                    <select id="drawer-status-select" class="field" style="padding: 10px;">
                        <option value="Pending">Pending (Menunggu)</option>
                        <option value="Diproses">Diproses (Pengerjaan)</option>
                        <option value="Selesai">Selesai (Tuntas)</option>
                        <option value="Ditolak">Ditolak (Tidak Sesuai)</option>
                    </select>
                </div>
                <button class="button primary" id="save-status-btn" type="button" style="width: 100%;">
                    Simpan Perubahan Status
                </button>
            </div>
        @endif

        <div style="margin-top: 24px;">
            <button class="button secondary" style="width: 100%;" type="button" onclick="window.closeDrawerModal()">
                Tutup Jendela Detail
            </button>
        </div>
    </aside>

    <!-- Global Floating Toast Notification -->
    <div class="toast" id="toast" hidden role="status" aria-live="polite">
        <svg class="icon" width="18" height="18"><use href="#i-check"></use></svg>
        <span id="toast-message">Pesan notifikasi berhasil.</span>
    </div>

    <!-- Data Awal untuk Komponen JavaScript -->
    <script>
        window.SUARA_KAMPUS_REPORTS = @json($reports ?? []);
        window.SUARA_KAMPUS_IS_ADMIN = {{ Auth::check() && Auth::user()->isAdmin() ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('js/suara-kampus.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
