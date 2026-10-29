<form class="inline-status-form" method="POST" action="{{ route('complaints.update-status', $complaint) }}">
    @csrf
    @method('PATCH')
    <label class="sr-only" for="status-{{ $complaint->id }}">Ubah status {{ $complaint->title }}</label>
    <select id="status-{{ $complaint->id }}" name="status" aria-label="Status pengaduan {{ $complaint->id }}">
        @foreach (['Menunggu', 'Diproses', 'Selesai', 'Ditolak'] as $status)
            <option value="{{ $status }}" @selected($complaint->status === $status)>{{ $status }}</option>
        @endforeach
    </select>
    <button class="inline-status-save" type="submit" title="Simpan status" aria-label="Simpan status pengaduan {{ $complaint->id }}">✓</button>
</form>