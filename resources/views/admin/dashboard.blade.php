@extends('layouts.app')

@section('title', 'Dashboard Administrator')

@section('content')
<section class="hero-row">
    <div>
        <span class="date-label">PORTAL ADMINISTRATOR & PETUGAS SARPRAS</span>
        <h1>Selamat Datang, Admin Kemahasiswaan.</h1>
        <p>Kelola, verifikasi, dan tindak lanjuti seluruh aspirasi serta laporan fasilitas kampus.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button class="button secondary export-button" id="export-button" type="button">
            <svg class="icon" width="18" height="18"><use href="#i-download"></use></svg>
            <span>Unduh Rekap CSV</span>
        </button>
    </div>
</section>

<!-- 4 Kartu Statistik Admin -->
<section class="stats-grid admin-stats">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg class="icon" width="22" height="22"><use href="#i-file"></use></svg>
        </div>
        <div class="stat-copy">
            <span>Total Masuk</span>
            <strong data-total>{{ $totalReports ?? 5 }}</strong>
            <small>Keseluruhan tiket</small>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon coral">
            <svg class="icon" width="22" height="22"><use href="#i-alert"></use></svg>
        </div>
        <div class="stat-copy">
            <span>Menunggu Verifikasi</span>
            <strong>{{ $pendingReports ?? 0 }}</strong>
            <small>Perlu ditinjau segera</small>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg class="icon" width="22" height="22"><use href="#i-clock"></use></svg>
        </div>
        <div class="stat-copy">
            <span>Dalam Pengerjaan</span>
            <strong data-processing>{{ $processReports ?? 2 }}</strong>
            <small>Sedang ditangani teknisi</small>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg class="icon" width="22" height="22"><use href="#i-check"></use></svg>
        </div>
        <div class="stat-copy">
            <span>Tuntas Selesai</span>
            <strong data-completed>{{ $doneReports ?? 3 }}</strong>
            <small>Berhasil diselesaikan</small>
        </div>
    </div>
</section>

<!-- Insight & Analitik Pengaduan -->
<section class="admin-insight-grid">
    <div class="panel chart-panel">
        <div class="panel-header">
            <div>
                <h2>Tren Laporan Masuk</h2>
                <p>Volume pengaduan mahasiswa per minggu</p>
            </div>
            <span class="trend-up">+14% dari bulan lalu</span>
        </div>
        <div class="bars">
            <span><i style="height: 38%;"></i><small>M-1</small></span>
            <span><i style="height: 52%;"></i><small>M-2</small></span>
            <span><i style="height: 45%;"></i><small>M-3</small></span>
            <span><i style="height: 68%;"></i><small>M-4</small></span>
            <span><i style="height: 85%;"></i><small>M-5</small></span>
            <span class="current"><i style="height: 94%;"></i><small>M-6 (Kini)</small></span>
        </div>
    </div>

    <div class="panel category-panel">
        <div class="panel-header">
            <div>
                <h2>Distribusi Berdasarkan Bidang</h2>
                <p>Kategori paling sering dilaporkan</p>
            </div>
        </div>
        <div class="category-row">
            <span>Sarpras</span>
            <div><i class="teal" style="width: 48%;"></i></div>
            <strong>48%</strong>
        </div>
        <div class="category-row">
            <span>Digital/TI</span>
            <div><i class="navy" style="width: 24%;"></i></div>
            <strong>24%</strong>
        </div>
        <div class="category-row">
            <span>Akademik</span>
            <div><i class="amber" style="width: 18%;"></i></div>
            <strong>18%</strong>
        </div>
        <div class="category-row">
            <span>Keuangan</span>
            <div><i class="coral" style="width: 10%;"></i></div>
            <strong>10%</strong>
        </div>
    </div>
</section>

<!-- Tabel Pengelolaan Laporan Mahasiswa -->
<section class="panel">
    <div class="panel-header admin-table-header">
        <div>
            <h2>Kelola Seluruh Pengaduan Mahasiswa</h2>
            <p>Klik baris pengaduan untuk membuka detail dan mengubah status tindak lanjut.</p>
        </div>
        <div class="inline-filters">
            <label class="search-field small-search" for="filter-search" style="margin: 0;">
                <svg class="icon" width="16" height="16"><use href="#i-search"></use></svg>
                <input id="filter-search" data-filter-search placeholder="Cari tiket/pelapor...">
            </label>
            <select data-filter-status>
                <option value="Semua">Semua Status</option>
                <option value="Pending">Pending</option>
                <option value="Diproses">Diproses</option>
                <option value="Selesai">Selesai</option>
            </select>
        </div>
    </div>

    <div data-table="history">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tiket & Judul</th>
                        <th>Pelapor</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th aria-label="Aksi"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports ?? [] as $report)
                        <tr data-report-id="{{ $report['id'] }}" onclick="window.openDrawerModal('{{ $report['id'] }}')">
                            <td>
                                <strong>{{ $report['title'] }}</strong>
                                <small>{{ $report['id'] }}</small>
                            </td>
                            <td>
                                <strong>{{ $report['reporter'] ?? 'Mahasiswa' }}</strong>
                            </td>
                            <td>{{ $report['category'] }}</td>
                            <td>{{ $report['location'] ?? 'Area Kampus' }}</td>
                            <td>{{ $report['date'] }}</td>
                            <td>
                                @php
                                    $statusClass = match(strtolower($report['status'])) {
                                        'selesai' => 'status-selesai',
                                        'diproses' => 'status-diproses',
                                        'ditolak' => 'status-ditolak',
                                        default => 'status-pending',
                                    };
                                @endphp
                                <span class="status {{ $statusClass }}">
                                    <span class="status-dot"></span>{{ $report['status'] }}
                                </span>
                            </td>
                            <td>
                                <button aria-label="Kelola tiket {{ $report['id'] }}" class="row-arrow" type="button">
                                    <svg class="icon" width="18" height="18"><use href="#i-chevron"></use></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="table-footer" id="table-footer">
        Menampilkan {{ count($reports ?? []) }} pengaduan mahasiswa
    </div>
</section>
@endsection

