@extends('layouts.portal')

@section('title', 'Ubah Pengguna')

@section('content')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Manajemen pengguna</p>
            <h1>Ubah data pengguna</h1>
            <p>Perbarui informasi akun {{ $user->name }}.</p>
        </div>
        <a class="text-link" href="{{ route('users.index') }}">← Kembali ke daftar pengguna</a>
    </section>

    <form class="panel user-edit-form" method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="user-edit-grid">
            <label class="field" for="name">
                Nama lengkap
                <input id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="255" autocomplete="name" required>
            </label>

            <label class="field" for="nim">
                NIM
                <input id="nim" name="nim" value="{{ old('nim', $user->nim) }}" maxlength="32" autocomplete="username">
            </label>

            <label class="field" for="email">
                Email
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" maxlength="255" autocomplete="email" required>
            </label>

            <label class="field" for="role">
                Peran
                <input id="role" value="{{ $user->isAdmin() ? 'Administrator' : 'Mahasiswa' }}" disabled>
                <small>Perubahan peran tidak tersedia di halaman ini.</small>
            </label>
        </div>

        <div class="form-actions">
            <p>Kata sandi dan peran akun tidak diubah.</p>
            <button class="primary-button" type="submit">Simpan perubahan</button>
        </div>
    </form>
@endsection
