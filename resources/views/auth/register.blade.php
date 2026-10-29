@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <header class="auth-intro">
        <p class="auth-eyebrow">Mulai dari sini</p>
        <h1 class="auth-title">Buat akun mahasiswa</h1>
        <p class="auth-description">Daftarkan akun kampusmu untuk menyampaikan aspirasi.</p>
    </header>

    @if ($errors->any())
        <p class="auth-alert auth-alert--error" role="alert">Masih ada data yang perlu diperiksa. Lihat keterangan pada tiap kolom.</p>
    @endif

    <form class="auth-form" method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="name">Nama lengkap</label>
            <input class="auth-input" id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" autocomplete="name" maxlength="255" required>
            @error('name')
                <p class="auth-field-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-grid">
            <div class="auth-field">
                <label class="auth-label" for="nim">NIM</label>
                <input class="auth-input" id="nim" name="nim" type="text" value="{{ old('nim') }}" placeholder="Nomor induk mahasiswa" autocomplete="username" maxlength="32" required>
                @error('nim')
                    <p class="auth-field-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="email">Email kampus</label>
                <input class="auth-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@kampus.ac.id" autocomplete="email" maxlength="255" required>
                @error('email')
                    <p class="auth-field-error" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="auth-grid">
            <div class="auth-field">
                <label class="auth-label" for="password">Kata sandi</label>
                <input class="auth-input" id="password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8" required>
                @error('password')
                    <p class="auth-field-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="password_confirmation">Ulangi kata sandi</label>
                <input class="auth-input" id="password_confirmation" name="password_confirmation" type="password" placeholder="Masukkan kembali" autocomplete="new-password" minlength="8" required>
            </div>
        </div>

        <button class="auth-submit" type="submit">
            <span>Buat Akun</span>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </form>

    <div class="auth-divider"></div>
    <p class="auth-switch">Sudah punya akun? <a class="auth-link" href="{{ route('login') }}">Masuk</a></p>
@endsection