@extends('layouts.portal')

@section('title', 'Profil Saya')

@section('content')
    <div class="profile-page">
        <section class="page-heading">
            <div>
                <p class="eyebrow">Akun Saya</p>
                <h1>Profil Saya</h1>
                <p>Kelola informasi akun dan foto profil Anda.</p>
            </div>
        </section>

        <div class="profile-layout">
            <section class="panel profile-card">
                <div class="profile-hero">
                    <div class="profile-photo-preview">
                        <img
                            id="profile-photo-preview"
                            src="{{ $user->profile_photo_path ? route('profile.photo') : '' }}"
                            alt="Foto profil {{ $user->name }}"
                            @if (! $user->profile_photo_path) hidden @endif
                        >
                        <span id="profile-photo-fallback" @if ($user->profile_photo_path) hidden @endif>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="profile-hero-info">
                        <span class="user-role {{ $user->isAdmin() ? 'user-role--admin' : '' }}">{{ $user->isAdmin() ? 'Administrator' : 'Mahasiswa' }}</span>
                        <h2>{{ $user->name }}</h2>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>

                <form class="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="profile-photo-field">
                        <div>
                            <strong>Foto profil</strong>
                            <p>JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.</p>
                        </div>
                        <label class="profile-upload-button" for="profile_photo">
                            Pilih foto
                            <input id="profile_photo" class="profile-photo-input" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp">
                        </label>
                    </div>
                    @error('profile_photo')
                        <p class="profile-field-error" role="alert">{{ $message }}</p>
                    @enderror

                    <div class="profile-form-fields">
                        @if ($user->isAdmin())
                            <label class="field" for="name">
                                Nama lengkap
                                <input id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="255" autocomplete="name" required>
                            </label>
                        @else
                            <label class="field" for="name">
                                Nama lengkap
                                <input id="name" value="{{ $user->name }}" maxlength="255" autocomplete="name" readonly>
                            </label>
                            <label class="field" for="nim">
                                NIM
                                <input id="nim" value="{{ $user->nim }}" maxlength="32" autocomplete="username" readonly>
                            </label>
                        @endif

                        <label class="field" for="email">
                            Email
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" maxlength="255" autocomplete="email" required>
                        </label>
                    </div>

                    <div class="profile-form-actions">
                        <p>Perubahan akan tersimpan pada akun Anda.</p>
                        <button class="primary-button" type="submit">Simpan Profil</button>
                    </div>
                </form>
            </section>

            <aside class="panel profile-details">
                <h2>Informasi akun</h2>
                <dl>
                    <div>
                        <dt>Jenis akun</dt>
                        <dd>{{ $user->isAdmin() ? 'Administrator' : 'Mahasiswa' }}</dd>
                    </div>
                    <div>
                        <dt>Bergabung sejak</dt>
                        <dd>{{ $user->created_at->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt>Kata sandi</dt>
                        <dd>••••••••</dd>
                    </div>
                </dl>
            </aside>
        </div>
    </div>

@endsection
