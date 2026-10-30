@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div><h1>Selamat datang, {{ Auth::user()->nama }} 👋</h1><p class="mb-0">Apa yang akan Bapak/Ibu siapkan hari ini?</p></div>
        <a href="{{ route('materi.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah materi</a>
    </div>
    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Materi pelajaran', 'jumlah' => $jumlahMateri, 'ikon' => 'bi-journal-bookmark', 'tautan' => route('materi.index')],
            ['label' => 'Koleksi soal', 'jumlah' => $jumlahSoal, 'ikon' => 'bi-question-circle', 'tautan' => route('bank-soal.index')],
            ['label' => 'Ulangan harian', 'jumlah' => $jumlahUlangan, 'ikon' => 'bi-clipboard-check', 'tautan' => route('ulangan.index')],
            ['label' => 'Ujian besar', 'jumlah' => $jumlahUjian, 'ikon' => 'bi-clipboard-data', 'tautan' => route('ujian-besar.index')],
        ] as $stat)
            <div class="col-6 col-xl-3"><a class="text-decoration-none text-reset" href="{{ $stat['tautan'] }}"><div class="card stat-card h-100"><div class="card-body"><div class="stat-icon"><i class="bi {{ $stat['ikon'] }}"></i></div><div class="stat-number">{{ $stat['jumlah'] }}</div><div class="stat-label">{{ $stat['label'] }}</div></div></div></a></div>
        @endforeach
    </div>
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8"><div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center"><span><i class="bi bi-journal-text me-2"></i>Materi terbaru</span><a href="{{ route('materi.index') }}" class="small">Lihat semua</a></div>
            <div class="card-body p-0">@forelse ($materiTerbaru as $materi)
                <div class="d-flex justify-content-between align-items-center gap-3 px-3 py-3 border-bottom"><div><div class="fw-semibold">{{ $materi->judul_materi }}</div><div class="small text-muted">{{ $materi->mapel->nama_mapel }} · Kelas {{ $materi->kelas->nama_kelas }}</div></div><a class="btn btn-sm btn-outline-secondary" href="{{ route('materi.edit', $materi) }}">Buka</a></div>
            @empty<div class="empty-state"><i class="bi bi-journal-plus"></i>Belum ada materi. Tambahkan materi pertama Bapak/Ibu.</div>@endforelse</div>
        </div></div>
        <div class="col-12 col-xl-4"><div class="card h-100">
            <div class="card-header"><i class="bi bi-lightning-charge me-2"></i>Mulai dari sini</div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action py-3" href="{{ route('bank-soal.create') }}"><i class="bi bi-plus-circle me-2"></i>Buat soal baru</a>
                <a class="list-group-item list-group-item-action py-3" href="{{ route('ulangan.create') }}"><i class="bi bi-clipboard-plus me-2"></i>Susun ulangan harian</a>
                <a class="list-group-item list-group-item-action py-3" href="{{ route('ujian-besar.create') }}"><i class="bi bi-calendar2-check me-2"></i>Siapkan UTS / SAS</a>
                <a class="list-group-item list-group-item-action py-3" href="{{ route('sekolah.kelas.index') }}"><i class="bi bi-people me-2"></i>Kelola kelas dan siswa</a>
            </div>
        </div></div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-6"><div class="card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i class="bi bi-door-open"></i></div><div><div class="fs-4 fw-bold">{{ $jumlahKelas }}</div><div class="small text-muted">Kelas aktif</div></div></div></div></div>
        <div class="col-6"><div class="card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i class="bi bi-people"></i></div><div><div class="fs-4 fw-bold">{{ $jumlahSiswa }}</div><div class="small text-muted">Siswa terdaftar</div></div></div></div></div>
    </div>
    <div class="card">
        <div class="card-header"><i class="bi bi-clipboard-check me-2"></i>Ulangan yang disusun</div>
        <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Nama ulangan</th><th>Mata pelajaran</th><th>Kelas</th><th>Jumlah soal</th><th></th></tr></thead>
            <tbody>@forelse ($ulanganTerbaru as $ulangan)<tr><td class="fw-semibold">{{ $ulangan->judul_ulangan }}</td><td>{{ $ulangan->mapel->nama_mapel }}</td><td>{{ $ulangan->kelas->nama_kelas }}</td><td>{{ $ulangan->soal()->count() }}</td><td><a href="{{ route('ulangan.edit', $ulangan) }}" class="btn btn-sm btn-outline-secondary">Atur</a></td></tr>@empty<tr><td class="empty-state" colspan="5"><i class="bi bi-clipboard"></i>Belum ada ulangan yang disusun.</td></tr>@endforelse</tbody>
        </table></div>
    </div>
@endsection
