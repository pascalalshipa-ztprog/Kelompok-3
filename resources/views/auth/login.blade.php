<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk | SuaraKampus Kelompok 3</title>

    <link rel="stylesheet" href="{{ asset('css/suara-kampus.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script>
        // Apply theme preference immediately
        (function() {
            const savedTheme = localStorage.getItem('suara_kampus_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body>
    <div class="login-page" id="login-page">
        <!-- Kolom Kiri: Branding & Informasi Platform -->
        <section class="login-brand-panel">
            <div class="login-brand-top">
                <div class="logo">
                    <span class="logo-mark" style="background: var(--teal);">
                        <svg class="icon" width="22" height="22"><use href="#i-shield"></use></svg>
                    </span>
                    <span>
                        <strong>SuaraKampus</strong>
                        <small>Portal Pengaduan Mahasiswa</small>
                    </span>
                </div>
                <span class="login-campus-label">KELOMPOK 3 • V1.0</span>
            </div>

            <div class="login-brand-copy">
                <span class="login-kicker">TRANSPARAN & CEPAT TANGGAP</span>
                <h1>Sampaikan kritik, saran, & laporan kampusmu.</h1>
                <p>Platform terpadu untuk menjembatani aspirasi mahasiswa dengan biro kemahasiswaan dan sarana prasarana demi lingkungan kampus yang lebih baik.</p>

                <div class="login-feature-list">
                    <div>
                        <span>
                            <svg class="icon" width="18" height="18"><use href="#i-shield"></use></svg>
                        </span>
                        <div>
                            <strong>Aman & Terjaga</strong>
                            <small>Identitas pelapor terproteksi penuh oleh sistem.</small>
                        </div>
                    </div>
                    <div>
                        <span>
                            <svg class="icon" width="18" height="18"><use href="#i-clock"></use></svg>
                        </span>
                        <div>
                            <strong>Real-time Tracking</strong>
                            <small>Pantau status perbaikan langkah demi langkah.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="login-brand-footer">
                <span>© 2026 SuaraKampus Kelompok 3 • Hak Cipta Dilindungi</span>
            </div>
        </section>

        <!-- Kolom Kanan: Form Autentikasi -->
        <section class="login-form-panel" style="position: relative;">
            <div style="position: absolute; top: 22px; right: 28px; z-index: 10;">
                <button aria-label="Ganti Mode Terang / Gelap" class="button icon-button theme-toggle-btn" id="login-theme-toggle" type="button" title="Ganti Mode Tema (Terang/Gelap)">
                    <svg class="icon theme-icon-sun" width="20" height="20"><use href="#i-sun"></use></svg>
                    <svg class="icon theme-icon-moon" width="20" height="20"><use href="#i-moon"></use></svg>
                </button>
            </div>
            <div class="login-form">
                <div class="login-mobile-logo" style="margin-bottom: 28px;">
                    <div class="logo">
                        <span class="logo-mark">
                            <svg class="icon" width="22" height="22"><use href="#i-shield"></use></svg>
                        </span>
                        <span>
                            <strong>SuaraKampus</strong>
                            <small>Portal Pengaduan</small>
                        </span>
                    </div>
                </div>

                <div class="login-heading">
                    <h2>Selamat Datang</h2>
                    <p>Silakan masuk menggunakan NIM (Mahasiswa) atau Email resmi (Admin).</p>
                </div>

                @if ($errors->any())
                    <div class="login-alert">
                        <span>!</span>
                        <div>
                            <strong>Gagal Masuk</strong>
                            <p>{{ $errors->first() }}</p>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert" style="background: var(--green-soft); color: var(--green); border: 1px solid #bfead8; margin-bottom: 20px; border-radius: 10px; padding: 12px; font-size: 11px;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" id="login-form">
                    @csrf

                    <div class="login-field">
                        <label for="identifier" style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 6px; color: var(--ink);">
                            NIM / Email Kampus
                        </label>
                        <div class="login-input-wrap">
                            <svg class="icon" width="18" height="18"><use href="#i-users"></use></svg>
                            <input id="identifier" name="identifier" type="text" placeholder="Masukkan NIM atau email..." value="{{ old('identifier', '25170018') }}" required autofocus autocomplete="username">
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password" style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 6px; color: var(--ink);">
                            Kata Sandi
                        </label>
                        <div class="login-input-wrap">
                            <svg class="icon" width="18" height="18"><use href="#i-shield"></use></svg>
                            <input id="password" name="password" type="password" placeholder="Masukkan kata sandi..." value="mahasiswa123" required autocomplete="current-password">
                            <button class="password-toggle" id="password-toggle" type="button">Lihat</button>
                        </div>
                    </div>

                    <div class="login-options">
                        <label class="remember-option">
                            <input type="checkbox" name="remember" id="remember" checked>
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button class="button primary login-submit" type="submit" style="width: 100%; min-height: 46px; font-size: 13px;">
                        Masuk ke Akun
                    </button>

                    <!-- Divider Akun Demo -->
                    <div class="demo-divider">
                        <span></span>
                        <small>AKUN DEMO CEPAT</small>
                        <span></span>
                    </div>

                    <!-- Tombol Cepat Akun Demo (1-Click) -->
                    <div class="demo-accounts">
                        <button type="button" class="demo-account-btn" data-demo="student" onclick="fillCredentials('25170018', 'mahasiswa123')">
                            <span class="demo-account-icon student">
                                <svg class="icon" width="18" height="18"><use href="#i-users"></use></svg>
                            </span>
                            <span>
                                <strong>Akun Mahasiswa</strong>
                                <small>Naufal (25170018)</small>
                                <em>Klik untuk mengisi</em>
                            </span>
                        </button>

                        <button type="button" class="demo-account-btn" data-demo="admin" onclick="fillCredentials('admin@kampus.ac.id', 'admin123')">
                            <span class="demo-account-icon admin">
                                <svg class="icon" width="18" height="18"><use href="#i-shield"></use></svg>
                            </span>
                            <span>
                                <strong>Akun Administrator</strong>
                                <small>Admin Kemahasiswaan</small>
                                <em>Klik untuk mengisi</em>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <!-- SVG Sprites -->
    <svg aria-hidden="true" class="svg-sprite">
        <defs>
            <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 3 4 6v5c0 5 3.4 8.4 8 10 4.6-1.6 8-5 8-10V6z"/><path d="m9 12 2 2 4-4"/></symbol>
            <symbol id="i-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6m3-3h-6"/></symbol>
            <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
            <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></symbol>
            <symbol id="i-moon" viewBox="0 0 24 24"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></symbol>
        </defs>
    </svg>

    <script>
        function fillCredentials(id, pass) {
            document.getElementById('identifier').value = id;
            document.getElementById('password').value = pass;
        }

        const pwdInput = document.getElementById('password');
        const pwdToggle = document.getElementById('password-toggle');
        if (pwdToggle && pwdInput) {
            pwdToggle.addEventListener('click', function() {
                if (pwdInput.type === 'password') {
                    pwdInput.type = 'text';
                    pwdToggle.textContent = 'Sembunyikan';
                } else {
                    pwdInput.type = 'password';
                    pwdToggle.textContent = 'Lihat';
                }
            });
        }

        const loginThemeToggle = document.getElementById('login-theme-toggle');
        if (loginThemeToggle) {
            loginThemeToggle.addEventListener('click', function() {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                const nextTheme = isDark ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', nextTheme);
                localStorage.setItem('suara_kampus_theme', nextTheme);
            });
        }
    </script>
</body>
</html>

