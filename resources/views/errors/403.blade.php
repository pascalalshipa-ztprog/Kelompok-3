<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak | SuaraKampus</title>
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body class="portal-page">
    <main style="min-height: 100vh; display: grid; place-items: center; text-align: center; padding: 24px;">
        <div>
            <p class="eyebrow">Error 403</p>
            <h1>Akses Ditolak</h1>
            <p>Kamu tidak memiliki izin untuk membuka halaman ini.</p>

            @auth
                <a class="primary-button primary-button--link"
                   href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard') }}">
                    Kembali ke Dashboard
                </a>
            @else
                <a class="primary-button primary-button--link" href="{{ route('login') }}">
                    Kembali ke Login
                </a>
            @endauth
        </div>
    </main>
</body>
</html>