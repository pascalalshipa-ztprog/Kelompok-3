@extends('layouts.portal')

@php($isEditing = isset($complaint))
@php($complaintsRoutePrefix = auth()->user()->isAdmin() ? 'admin.complaints' : 'complaints')

@section('title', $isEditing ? 'Edit Pengaduan' : 'Buat Pengaduan')

@section('content')
    <div class="complaint-form-page">
        <section class="page-heading">
            <div>
                <p class="eyebrow">Sampaikan aspirasi</p>
                <h1>{{ $isEditing ? 'Edit pengaduan' : 'Buat pengaduan baru' }}</h1>
                <p>Jelaskan kendala yang kamu alami agar dapat ditindaklanjuti.</p>
            </div>
            <a class="text-link" href="{{ $isEditing ? route($complaintsRoutePrefix.'.show', $complaint) : route('complaints.index') }}">← Kembali</a>
        </section>

        <form class="panel complaint-form complaint-form--page" method="POST" action="{{ $isEditing ? route($complaintsRoutePrefix.'.update', $complaint) : route('complaints.store') }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <div class="complaint-form-intro">
                <h2>Informasi pengaduan</h2>
                <p>Lengkapi detail berikut supaya laporan mudah dipahami.</p>
            </div>

            <label class="field {{ $errors->has('category_id') ? 'field--invalid' : '' }}">
                <span>Kategori</span>
                <select name="category_id" required aria-invalid="{{ $errors->has('category_id') ? 'true' : 'false' }}" @if ($errors->has('category_id')) aria-describedby="category-error" @endif>
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $complaint->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <small id="category-error" class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="field {{ $errors->has('title') ? 'field--invalid' : '' }}">
                <span>Judul pengaduan</span>
                <input name="title" value="{{ old('title', $complaint->title ?? '') }}" placeholder="Contoh: Lampu koridor gedung B mati" maxlength="140" required aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}" aria-describedby="title-hint{{ $errors->has('title') ? ' title-error' : '' }}">
                <small id="title-hint" class="field-hint">Tulis ringkasan masalah dalam maksimal 140 karakter.</small>
                @error('title')
                    <small id="title-error" class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="field {{ $errors->has('description') ? 'field--invalid' : '' }}">
                <span>Detail pengaduan</span>
                <textarea name="description" rows="6" placeholder="Sertakan lokasi, waktu, dan informasi yang membantu tindak lanjut." maxlength="3000" required aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}" aria-describedby="description-hint{{ $errors->has('description') ? ' description-error' : '' }}">{{ old('description', $complaint->description ?? '') }}</textarea>
                <small id="description-hint" class="field-hint">Hindari mencantumkan informasi pribadi yang tidak diperlukan.</small>
                @error('description')
                    <small id="description-error" class="field-error">{{ $message }}</small>
                @enderror
            </label>

            <label class="field {{ $errors->has('location') ? 'field--invalid' : '' }}">
                <span>Lokasi <small>(opsional)</small></span>
                <input name="location" value="{{ old('location', $complaint->location ?? '') }}" placeholder="Contoh: Gedung B, lantai 2" maxlength="180" aria-invalid="{{ $errors->has('location') ? 'true' : 'false' }}" @if ($errors->has('location')) aria-describedby="location-error" @endif>
                @error('location')
                    <small id="location-error" class="field-error">{{ $message }}</small>
                @enderror
            </label>

            @if ($isEditing && auth()->user()->isAdmin())
                <label class="field {{ $errors->has('status') ? 'field--invalid' : '' }}">
                    <span>Status</span>
                    <select name="status" required aria-invalid="{{ $errors->has('status') ? 'true' : 'false' }}" @if ($errors->has('status')) aria-describedby="status-error" @endif>
                        @foreach (['Menunggu', 'Diproses', 'Selesai', 'Ditolak'] as $status)
                            <option @selected(old('status', $complaint->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <small id="status-error" class="field-error">{{ $message }}</small>
                    @enderror
                </label>
            @endif

            <div class="form-actions">
                <p>Periksa kembali informasi sebelum menyimpan.</p>
                <button class="primary-button" type="submit">{{ $isEditing ? 'Simpan Perubahan' : 'Kirim Pengaduan' }} <span aria-hidden="true">→</span></button>
            </div>
        </form>
    </div>
@endsection
