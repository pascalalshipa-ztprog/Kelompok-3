@extends('layouts.app')

@section('title', 'Buat Pengaduan Baru')

@section('content')
<div class="narrow-page">
    <a class="button back-button" href="{{ route('dashboard') }}">
        <span class="back-arrow">←</span> Kembali ke Dashboard
    </a>

    <div class="page-heading">
        <span class="date-label">FORMULIR ASPIRASI RESMI</span>
        <h1>Sampaikan Aspirasimu</h1>
        <p>Pilih kategori yang sesuai, jelaskan kendala atau masukanmu secara rinci, dan lampirkan bukti pendukung.</p>
    </div>

    @if ($errors->any())
        <div class="alert error" id="server-errors" style="margin-bottom: 20px;">
            <strong>Mohon periksa kembali formulir:</strong>
            <ul style="margin: 6px 0 0 16px; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert error" id="complaint-error" hidden>
        <strong>Formulir belum lengkap:</strong>
        <p style="margin: 4px 0 0;">Pilih salah satu kategori pengaduan dan tuliskan deskripsi minimal 10 karakter.</p>
    </div>

    <form class="form-panel" id="complaint-form" enctype="multipart/form-data" method="POST" action="{{ route('pengaduan.store') }}">
        @csrf

        <!-- Langkah 01: Kategori & Informasi Pengaduan -->
        <div class="form-section-heading">
            <span>01</span>
            <div>
                <h2>Kategori Pengaduan</h2>
                <p>Tentukan kategori utama yang paling tepat menggambarkan permasalahanmu.</p>
            </div>
        </div>

        <fieldset class="complaint-type-field">
            <legend>Pilih Salah Satu Bidang:</legend>
            <div class="complaint-type-list">
                <label class="complaint-type-option selected" for="type-fasilitas">
                    <input type="radio" id="type-fasilitas" name="kategori" value="Fasilitas dan Sarana Prasarana" data-label="Fasilitas dan Sarana Prasarana" checked required>
                    <span class="complaint-type-number">01</span>
                    <span class="complaint-type-copy">
                        <strong>Fasilitas & Sarana Prasarana</strong>
                        <small>Kerusakan AC, proyektor, kelistrikan, kursi kelas, lift, atau toilet kampus</small>
                    </span>
                    <span class="complaint-type-check">
                        <svg class="icon" width="18" height="18"><use href="#i-check"></use></svg>
                    </span>
                </label>

                <label class="complaint-type-option" for="type-akademik">
                    <input type="radio" id="type-akademik" name="kategori" value="Akademik" data-label="Akademik">
                    <span class="complaint-type-number">02</span>
                    <span class="complaint-type-copy">
                        <strong>Akademik & Perkuliahan</strong>
                        <small>Masalah pengisian KRS, nilai KHS belum terbit, jadwal kuliah bentrok, dosen pengampu</small>
                    </span>
                    <span class="complaint-type-check">
                        <svg class="icon" width="18" height="18"><use href="#i-check"></use></svg>
                    </span>
                </label>

                <label class="complaint-type-option" for="type-administrasi">
                    <input type="radio" id="type-administrasi" name="kategori" value="Administrasi dan Keuangan" data-label="Administrasi dan Keuangan">
                    <span class="complaint-type-number">03</span>
                    <span class="complaint-type-copy">
                        <strong>Administrasi & Keuangan</strong>
                        <small>Pembayaran UKT, klaim beasiswa, legalisir surat keterangan, penagihan invoice</small>
                    </span>
                    <span class="complaint-type-check">
                        <svg class="icon" width="18" height="18"><use href="#i-check"></use></svg>
                    </span>
                </label>

                <label class="complaint-type-option" for="type-digital">
                    <input type="radio" id="type-digital" name="kategori" value="Layanan Digital & Fasilitas" data-label="Layanan Digital & Fasilitas">
                    <span class="complaint-type-number">04</span>
                    <span class="complaint-type-copy">
                        <strong>Layanan Digital & Jaringan Kampus</strong>
                        <small>Kendala portal SIAKAD, Wi-Fi perpustakaan lambat, e-learning, akun email kampus</small>
                    </span>
                    <span class="complaint-type-check">
                        <svg class="icon" width="18" height="18"><use href="#i-check"></use></svg>
                    </span>
                </label>
            </div>
        </fieldset>

        <!-- Informasi Tambahan -->
        <div class="form-row-2col">
            <div class="field" style="margin-bottom: 0;">
                <span>Judul Pengaduan <strong style="color: var(--coral);">*</strong></span>
                <input type="text" id="judul" name="judul" placeholder="Contoh: AC ruang kuliah B.301 tidak dingin" required value="{{ old('judul') }}">
                <small>Ringkas inti permasalahan dalam 1 kalimat.</small>
            </div>

            <div class="field" style="margin-bottom: 0;">
                <span>Lokasi / Ruangan Terkait</span>
                <input type="text" id="lokasi" name="lokasi" placeholder="Contoh: Gedung B Lantai 3, Ruang B.301" value="{{ old('lokasi') }}">
                <small>Memudahkan petugas langsung menuju tempat.</small>
            </div>
        </div>

        <!-- Langkah 02: Uraian Kritik / Masalah -->
        <div class="form-section-heading" style="margin-top: 32px;">
            <span>02</span>
            <div>
                <h2>Rincian Masalah & Bukti Pendukung</h2>
                <p>Tuliskan kronologi, waktu kejadian, serta lampirkan foto jika tersedia.</p>
            </div>
        </div>

        <div class="field">
            <span>Uraian Lengkap Pengaduan <strong style="color: var(--coral);">*</strong></span>
            <textarea id="description" name="description" maxlength="500" placeholder="Jelaskan secara detail masalah yang dihadapi, dampak yang dirasakan, dan harapan solusi..." rows="6" required>{{ old('description') }}</textarea>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                <small style="color: var(--ink-muted);">Minimal 10 karakter, maksimal 500 karakter.</small>
                <small id="character-count" style="font-weight: 600; color: var(--teal);">0/500 karakter</small>
            </div>
        </div>

        <!-- Upload File Interaktif -->
        <div class="field">
            <span>Lampiran Bukti (Opsional)</span>
            <label class="upload-area" id="upload-dropzone" for="attachment">
                <input accept="image/jpeg,image/png,image/webp,application/pdf" id="attachment" name="attachment" type="file">
                <span class="upload-icon">
                    <svg class="icon" width="22" height="22"><use href="#i-upload"></use></svg>
                </span>
                <strong>Klik untuk memilih foto / dokumen bukti</strong>
                <span>Mendukung JPG, PNG, atau PDF • Ukuran maksimum 5 MB</span>
            </label>

            <!-- Pratinjau File Terpilih -->
            <div id="file-preview-card" class="file-preview-card" hidden>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="file-icon-box">
                        <svg class="icon" width="20" height="20"><use href="#i-paperclip"></use></svg>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <strong id="preview-file-name" style="font-size: 12px; color: var(--ink);">nama_file.jpg</strong>
                        <small id="preview-file-size" style="color: var(--ink-muted); font-size: 10px;">1.2 MB</small>
                    </div>
                </div>
                <button type="button" id="remove-file-button" class="button icon-button" title="Hapus lampiran" style="color: var(--coral);">
                    <svg class="icon" width="18" height="18"><use href="#i-close"></use></svg>
                </button>
            </div>
        </div>

        <!-- Catatan Privasi & Keamanan Mahasiswa -->
        <div class="privacy-note" style="margin-top: 24px;">
            <svg class="icon" width="20" height="20" style="flex-shrink: 0;"><use href="#i-shield"></use></svg>
            <div>
                <strong>Jaminan Kerahasiaan Identitas:</strong>
                Laporanmu dilindungi dan hanya akan ditindaklanjuti secara profesional oleh Biro Kemahasiswaan dan Unit Sarana Terkait.
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="form-actions">
            <a class="button secondary" href="{{ route('dashboard') }}">Batal</a>
            <button class="button primary" id="submit-button" type="submit">
                <svg class="icon" width="18" height="18"><use href="#i-plus"></use></svg>
                <span>Kirim Pengaduan</span>
            </button>
        </div>
    </form>
</div>
@endsection
