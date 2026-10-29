@extends('layouts.portal')

@section('title', auth()->user()->isAdmin() ? 'Semua Pengaduan' : 'Riwayat Pengaduan')

@section('content')
    <section class="page-heading">
        <div><p class="eyebrow">{{ auth()->user()->isAdmin() ? 'Pusat kendali' : 'Ruang aspirasi' }}</p><h1>{{ auth()->user()->isAdmin() ? 'Semua pengaduan' : 'Riwayat pengaduan' }}</h1><p>Cari dan tinjau laporan berdasarkan judul, isi, kategori, atau pelapor.</p></div>
        @if (auth()->user()->isAdmin())
            <a class="primary-button primary-button--link" href="{{ route('complaints.export') }}">Unduh laporan CSV</a>
        @else
            <a class="primary-button primary-button--link" href="{{ route('complaints.create') }}"><span aria-hidden="true">＋</span> Buat Pengaduan</a>
        @endif
    </section>

    <section class="panel table-panel">
        <form class="search-bar" method="GET" action="{{ route('complaints.index') }}">
            <label class="sr-only" for="search">Cari pengaduan</label>
            <input id="search" name="search" value="{{ $search }}" placeholder="Cari judul, isi, kategori, atau pelapor">
            <button type="submit" class="primary-button primary-button--link">Cari</button>
            @if ($search !== '')<a class="text-link" href="{{ route('complaints.index') }}">Reset</a>@endif
        </form>
        @if ($complaints->isEmpty())
            <div class="empty-state"><span class="empty-icon">▤</span><h3>Tidak ada pengaduan ditemukan</h3><p>Coba ubah kata pencarian atau buat pengaduan baru.</p></div>
        @else
            <div class="table-scroll"><table><thead><tr><th>ID Pengaduan</th>@if (auth()->user()->isAdmin())<th>Pelapor</th>@endif<th>Subjek</th><th>Tanggal</th><th>Status</th>@if (auth()->user()->isAdmin())<th>Perbarui</th>@endif<th>Aksi</th></tr></thead><tbody>
                @foreach ($complaints as $complaint)
                    <tr>
                        <td class="ticket-id">SK-{{ str_pad((string) $complaint->id, 6, '0', STR_PAD_LEFT) }}<small>{{ $complaint->category->name }}</small></td>
                        @if (auth()->user()->isAdmin())<td>{{ $complaint->user?->name ?? 'Akun dihapus' }}<small class="cell-subtext">{{ $complaint->user?->nim }}</small></td>@endif
                        <td class="subject-cell"><a href="{{ route('complaints.show', $complaint) }}"><strong>{{ $complaint->title }}</strong></a><small>{{ Str::limit($complaint->description, 90) }}</small></td>
                        <td>{{ $complaint->created_at->format('d M Y') }}</td>
                        <td><span class="status status--{{ $complaint->status === 'Selesai' ? 'done' : ($complaint->status === 'Diproses' ? 'progress' : 'waiting') }}">{{ $complaint->status }}</span></td>
                        @if (auth()->user()->isAdmin())<td>@include('complaints.partials.inline-status', ['complaint' => $complaint])</td>@endif
                        <td class="row-actions"><a class="text-link" href="{{ route('complaints.show', $complaint) }}">Detail</a>@can('update', $complaint)<a class="text-link" href="{{ route('complaints.edit', $complaint) }}">Edit</a>@endcan</td>
                    </tr>
                @endforeach
            </tbody></table></div>
            <div class="pagination"><span>Menampilkan {{ $complaints->firstItem() }}–{{ $complaints->lastItem() }} dari {{ $complaints->total() }} pengaduan</span><div>@if ($complaints->previousPageUrl())<a href="{{ $complaints->previousPageUrl() }}">← Sebelumnya</a>@endif<span>Halaman {{ $complaints->currentPage() }} dari {{ $complaints->lastPage() }}</span>@if ($complaints->nextPageUrl())<a href="{{ $complaints->nextPageUrl() }}">Berikutnya →</a>@endif</div></div>
        @endif
    </section>
@endsection