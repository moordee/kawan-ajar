@extends('layouts.app')

@section('title', 'Ulangan Harian')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3"><div><h1>Mesin Pembuat Ulangan</h1><p class="mb-0">Susun uji kompetensi dari soal yang sudah ada di bank soal.</p></div><a class="btn btn-primary" href="{{ route('ulangan.create') }}"><i class="bi bi-plus-lg me-2"></i>Buat ulangan</a></div>
    <div class="card"><div class="card-header"><i class="bi bi-clipboard-check me-2"></i>Ulangan harian</div><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Nama ulangan</th><th>Mata pelajaran</th><th>Kelas</th><th>Jumlah soal</th><th>Hasil siswa</th><th class="text-end">Tindakan</th></tr></thead><tbody>
        @forelse ($ulanganList as $ulangan)<tr><td class="fw-semibold">{{ $ulangan->judul_ulangan }}</td><td>{{ $ulangan->mapel->nama_mapel }}</td><td>{{ $ulangan->kelas->nama_kelas }}</td><td>{{ $ulangan->soal_count }}</td><td>{{ $ulangan->tampilkan_hasil ? 'Ditampilkan' : 'Tidak ditampilkan' }}</td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="{{ route('ulangan.edit', $ulangan) }}">Atur</a> <form class="d-inline" method="POST" action="{{ route('ulangan.destroy', $ulangan) }}" data-confirm="Hapus ulangan ini?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" aria-label="Hapus ulangan"><i class="bi bi-trash"></i></button></form></td></tr>
        @empty<tr><td colspan="6"><div class="empty-state"><i class="bi bi-clipboard-plus"></i>Belum ada ulangan. Pilih soal dari bank soal untuk mulai menyusun.</div></td></tr>@endforelse
        </tbody></table></div>@if ($ulanganList->hasPages())<div class="card-body border-top">{{ $ulanganList->links() }}</div>@endif</div>
@endsection
