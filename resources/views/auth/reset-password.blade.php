@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')

@section('content')
    <header class="auth-intro">
        <p class="auth-eyebrow">Pemulihan akun</p>
        <h1 class="auth-title">Buat kata sandi baru</h1>
        <p class="auth-description">Gunakan kata sandi baru minimal 8 karakter untuk akunmu.</p>
    </header>

    @if ($errors->any())
        <p class="auth-alert auth-alert--error" role="alert">{{ $errors->first() }}</p>
    @endif

    <form class="auth-form" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="auth-field">
            <label class="auth-label" for="email">Email terdaftar</label>
            <input class="auth-input" id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus>
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">Kata sandi baru</label>
            <input class="auth-input" id="password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8" required>
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password_confirmation">Ulangi kata sandi baru</label>
            <input class="auth-input" id="password_confirmation" name="password_confirmation" type="password" placeholder="Masukkan kembali" autocomplete="new-password" minlength="8" required>
        </div>

        <button class="auth-submit" type="submit">Simpan kata sandi baru</button>
    </form>

    <div class="auth-divider"></div>
    <p class="auth-switch"><a class="auth-link" href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
@endsection
