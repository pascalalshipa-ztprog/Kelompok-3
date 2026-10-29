@extends('layouts.app')

@section('title', 'Riwayat Pengaduan Mahasiswa')

@section('content')
<div class="page-heading row">
    <div>
        <span class="date-label">ARSIP ASPIRASI & LAPORAN</span>
        <h1>Riwayat Pengaduan</h1>
        <p>Pantau transparansi dan perkembangan penanganan dari seluruh laporan yang pernah kamu ajukan.</p>
    </div>
    <div class="header-action-buttons">
        <button class="button secondary export-button" id="export-button" type="button" title="Ekspor data pengaduan ke format CSV">
            <svg class="icon" width="18" height="18"><use href="#i-download"></use></svg>
            <span>Unduh CSV</span>
        </button>
        <a class="button primary" href="{{ route('pengaduan.create') }}">
            <svg class="icon" width="18" height="18"><use href="#i-plus"></use></svg>
            <span>Pengaduan Baru</span>
        </a>
    </div>
</div>

<section class="panel">
    <!-- Filter & Pencarian Cepat -->
    <div class="filter-bar">
        <label class="search-field" for="filter-search" style="margin: 0;">
            <svg class="icon" width="18" height="18"><use href="#i-search"></use></svg>
            <input id="filter-search" data-filter-search placeholder="Cari judul, tiket, atau lokasi...">
        </label>

        <div class="filter-selects-wrap">
            <div class="select-field">
                <span>Kategori:</span>
                <select id="filter-category" data-filter-category>
                    <option value="Semua">Semua Kategori</option>
                    <option value="Fasilitas dan Sarana Prasarana">Fasilitas & Sarpras</option>
                    <option value="Akademik">Akademik</option>
                    <option value="Administrasi dan Keuangan">Administrasi & Keuangan</option>
                    <option value="Layanan Digital & Fasilitas">Layanan Digital</option>
                </select>
            </div>

            <div class="select-field">
                <span>Status Tindak Lanjut:</span>
                <select id="filter-status" data-filter-status>
                    <option value="Semua">Semua Status</option>
                    <option value="Diproses">Sedang Diproses</option>
                    <option value="Selesai">Telah Selesai</option>
                    <option value="Pending">Menunggu Verifikasi</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Kontainer Tabel Interaktif -->
    <div data-table="history">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pengaduan & Tiket</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th aria-label="Aksi"></th>
                    </tr>
                </thead>
                <tbody id="history-table-body">
                    @forelse($reports ?? [] as $report)
                        <tr data-report-id="{{ $report['id'] }}" onclick="window.openDrawerModal('{{ $report['id'] }}')">
                            <td>
                                <strong>{{ $report['title'] }}</strong>
                                <small>{{ $report['id'] }}</small>
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
                                <button aria-label="Lihat detail tiket {{ $report['id'] }}" class="row-arrow" type="button">
                                    <svg class="icon" width="18" height="18"><use href="#i-chevron"></use></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <svg class="icon" width="22" height="22"><use href="#i-file"></use></svg>
                                    <strong>Belum ada riwayat pengaduan</strong>
                                    <span>Laporan yang kamu ajukan akan tersimpan rapi di halaman ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-footer" id="table-footer">
        Menampilkan {{ count($reports ?? []) }} dari {{ count($reports ?? []) }} pengaduan
    </div>
</section>
@endsection
