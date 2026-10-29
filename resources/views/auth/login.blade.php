@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <header class="auth-intro">
        <p class="auth-eyebrow">Selamat datang</p>
        <h1 class="auth-title">Masuk ke akunmu</h1>
        <p class="auth-description">Gunakan NIM atau email yang terdaftar untuk melanjutkan.</p>
    </header>

    @if (session('status'))
        <p class="auth-alert" role="status">{{ session('status') }}</p>
    @endif

    @if ($errors->any() && !$errors->has('identity'))
        <p class="auth-alert auth-alert--error" role="alert">Periksa kembali data yang kamu masukkan.</p>
    @endif

    <form class="auth-form" method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="identity">NIM atau Email</label>
            <input class="auth-input" id="identity" name="identity" type="text" value="{{ old('identity') }}" placeholder="Masukkan NIM atau email" autocomplete="username" required autofocus>
            @error('identity')
                <p class="auth-field-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">Kata sandi</label>
            <input class="auth-input" id="password" name="password" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
            @error('password')
                <p class="auth-field-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <a class="auth-link auth-forgot-link" href="{{ route('password.request') }}">Lupa kata sandi?</a>

        <label class="auth-check">
            <input type="checkbox" name="remember" value="1">
            <span>Ingat saya</span>
        </label>

        <button class="auth-submit" type="submit">
            <span>Masuk ke Portal</span>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </form>

    <div class="demo-divider"><span>Akun demo</span></div>
    <div class="demo-accounts">
        <div class="demo-account"><span class="demo-avatar">MP</span><span><strong>Mahasiswa</strong><small>NIM: 25170023 · Kata sandi: password</small></span></div>
        <div class="demo-account"><span class="demo-avatar demo-avatar--admin">AD</span><span><strong>Admin</strong><small>Email: admin@kampus.ac.id · Kata sandi: password</small></span></div>
    </div>

    <div class="auth-divider"></div>
    <p class="auth-switch">Belum punya akun? <a class="auth-link" href="{{ route('register') }}">Daftar sekarang</a></p>
@endsection