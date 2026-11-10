@extends('layouts.portal')

@section('title', 'Detail Pengaduan')

@section('content')
    @php($complaintsRoutePrefix = auth()->user()->isAdmin() ? 'admin.complaints' : 'complaints')

    <div class="complaint-detail-page">
        <section class="page-heading complaint-detail-heading">
            <div>
                <p class="eyebrow">Detail laporan · SK-{{ str_pad((string) $complaint->id, 6, '0', STR_PAD_LEFT) }}</p>
                <h1>{{ $complaint->title }}</h1>
                <p>Dibuat {{ $complaint->created_at->format('d M Y, H:i') }}</p>
            </div>
            <a class="text-link" href="{{ route($complaintsRoutePrefix.'.index') }}">← Kembali ke daftar</a>
        </section>

        <section class="panel complaint-detail">
            <div class="complaint-detail-summary">
                <div>
                    <p class="eyebrow">Status laporan</p>
                    <span class="status status--{{ $complaint->status === 'Selesai' ? 'done' : ($complaint->status === 'Diproses' ? 'progress' : 'waiting') }}">{{ $complaint->status }}</span>
                </div>
                <span class="complaint-category">{{ $complaint->category->name }}</span>
            </div>

            <dl class="detail-grid">
                <div>
                    <dt>Pelapor</dt>
                    <dd>{{ $complaint->user?->name ?? 'Akun dihapus' }}{{ $complaint->user?->nim ? ' · '.$complaint->user->nim : '' }}</dd>
                </div>
                <div>
                    <dt>Lokasi</dt>
                    <dd>{{ $complaint->location ?: 'Tidak dicantumkan' }}</dd>
                </div>
                <div class="detail-description">
                    <dt>Isi pengaduan</dt>
                    <dd>{{ $complaint->description }}</dd>
                </div>
            </dl>

            @canany(['update', 'delete'], $complaint)
                <div class="complaint-detail-actions">
                    <span>Aksi pengaduan</span>
                    <div class="row-actions">
                        @can('update', $complaint)
                            <a class="primary-button primary-button--link" href="{{ route($complaintsRoutePrefix.'.edit', $complaint) }}">Edit</a>
                        @endcan
                        @can('delete', $complaint)
                            <form method="POST" action="{{ route($complaintsRoutePrefix.'.destroy', $complaint) }}" onsubmit="return confirm('Hapus pengaduan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="delete-button" type="submit">Hapus</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endcanany
        </section>
    </div>
@endsection
