<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7f2">
    <title>@yield('title') | SuaraKampus</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ filemtime(public_path('css/auth.css')) }}">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <a class="auth-brand" href="{{ route('login') }}" aria-label="SuaraKampus, halaman masuk">
            <span class="auth-brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 19V8.5L12 4l8 4.5V19M8 19v-6h8v6M3 19h18M9 9h.01M15 9h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <span>
                <span class="auth-brand-name">SuaraKampus</span>
                <span class="auth-brand-caption">Portal Pengaduan</span>
            </span>
        </a>

        @yield('content')

        <p class="auth-footnote">Ruang aman untuk menyampaikan aspirasi dan membangun kampus bersama.<br>Copyright © 2026 Ztecno</p>
    </main>
</body>
</html>