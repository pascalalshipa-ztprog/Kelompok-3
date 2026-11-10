@extends('layouts.portal')

@section('title', auth()->user()->isAdmin() ? 'Semua Pengaduan' : 'Riwayat Pengaduan')

@section('content')
    @php($complaintsRoutePrefix = auth()->user()->isAdmin() ? 'admin.complaints' : 'complaints')

    <div class="complaints-page {{ auth()->user()->isAdmin() ? 'complaints-page--admin' : '' }}" data-complaints-page>
        <section class="page-heading">
            <div>
                <p class="eyebrow">{{ auth()->user()->isAdmin() ? 'Pusat kendali' : 'Ruang aspirasi' }}</p>
                <h1>{{ auth()->user()->isAdmin() ? 'Semua pengaduan' : 'Riwayat pengaduan' }}</h1>
                <p>{{ auth()->user()->isAdmin() ? 'Cari dan tinjau laporan berdasarkan judul, isi, kategori, atau pelapor.' : 'Cari dan tinjau laporan berdasarkan judul, isi, atau kategori.' }}</p>
            </div>
            @if (auth()->user()->isAdmin())
                <a class="primary-button primary-button--link" href="{{ route('admin.complaints.export') }}">Unduh laporan CSV</a>
            @else
                <a class="primary-button primary-button--link" href="{{ route('complaints.create') }}"><span aria-hidden="true">＋</span> Buat Pengaduan</a>
            @endif
        </section>

        <section class="panel table-panel">
            <div class="complaints-toolbar">
                <div class="complaints-toolbar-copy">
                    <h2>Daftar pengaduan</h2>
                    <p data-complaint-count data-default-count="{{ $complaints->total() }} laporan tercatat">{{ $complaints->total() }} laporan tercatat</p>
                </div>
                <form class="search-bar" method="GET" action="{{ route($complaintsRoutePrefix.'.index') }}">
                    <label class="sr-only" for="search">Cari pengaduan</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" placeholder="{{ auth()->user()->isAdmin() ? 'Cari judul, isi, atau pelapor' : 'Cari judul atau isi pengaduan' }}">
                    <label class="sr-only" for="category-filter">Filter berdasarkan kategori</label>
                    <select id="category-filter" class="category-filter" aria-label="Filter berdasarkan kategori">
                        <option value="">Semua kategori</option>
                        @foreach ($complaints->getCollection()->pluck('category')->filter()->unique('id')->sortBy('name') as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="primary-button primary-button--link">Cari</button>
                    @if ($search !== '')
                        <a class="text-link" href="{{ route($complaintsRoutePrefix.'.index') }}">Reset</a>
                    @endif
                </form>
            </div>

            @if ($complaints->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">▤</span>
                    <h3>Tidak ada pengaduan ditemukan</h3>
                    <p>Coba ubah kata pencarian atau buat pengaduan baru.</p>
                </div>
            @else
                <div class="table-scroll">
                    <table class="complaints-table">
                        <thead>
                            <tr>
                                <th>ID Pengaduan</th>
                                @if (auth()->user()->isAdmin())
                                    <th>Pelapor</th>
                                @endif
                                <th>Subjek</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                @if (auth()->user()->isAdmin())
                                    <th>Perbarui</th>
                                @endif
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($complaints as $complaint)
                                <tr data-category-id="{{ $complaint->category->id }}">
                                    <td class="ticket-id">
                                        SK-{{ str_pad((string) $complaint->id, 6, '0', STR_PAD_LEFT) }}
                                        <small>{{ $complaint->category->name }}</small>
                                    </td>
                                    @if (auth()->user()->isAdmin())
                                        <td>
                                            {{ $complaint->user?->name ?? 'Akun dihapus' }}
                                            <small class="cell-subtext">{{ $complaint->user?->nim }}</small>
                                        </td>
                                    @endif
                                    <td class="subject-cell">
                                        <a href="{{ route($complaintsRoutePrefix.'.show', $complaint) }}"><strong>{{ $complaint->title }}</strong></a>
                                        <small>{{ Str::limit($complaint->description, 90) }}</small>
                                    </td>
                                    <td>{{ $complaint->created_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="status status--{{ $complaint->status === 'Selesai' ? 'done' : ($complaint->status === 'Diproses' ? 'progress' : 'waiting') }}">
                                            {{ $complaint->status }}
                                        </span>
                                    </td>
                                    @if (auth()->user()->isAdmin())
                                        <td>@include('complaints.partials.inline-status', ['complaint' => $complaint])</td>
                                    @endif
                                    <td class="row-actions">
                                        <a class="text-link" href="{{ route($complaintsRoutePrefix.'.show', $complaint) }}">Detail</a>
                                        @can('update', $complaint)
                                            <a class="text-link" href="{{ route($complaintsRoutePrefix.'.edit', $complaint) }}">Edit</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="category-filter-empty" data-category-empty hidden>Tidak ada pengaduan pada kategori ini di halaman sekarang.</p>
                <div class="pagination">
                    <span>Menampilkan {{ $complaints->firstItem() }}–{{ $complaints->lastItem() }} dari {{ $complaints->total() }} pengaduan</span>
                    <div>
                        @if ($complaints->previousPageUrl())
                            <a href="{{ $complaints->previousPageUrl() }}">← Sebelumnya</a>
                        @endif
                        <span>Halaman {{ $complaints->currentPage() }} dari {{ $complaints->lastPage() }}</span>
                        @if ($complaints->nextPageUrl())
                            <a href="{{ $complaints->nextPageUrl() }}">Berikutnya →</a>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        (() => {
            const filter = document.querySelector('[data-complaints-page] #category-filter');
            const rows = document.querySelectorAll('[data-complaints-page] .complaints-table tbody tr[data-category-id]');
            const emptyMessage = document.querySelector('[data-complaints-page] [data-category-empty]');
            const count = document.querySelector('[data-complaints-page] [data-complaint-count]');

            if (!filter || rows.length === 0 || !emptyMessage || !count) {
                return;
            }

            filter.addEventListener('change', () => {
                let visibleRows = 0;

                rows.forEach((row) => {
                    const isVisible = filter.value === '' || row.dataset.categoryId === filter.value;
                    row.hidden = !isVisible;
                    visibleRows += Number(isVisible);
                });

                emptyMessage.hidden = visibleRows > 0;
                count.textContent = filter.value === ''
                    ? count.dataset.defaultCount
                    : `${visibleRows} pengaduan pada halaman ini`;
            });
        })();
    </script>
@endsection