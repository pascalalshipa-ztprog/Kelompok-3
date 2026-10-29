@extends('layouts.portal')

@section('title', 'Manajemen Pengguna')

@section('content')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Pusat kendali</p>
            <h1>Manajemen pengguna</h1>
            <p>Lihat dan kelola akun mahasiswa serta administrator.</p>
        </div>
    </section>

    <section class="panel table-panel">
        <form class="search-bar" method="GET" action="{{ route('users.index') }}">
            <label class="sr-only" for="search">Cari pengguna</label>
            <input id="search" name="search" value="{{ $search }}" placeholder="Cari nama, NIM, atau email">
            <button type="submit" class="primary-button primary-button--link">Cari</button>
            @if ($search !== '')
                <a class="text-link" href="{{ route('users.index') }}">Reset</a>
            @endif
        </form>

        @if ($users->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">♙</span>
                <h3>Tidak ada pengguna ditemukan</h3>
                <p>Coba ubah kata pencarian.</p>
            </div>
        @else
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>NIM</th>
                            <th>Peran</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}<small>{{ $user->email }}</small></td>
                                <td>{{ $user->nim ?? '—' }}</td>
                                <td><span class="user-role {{ $user->isAdmin() ? 'user-role--admin' : '' }}">{{ $user->isAdmin() ? 'Admin' : 'Mahasiswa' }}</span></td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td class="user-actions">
                                    <a class="text-link" href="{{ route('users.edit', $user) }}">Ubah</a>
                                    @if (auth()->user()->isNot($user))
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus akun ini? Pengaduannya tetap tersimpan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="delete-button" type="submit">Hapus</button>
                                        </form>
                                    @else
                                        <span class="user-current">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <span>Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna</span>
                <div>
                    @if ($users->previousPageUrl())
                        <a href="{{ $users->previousPageUrl() }}">← Sebelumnya</a>
                    @endif
                    <span>Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</span>
                    @if ($users->nextPageUrl())
                        <a href="{{ $users->nextPageUrl() }}">Berikutnya →</a>
                    @endif
                </div>
            </div>
        @endif
    </section>
@endsection
