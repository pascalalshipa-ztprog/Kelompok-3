@extends('layouts.portal')

@php($isEditing = isset($complaint))

@section('title', $isEditing ? 'Edit Pengaduan' : 'Buat Pengaduan')

@section('content')
    <section class="page-heading"><div><p class="eyebrow">Sampaikan aspirasi</p><h1>{{ $isEditing ? 'Edit pengaduan' : 'Buat pengaduan baru' }}</h1><p>Jelaskan kendala yang kamu alami agar dapat ditindaklanjuti.</p></div><a class="text-link" href="{{ $isEditing ? route('complaints.show', $complaint) : route('complaints.index') }}">← Kembali</a></section>
    <form class="panel complaint-form" method="POST" action="{{ $isEditing ? route('complaints.update', $complaint) : route('complaints.store') }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif
        <label class="field"><span>Kategori</span><select name="category_id" required><option value="">Pilih kategori</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $complaint->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label class="field"><span>Judul pengaduan</span><input name="title" value="{{ old('title', $complaint->title ?? '') }}" placeholder="Contoh: Lampu koridor gedung B mati" maxlength="140" required></label>
        <label class="field"><span>Ceritakan detail pengaduan</span><textarea name="description" rows="6" placeholder="Sertakan lokasi, waktu, dan informasi yang membantu tindak lanjut." maxlength="3000" required>{{ old('description', $complaint->description ?? '') }}</textarea></label>
        <label class="field"><span>Lokasi <small>(opsional)</small></span><input name="location" value="{{ old('location', $complaint->location ?? '') }}" placeholder="Contoh: Gedung B, lantai 2" maxlength="180"></label>
        @if ($isEditing && auth()->user()->isAdmin())<label class="field"><span>Status</span><select name="status" required>@foreach (['Menunggu', 'Diproses', 'Selesai', 'Ditolak'] as $status)<option @selected(old('status', $complaint->status) === $status)>{{ $status }}</option>@endforeach</select></label>@endif
        <div class="form-actions"><p>Periksa kembali informasi sebelum menyimpan.</p><button class="primary-button" type="submit">{{ $isEditing ? 'Simpan Perubahan' : 'Kirim Pengaduan' }} <span aria-hidden="true">→</span></button></div>
    </form>
@endsection