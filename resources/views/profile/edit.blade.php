@extends('layouts.portal')

@section('title', 'Profil Saya')

@section('content')
    @php($profileRoutePrefix = $user->isAdmin() ? 'admin.profile' : 'profile')
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
                            src="{{ $user->profile_photo_path ? route($profileRoutePrefix.'.photo') : '' }}"
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

                <form class="profile-form" method="POST" action="{{ route($profileRoutePrefix.'.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="profile-photo-field">
                        <div>
                            <strong>Foto profil</strong>
                            <p>JPG, PNG, atau WEBP. Ukuran maksimal 500 KB.</p>
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
                        <div class="profile-readonly-field">
                            <span>Nama lengkap</span>
                            <strong>{{ $user->name }}</strong>
                        </div>
                        @if (! $user->isAdmin())
                            <div class="profile-readonly-field">
                                <span>NIM</span>
                                <strong>{{ $user->nim }}</strong>
                            </div>
                        @endif
                        <div class="profile-readonly-field">
                            <span>Email</span>
                            <strong>{{ $user->email }}</strong>
                        </div>
                    </div>

                    <div class="profile-form-actions">
                        <p>Pilih foto baru untuk memperbarui foto profil.</p>
                        <button class="primary-button" type="submit">Simpan Foto Profil</button>
                    </div>
                </form>
            </section>

            <div class="profile-sidebar">
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
                    </dl>
                    <a class="profile-password-link" href="#ubah-kata-sandi">
                        <span>Ubah kata sandi</span><span aria-hidden="true">→</span>
                    </a>
                </aside>

                <section class="panel password-panel" id="ubah-kata-sandi">
                    <div class="panel-heading">
                        <div>
                            <h2>Ubah kata sandi</h2>
                            <p>Konfirmasi kata sandi saat ini sebelum menyimpan yang baru.</p>
                        </div>
                    </div>
                    <form class="complaint-form password-form" method="POST" action="{{ route('profile.password.update') }}">
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
            </div>
        </div>
    </div>

@endsection
