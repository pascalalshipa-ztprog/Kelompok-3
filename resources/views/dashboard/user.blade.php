@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Ruang aspirasi</p>
            <h1>Halo, {{ auth()->user()->name }}</h1>
            <p>Pantau perkembangan laporan dan sampaikan aspirasi kampusmu.</p>
        </div>
        <a class="primary-button primary-button--link" href="{{ route('complaints.create') }}">
            <span aria-hidden="true">＋</span> Buat Pengaduan
        </a>
    </section>

    <section class="stats-grid" aria-label="Ringkasan pengaduan">
        <article class="stat-card"><span class="stat-icon stat-icon--green">▤</span><span class="stat-label">Total Pengaduan</span><strong>{{ $totalCount }}</strong><small>Laporan yang kamu kirim</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon--amber">◷</span><span class="stat-label">Menunggu</span><strong>{{ $summary->get('Menunggu', 0) }}</strong><small>Menanti tindak lanjut</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon--blue">↻</span><span class="stat-label">Sedang Diproses</span><strong>{{ $summary->get('Diproses', 0) }}</strong><small>Dalam penanganan</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon--mint">✓</span><span class="stat-label">Selesai</span><strong>{{ $summary->get('Selesai', 0) }}</strong><small>Sudah ditindaklanjuti</small></article>
    </section>

    <section class="panel table-panel">
        <div class="panel-heading">
            <div>
                <h2>Pengaduan terbaru</h2>
                <p>Perkembangan laporan yang terhubung dengan akunmu.</p>
            </div>
            <a class="text-link" href="{{ route('complaints.index') }}">Lihat semua <span aria-hidden="true">→</span></a>
        </div>

        @if ($recentComplaints->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">▤</span>
                <h3>Belum ada pengaduan</h3>
                <p>Pengaduan yang kamu kirim akan muncul di sini.</p>
                <a href="{{ route('complaints.create') }}" class="text-link">Buat pengaduan pertama →</a>
            </div>
        @else
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID Pengaduan</th>
                            <th>Subjek</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentComplaints as $complaint)
                            <tr>
                                <td class="ticket-id">
                                    <a href="{{ route('complaints.show', $complaint) }}">SK-{{ str_pad((string) $complaint->id, 6, '0', STR_PAD_LEFT) }}</a>
                                    <small>{{ $complaint->category->name }}</small>
                                </td>
                                <td class="subject-cell">
                                    <a href="{{ route('complaints.show', $complaint) }}"><strong>{{ $complaint->title }}</strong></a>
                                    <small>{{ Str::limit($complaint->description, 90) }}</small>
                                </td>
                                <td>{{ $complaint->created_at->format('d M Y') }}</td>
                                <td>
                                    <span class="status status--{{ $complaint->status === 'Selesai' ? 'done' : ($complaint->status === 'Diproses' ? 'progress' : 'waiting') }}">
                                        {{ $complaint->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection