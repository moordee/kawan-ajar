@extends('layouts.app')

@section('title', 'UTS / SAS')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3"><div><h1>Mesin Pembuat Ujian Besar</h1><p class="mb-0">Siapkan Ujian Tengah Semester atau Sumatif Akhir Semester.</p></div><a class="btn btn-primary" href="{{ route('ujian-besar.create') }}"><i class="bi bi-plus-lg me-2"></i>Susun UTS / SAS</a></div>
    <div class="card"><div class="card-header"><i class="bi bi-clipboard-data me-2"></i>Daftar ujian</div><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Nama ujian</th><th>Semester</th><th>Mata pelajaran</th><th>Waktu</th><th>Jumlah soal</th><th class="text-end">Tindakan</th></tr></thead><tbody>
        @forelse ($ujianList as $ujian)<tr><td class="fw-semibold">{{ $ujian->judul_ujian }}</td><td>{{ $ujian->semester }}</td><td>{{ $ujian->mapel->nama_mapel }}</td><td>{{ $ujian->waktu_ujian }} menit</td><td>{{ $ujian->soal_count }}</td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="{{ route('ujian-besar.edit', $ujian) }}">Atur</a> <form class="d-inline" method="POST" action="{{ route('ujian-besar.destroy', $ujian) }}" data-confirm="Hapus ujian ini?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" aria-label="Hapus ujian"><i class="bi bi-trash"></i></button></form></td></tr>
        @empty<tr><td colspan="6"><div class="empty-state"><i class="bi bi-calendar2-check"></i>Belum ada UTS/SAS yang disusun.</div></td></tr>@endforelse
        </tbody></table></div>@if ($ujianList->hasPages())<div class="card-body border-top">{{ $ujianList->links() }}</div>@endif</div>
@endsection
