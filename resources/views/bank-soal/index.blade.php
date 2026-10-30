@extends('layouts.app')

@section('title', 'Bank Soal')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div><h1>Bank Soal</h1><p class="mb-0">Kumpulan soal yang bisa dipakai kembali saat menyusun penilaian.</p></div>
        <a href="{{ route('bank-soal.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah soal</a>
    </div>
    <form class="card mb-3" method="GET"><div class="card-body row g-2 align-items-end">
        <div class="col-12 col-lg-5"><label class="form-label" for="cari">Cari isi soal</label><input class="form-control" id="cari" name="cari" value="{{ request('cari') }}" placeholder="Ketik kata dalam soal"></div>
        <div class="col-6 col-lg-3"><label class="form-label" for="mapel_id">Mata pelajaran</label><select class="form-select" name="mapel_id" id="mapel_id"><option value="">Semua mata pelajaran</option>@foreach ($mapelList as $mapel)<option value="{{ $mapel->mapel_id }}" @selected(request('mapel_id') == $mapel->mapel_id)>{{ $mapel->nama_mapel }}</option>@endforeach</select></div>
        <div class="col-6 col-lg-2"><label class="form-label" for="jenis_soal">Jenis</label><select class="form-select" name="jenis_soal" id="jenis_soal"><option value="">Semua jenis</option><option @selected(request('jenis_soal') === 'Pilihan Ganda')>Pilihan Ganda</option><option @selected(request('jenis_soal') === 'Esai')>Esai</option></select></div>
        <div class="col-12 col-lg-2 d-flex gap-2"><button class="btn btn-outline-primary flex-fill">Cari</button><a class="btn btn-outline-secondary" href="{{ route('bank-soal.index') }}" aria-label="Hapus filter"><i class="bi bi-x-lg"></i></a></div>
    </div></form>
    <div class="card">
        <div class="card-header"><i class="bi bi-question-circle me-2"></i>Soal tersimpan <span class="badge badge-soft ms-1">{{ $soalList->total() }}</span></div>
        <div class="table-responsive"><table class="table mb-0">
            <thead><tr><th>Soal</th><th>Mata pelajaran</th><th>Jenis</th><th>Kesulitan</th><th>Sumber</th><th class="text-end">Tindakan</th></tr></thead>
            <tbody>@forelse ($soalList as $soal)
                <tr><td><div class="fw-semibold">{{ \Illuminate\Support\Str::limit($soal->teks_soal, 100) }}</div>@if ($soal->bab_materi)<div class="small text-muted">Bab: {{ $soal->bab_materi }}</div>@endif</td><td>{{ $soal->mapel->nama_mapel }}</td><td>{{ $soal->jenis_soal }}</td><td><span class="badge badge-soft">{{ $soal->tingkat_kesulitan }}</span></td><td>{{ $soal->sumber_soal }}</td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="{{ route('bank-soal.edit', $soal) }}">Ubah</a> <form class="d-inline" method="POST" action="{{ route('bank-soal.destroy', $soal) }}" data-confirm="Hapus soal ini dari bank soal?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" aria-label="Hapus soal"><i class="bi bi-trash"></i></button></form></td></tr>
            @empty<tr><td colspan="6"><div class="empty-state"><i class="bi bi-patch-question"></i>Belum ada soal. Tambahkan soal sendiri atau buat dengan bantuan AI.</div></td></tr>@endforelse</tbody>
        </table></div>
        @if ($soalList->hasPages())<div class="card-body border-top">{{ $soalList->links() }}</div>@endif
    </div>
@endsection
