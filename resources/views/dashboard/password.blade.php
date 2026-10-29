@extends('layouts.portal')

@section('title', 'Ubah Kata Sandi')

@section('content')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Keamanan Akun</p>
            <h1>Ubah kata sandi</h1>
            <p>Perbarui kata sandi untuk menjaga keamanan akun Anda.</p>
        </div>
    </section>

    <section class="panel password-panel">
        <div class="panel-heading">
            <div>
                <h2>Ubah kata sandi</h2>
                <p>Konfirmasi kata sandi saat ini sebelum menyimpan yang baru.</p>
            </div>
        </div>
        <form class="complaint-form password-form" method="POST" action="{{ route('dashboard.password.update') }}">
            @csrf
            @method('PUT')
            <label class="field" for="current_password">
                Kata sandi saat ini
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
            </label>
            <label class="field" for="password">
                Kata sandi baru
                <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
            </label>
            <label class="field" for="password_confirmation">
                Ulangi kata sandi baru
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
            </label>
            <div class="form-actions">
                <p>Gunakan minimal 8 karakter.</p>
                <button class="primary-button" type="submit">Simpan kata sandi</button>
            </div>
        </form>
    </section>
@endsection
