@extends('layouts.portal')

@section('title', 'Dashboard Admin')

@section('content')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Pusat kendali</p>
            <h1>Halo, {{ auth()->user()->name }}</h1>
            <p>Pantau dan tindak lanjuti aspirasi mahasiswa.</p>
        </div>
    </section>

    <section class="stats-grid" aria-label="Ringkasan pengaduan">
        <article class="stat-card"><span class="stat-icon stat-icon--green">▤</span><span class="stat-label">Total Pengaduan</span><strong>{{ $totalCount }}</strong><small>Semua laporan masuk</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon--amber">◷</span><span class="stat-label">Menunggu</span><strong>{{ $summary->get('Menunggu', 0) }}</strong><small>Menanti tindak lanjut</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon--blue">↻</span><span class="stat-label">Sedang Diproses</span><strong>{{ $summary->get('Diproses', 0) }}</strong><small>Dalam penanganan</small></article>
        <article class="stat-card"><span class="stat-icon stat-icon--mint">✓</span><span class="stat-label">Selesai</span><strong>{{ $summary->get('Selesai', 0) }}</strong><small>Sudah ditindaklanjuti</small></article>
    </section>

    <section class="analytics-grid" aria-label="Ringkasan semua pengguna dan pengaduan">
        <article class="panel analytics-card">
            <div class="analytics-heading">
                <h2>Total Pengguna</h2>
                <p>Komposisi semua akun berdasarkan peran.</p>
            </div>
            <div class="analytics-chart">
                <div class="pie-chart" style="--pie-gradient: {{ $userPieChart['gradient'] }}" role="img" aria-label="Grafik pie komposisi {{ $userPieChart['total'] }} akun"></div>
                <div class="analytics-legend">
                    <strong class="analytics-total">{{ number_format($userPieChart['total']) }} akun</strong>
                    <ul>
                        @foreach ($userPieChart['slices'] as $slice)
                            <li>
                                <span class="pie-legend-swatch" style="--legend-color: {{ $slice['color'] }}" aria-hidden="true"></span>
                                <span>{{ $slice['label'] }}</span>
                                <strong>{{ number_format($slice['value']) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </article>

        <article class="panel analytics-card">
            <div class="analytics-heading">
                <h2>Total Pengaduan</h2>
                <p>Komposisi semua pengaduan berdasarkan status.</p>
            </div>
            <div class="analytics-chart">
                <div class="pie-chart" style="--pie-gradient: {{ $complaintPieChart['gradient'] }}" role="img" aria-label="Grafik pie komposisi {{ $complaintPieChart['total'] }} pengaduan"></div>
                <div class="analytics-legend">
                    <strong class="analytics-total">{{ number_format($complaintPieChart['total']) }} pengaduan</strong>
                    <ul>
                        @foreach ($complaintPieChart['slices'] as $slice)
                            <li>
                                <span class="pie-legend-swatch" style="--legend-color: {{ $slice['color'] }}" aria-hidden="true"></span>
                                <span>{{ $slice['label'] }}</span>
                                <strong>{{ number_format($slice['value']) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </article>
    </section>
@endsection