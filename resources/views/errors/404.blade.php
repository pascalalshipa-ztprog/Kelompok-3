<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan | SuaraKampus</title>
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body class="portal-page">
    <main style="min-height: 100vh; display: grid; place-items: center; text-align: center; padding: 24px;">
        <div>
            <p class="eyebrow">Error 404</p>
            <h1>Halaman Tidak Ditemukan</h1>
            <p>Halaman yang kamu cari tidak ditemukan atau mungkin telah dipindahkan.</p>

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
