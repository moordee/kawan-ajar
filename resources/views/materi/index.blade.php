@extends('layouts.app')

@section('title', 'Lemari Materi')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div><h1>Lemari Materi</h1><p class="mb-0">Simpan dan temukan kembali bahan ajar Bapak/Ibu.</p></div>
        <a href="{{ route('materi.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah materi</a>
    </div>
    <div class="card mb-3">
        <form class="card-body row g-2 align-items-end" method="GET">
            <div class="col-12 col-md-6"><label class="form-label" for="cari">Cari judul materi</label><input class="form-control" id="cari" name="cari" value="{{ request('cari') }}" placeholder="Contoh: Pecahan sederhana"></div>
            <div class="col-12 col-md-4"><label class="form-label" for="kelas_id">Kelas</label><select class="form-select" id="kelas_id" name="kelas_id"><option value="">Semua kelas</option>@foreach ($kelasList as $kelas)<option value="{{ $kelas->kelas_id }}" @selected(request('kelas_id') == $kelas->kelas_id)>{{ $kelas->nama_kelas }}</option>@endforeach</select></div>
            <div class="col-12 col-md-2 d-flex gap-2"><button class="btn btn-outline-primary flex-fill" type="submit"><i class="bi bi-search me-1"></i> Cari</button><a class="btn btn-outline-secondary" href="{{ route('materi.index') }}" aria-label="Hapus filter"><i class="bi bi-x-lg"></i></a></div>
        </form>
    </div>
    <div class="card">
        <div class="card-header"><i class="bi bi-journal-bookmark me-2"></i>Daftar materi <span class="badge badge-soft ms-1">{{ $materi->total() }}</span></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Judul materi</th><th>Mata pelajaran</th><th>Kelas</th><th>Tanggal dibuat</th><th>Lampiran</th><th class="text-end">Tindakan</th></tr></thead>
                <tbody>
                    @forelse ($materi as $item)
                        <tr>
                            <td><div class="fw-semibold">{{ $item->judul_materi }}</div><div class="small text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($item->isi_materi), 75) }}</div></td>
                            <td>{{ $item->mapel->nama_mapel }}</td><td>{{ $item->kelas->nama_kelas }}</td>
                            <td>{{ $item->tanggal_dibuat?->format('d M Y') }}</td>
                            <td>@if ($item->lampiran_dokumen)<a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->lampiran_dokumen) }}" target="_blank" rel="noopener">Lihat berkas</a>@else<span class="text-muted">—</span>@endif</td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('materi.edit', $item) }}"><i class="bi bi-pencil me-1"></i>Ubah</a>
                                <form class="d-inline" method="POST" action="{{ route('materi.destroy', $item) }}" data-confirm="Hapus materi ini?">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Hapus materi"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-journal-plus"></i>Belum ada materi yang cocok. Tambahkan materi pertama Bapak/Ibu.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($materi->hasPages())<div class="card-body border-top">{{ $materi->links() }}</div>@endif
    </div>
@endsection
