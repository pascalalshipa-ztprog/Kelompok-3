@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <header class="auth-intro">
        <p class="auth-eyebrow">Pemulihan akun</p>
        <h1 class="auth-title">Lupa kata sandi?</h1>
        <p class="auth-description">Masukkan email terdaftar. Jika cocok, kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>
    </header>

    @if (session('status'))
        <p class="auth-alert" role="status">{{ session('status') }}</p>
    @endif

    @error('email')
        <p class="auth-alert auth-alert--error" role="alert">{{ $message }}</p>
    @enderror

    <form class="auth-form" method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="email">Email terdaftar</label>
            <input class="auth-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@kampus.ac.id" autocomplete="email" required autofocus>
        </div>

        <button class="auth-submit" type="submit">Kirim tautan reset</button>
    </form>

    <div class="auth-divider"></div>
    <p class="auth-switch"><a class="auth-link" href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
@endsection
