@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<section class="hero-row">
    <div>
        <span class="date-label">PORTAL MAHASISWA • SEMESTER GENAP 2025/2026</span>
        <h1>Selamat datang kembali, Naufal.</h1>
        <p>Pantau aspirasi, kritik, dan laporan sarana kampusmu secara transparan dalam satu tempat.</p>
    </div>
    <a class="button primary" href="{{ route('pengaduan.create') }}">
        <svg class="icon" width="18" height="18"><use href="#i-plus"></use></svg> Sampaikan Aspirasi
    </a>
</section>

<!-- Ringkasan Statistik Pengaduan -->
<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg class="icon" width="22" height="22"><use href="#i-file"></use></svg>
        </div>
        <div class="stat-copy">
            <span>Total Pengaduan</span>
            <strong data-total>{{ $totalReports ?? 5 }}</strong>
            <small>Sepanjang semester aktif</small>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg class="icon" width="22" height="22"><use href="#i-clock"></use></svg>
        </div>
        <div class="stat-copy">
            <span>Sedang Diproses</span>
            <strong data-processing>{{ $processReports ?? 2 }}</strong>
            <small>Tim sarpras & biro menindaklanjuti</small>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg class="icon" width="22" height="22"><use href="#i-check"></use></svg>
        </div>
        <div class="stat-copy">
            <span>Telah Selesai</span>
            <strong data-completed>{{ $doneReports ?? 3 }}</strong>
            <small>Aspirasi berhasil terselesaikan</small>
        </div>
    </div>
</section>

<!-- Konten Utama: Daftar Terbaru & Kartu Status -->
<section class="content-grid">
    <!-- Panel Tabel Pengaduan Terkini -->
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2>Pengaduan Terbaru</h2>
                <p>Klik salah satu baris untuk memeriksa status tindak lanjut petugas.</p>
            </div>
            <a class="button secondary outline-action-btn" href="{{ route('pengaduan.riwayat') }}">
                <span>Buka Riwayat Lengkap</span>
                <svg class="icon" width="16" height="16"><use href="#i-chevron"></use></svg>
            </a>
        </div>

        <!-- Wadah Tabel Interaktif Compact (dirender oleh JS atau fallback Blade) -->
        <div data-table="compact">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Pengaduan</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th aria-label="Aksi"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(array_slice($reports ?? [], 0, 4) as $item)
                            <tr data-report-id="{{ $item['id'] }}" onclick="window.openDrawerModal('{{ $item['id'] }}')">
                                <td>
                                    <strong>{{ $item['title'] }}</strong>
                                    <small>{{ $item['id'] }} • {{ $item['location'] ?? 'Kampus' }}</small>
                                </td>
                                <td>{{ $item['category'] }}</td>
                                <td>{{ $item['date'] }}</td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($item['status'])) {
                                            'selesai' => 'status-selesai',
                                            'diproses' => 'status-diproses',
                                            'ditolak' => 'status-ditolak',
                                            default => 'status-pending',
                                        };
                                    @endphp
                                    <span class="status {{ $statusClass }}">
                                        <span class="status-dot"></span>{{ $item['status'] }}
                                    </span>
                                </td>
                                <td>
                                    <button aria-label="Lihat detail tiket {{ $item['id'] }}" class="row-arrow" type="button">
                                        <svg class="icon" width="18" height="18"><use href="#i-chevron"></use></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <svg class="icon" width="22" height="22"><use href="#i-file"></use></svg>
                                        <strong>Belum ada pengaduan aktif</strong>
                                        <span>Gunakan tombol "Sampaikan Aspirasi" untuk membuat laporan baru.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Samping (Side Stack) -->
    <div class="side-stack">
        <!-- Kartu Progress Pengaduan Aktif -->
        <div class="progress-card">
            <div class="progress-top">
                <span class="progress-icon">
                    <svg class="icon" width="20" height="20"><use href="#i-clock"></use></svg>
                </span>
                <span class="live-badge">MONITORING AKTIF</span>
            </div>
            <h3>AC Gedung B Rusak</h3>
            <p>Tiket <strong>PGD-2025-084</strong> telah diverifikasi dan tim teknisi sedang melakukan penggantian sparepart pada ruang B.301.</p>
            <div class="progress-track" title="Progres pengerjaan 65%">
                <span style="width: 65%;"></span>
            </div>
            <div class="progress-labels">
                <span>Verifikasi Berhasil</span>
                <strong>65% Selesai</strong>
            </div>
            <button class="button light-button" type="button" onclick="window.openDrawerModal('PGD-2025-084')">
                Periksa Detail Penanganan
            </button>
        </div>

        <!-- Kartu Tips Pengaduan Efektif -->
        <div class="tip-card">
            <span class="tip-icon">
                <svg class="icon" width="20" height="20"><use href="#i-info"></use></svg>
            </span>
            <div>
                <h3>Tips Laporan Cepat Direspons</h3>
                <p>Cantumkan kode ruangan / lokasi detail serta lampirkan foto agar penanggung jawab unit sarpras dapat langsung bertindak.</p>
                <a class="button secondary" href="{{ route('pengaduan.create') }}" style="margin-top: 8px; font-size: 11px;">
                    Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
