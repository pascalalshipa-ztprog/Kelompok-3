<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengaduan SuaraKampus</title>
    <style>
        @page {
            margin: 28px 32px;
        }

        body {
            color: #20332f;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        h1, h2, h3, p {
            margin-top: 0;
        }

        h1 {
            color: #116f5d;
            font-size: 23px;
            margin-bottom: 5px;
        }

        h2 {
            border-bottom: 1px solid #dce8e3;
            color: #116f5d;
            font-size: 15px;
            margin-bottom: 8px;
            padding-bottom: 5px;
        }

        p {
            line-height: 1.5;
        }

        .subtitle, .muted {
            color: #62736e;
        }

        .summary {
            border-collapse: separate;
            border-spacing: 6px;
            margin: 12px -6px 16px;
            width: 100%;
        }

        .summary td {
            background: #eef6f2;
            border: 1px solid #dce8e3;
            padding: 9px;
            width: 20%;
        }

        .summary strong {
            color: #116f5d;
            display: block;
            font-size: 18px;
            margin-bottom: 3px;
        }

        .section {
            margin-top: 17px;
        }

        .chart {
            border-collapse: collapse;
            width: 100%;
        }

        .chart td {
            padding: 5px 4px;
            vertical-align: middle;
        }

        .chart-label {
            width: 85px;
        }

        .chart-track {
            background: #edf2ef;
            height: 13px;
            width: 100%;
        }

        .chart-bar {
            height: 13px;
        }

        .chart-value {
            text-align: right;
            width: 65px;
        }

        .waiting { background: #d69a2d; }
        .progress { background: #4686b4; }
        .done { background: #16856d; }
        .rejected { background: #c85c54; }

        .flow {
            border-collapse: collapse;
            margin-top: 10px;
            width: 100%;
        }

        .flow td {
            text-align: center;
            vertical-align: middle;
        }

        .flow-step {
            background: #eef6f2;
            border: 1px solid #cfe0d8;
            color: #20332f;
            font-weight: bold;
            height: 48px;
            padding: 5px;
            width: 18%;
        }

        .flow-arrow {
            color: #16856d;
            font-size: 18px;
            font-weight: bold;
            width: 5%;
        }

        .report-table {
            border-collapse: collapse;
            margin-top: 9px;
            width: 100%;
        }

        .report-table th {
            background: #116f5d;
            color: #fff;
            text-align: left;
        }

        .report-table th, .report-table td {
            border: 1px solid #dce5e1;
            padding: 5px;
            vertical-align: top;
        }

        .report-table tr {
            page-break-inside: avoid;
        }

        .ticket {
            white-space: nowrap;
            width: 72px;
        }

        .status {
            white-space: nowrap;
            width: 62px;
        }

        .date {
            white-space: nowrap;
            width: 68px;
        }

        .footer {
            border-top: 1px solid #dce8e3;
            color: #62736e;
            font-size: 8px;
            margin-top: 16px;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Laporan Pengaduan SuaraKampus</h1>
        <p class="subtitle">Ringkasan pengaduan mahasiswa dan status tindak lanjut.</p>
        <p class="muted">Dibuat pada {{ $generatedAt->format('d/m/Y H:i') }}</p>
    </header>

    <table class="summary">
        <tr>
            <td><strong>{{ number_format($totalCount) }}</strong>Total pengaduan</td>
            <td><strong>{{ number_format($statusCounts['Menunggu']) }}</strong>Menunggu</td>
            <td><strong>{{ number_format($statusCounts['Diproses']) }}</strong>Diproses</td>
            <td><strong>{{ number_format($statusCounts['Selesai']) }}</strong>Selesai</td>
            <td><strong>{{ number_format($statusCounts['Ditolak']) }}</strong>Ditolak</td>
        </tr>
    </table>

    <section>
        <h2>Penjelasan ringkasan</h2>
        <p>
            Laporan ini merangkum {{ number_format($totalCount) }} pengaduan yang tercatat di SuaraKampus.
            Status <strong>Menunggu</strong> berarti laporan belum mulai ditangani, <strong>Diproses</strong>
            berarti sedang ditindaklanjuti, <strong>Selesai</strong> berarti penanganan telah ditutup,
            sedangkan <strong>Ditolak</strong> berarti laporan tidak dapat ditindaklanjuti.
            Ringkasan status dihitung dari seluruh laporan pada saat dokumen dibuat.
        </p>
    </section>

    <section class="section">
        <h2>Grafik pengaduan berdasarkan status</h2>
        <p class="muted">Jumlah laporan pada setiap status; panjang batang menunjukkan proporsinya terhadap jumlah terbanyak.</p>
        <table class="chart">
            @foreach ([
                ['label' => 'Menunggu', 'key' => 'Menunggu', 'class' => 'waiting'],
                ['label' => 'Diproses', 'key' => 'Diproses', 'class' => 'progress'],
                ['label' => 'Selesai', 'key' => 'Selesai', 'class' => 'done'],
                ['label' => 'Ditolak', 'key' => 'Ditolak', 'class' => 'rejected'],
            ] as $chartItem)
                @php($maxCount = max($statusCounts))
                @php($barWidth = $maxCount > 0 ? ($statusCounts[$chartItem['key']] / $maxCount * 100) : 0)
                <tr>
                    <td class="chart-label">{{ $chartItem['label'] }}</td>
                    <td>
                        <table class="chart-track">
                            <tr><td class="chart-bar {{ $chartItem['class'] }}" style="width: {{ $barWidth }}%"></td></tr>
                        </table>
                    </td>
                    <td class="chart-value">{{ number_format($statusCounts[$chartItem['key']]) }} laporan</td>
                </tr>
            @endforeach
        </table>
    </section>

    <section class="section">
        <h2>Diagram alur penanganan</h2>
        <p class="muted">Mahasiswa mengirim laporan, admin meninjau, lalu status diperbarui hingga laporan selesai atau ditolak.</p>
        <table class="flow">
            <tr>
                <td class="flow-step">Mahasiswa<br>Kirim pengaduan</td>
                <td class="flow-arrow">&gt;</td>
                <td class="flow-step">Menunggu<br>Masuk antrean</td>
                <td class="flow-arrow">&gt;</td>
                <td class="flow-step">Admin<br>Meninjau laporan</td>
                <td class="flow-arrow">&gt;</td>
                <td class="flow-step">Diproses<br>Tindak lanjut</td>
                <td class="flow-arrow">&gt;</td>
                <td class="flow-step">Selesai / Ditolak<br>Status akhir</td>
            </tr>
        </table>
    </section>

    <section class="section">
        <h2>Daftar pengaduan</h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th class="ticket">Nomor</th>
                    <th>Pelapor / NIM</th>
                    <th>Pengaduan</th>
                    <th class="status">Status</th>
                    <th class="date">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($complaints as $complaint)
                    <tr>
                        <td class="ticket">SK-{{ str_pad((string) $complaint->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            {{ $complaint->user?->name ?? 'Akun dihapus' }}<br>
                            <span class="muted">{{ $complaint->user?->nim ?? '-' }}</span>
                        </td>
                        <td>
                            <strong>{{ $complaint->title }}</strong><br>
                            <span class="muted">{{ $complaint->category->name }} · {{ $complaint->location ?: 'Lokasi tidak dicantumkan' }}</span><br>
                            {{ $complaint->description }}
                        </td>
                        <td class="status">{{ $complaint->status }}</td>
                        <td class="date">{{ $complaint->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Belum ada pengaduan yang tercatat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <p class="footer">Dokumen ini dibuat otomatis oleh SuaraKampus dan memuat data sesuai keadaan pada waktu pembuatan.</p>
</body>
</html>
