@extends('layouts.app')

@section('title', 'Kelas & Siswa')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3"><div><h1>Kelas & Siswa</h1><p class="mb-0">Atur rombongan belajar dan daftar siswa Bapak/Ibu.</p></div><a href="{{ route('sekolah.kelas.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah kelas</a></div>
    <div class="row g-3">
        @forelse ($kelasList as $kelas)
            <div class="col-12 col-xl-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-start gap-2">
                        <div><h2 class="h5 mb-1">{{ $kelas->nama_kelas }}</h2><div class="small text-muted">Kelas {{ $kelas->tingkat }} · {{ $kelas->siswa_count }} siswa</div></div>
                        <div class="d-flex gap-1">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('sekolah.kelas.edit', $kelas) }}" aria-label="Ubah kelas {{ $kelas->nama_kelas }}"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('sekolah.kelas.destroy', $kelas) }}" data-confirm="Hapus kelas {{ $kelas->nama_kelas }}? Kelas yang memiliki siswa tidak dapat dihapus.">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" aria-label="Hapus kelas"><i class="bi bi-trash"></i></button></form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="fw-semibold mb-2">Mata pelajaran yang diajar</div>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @forelse ($pengajaran->get($kelas->kelas_id, collect()) as $relasi)
                                    <span class="badge badge-soft d-inline-flex align-items-center gap-2">{{ $relasi->nama_mapel }}
                                        <form method="POST" action="{{ route('sekolah.pengajaran.destroy', $relasi->id) }}" data-confirm="Hapus {{ $relasi->nama_mapel }} dari kelas ini?">@csrf @method('DELETE')<button class="btn-close" style="font-size:.55rem" aria-label="Hapus mata pelajaran"></button></form>
                                    </span>
                                @empty
                                    <span class="small text-muted">Belum ada mata pelajaran yang ditambahkan.</span>
                                @endforelse
                            </div>
                            <form method="POST" action="{{ route('sekolah.pengajaran.store', $kelas) }}" class="d-flex gap-2">
                                @csrf
                                <select class="form-select form-select-sm" name="mapel_id" aria-label="Pilih mata pelajaran untuk kelas {{ $kelas->nama_kelas }}" required>
                                    <option value="">Pilih mata pelajaran</option>
                                    @foreach ($mapelList as $mapel)
                                        <option value="{{ $mapel->mapel_id }}">{{ $mapel->nama_mapel }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary text-nowrap" type="submit"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
                            </form>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3"><span class="fw-semibold">Daftar siswa</span><button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#tambah-siswa-{{ $kelas->kelas_id }}"><i class="bi bi-person-plus me-1"></i>Tambah siswa</button></div>
                        <div class="collapse mb-3" id="tambah-siswa-{{ $kelas->kelas_id }}"><form method="POST" action="{{ route('sekolah.siswa.store', $kelas) }}" class="row g-2">@csrf<div class="col-12 col-md-5"><label class="form-label small" for="nama-{{ $kelas->kelas_id }}">Nama siswa</label><input class="form-control" id="nama-{{ $kelas->kelas_id }}" name="nama_siswa" required></div><div class="col-12 col-md-4"><label class="form-label small" for="nis-{{ $kelas->kelas_id }}">NIS</label><input class="form-control" id="nis-{{ $kelas->kelas_id }}" name="nis" required></div><div class="col-12 col-md-3 d-flex align-items-end"><button class="btn btn-primary w-100">Simpan</button></div></form></div>
                        @php($siswaList = $kelas->siswa)
                        @if ($siswaList->isEmpty())<div class="text-muted small py-3">Belum ada siswa yang terdaftar di kelas ini.</div>
                        @else<div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Nama siswa</th><th>NIS</th><th></th></tr></thead><tbody>
                            @foreach ($siswaList as $siswa)<tr><td>{{ $siswa->nama_siswa }}</td><td>{{ $siswa->nis }}</td><td class="text-end"><button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#ubah-siswa-{{ $siswa->siswa_id }}" aria-label="Ubah data siswa"><i class="bi bi-pencil"></i></button> <form class="d-inline" method="POST" action="{{ route('sekolah.siswa.destroy', $siswa) }}" data-confirm="Hapus data {{ $siswa->nama_siswa }}?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" aria-label="Hapus siswa"><i class="bi bi-trash"></i></button></form></td></tr>
                                <tr class="collapse" id="ubah-siswa-{{ $siswa->siswa_id }}"><td colspan="3"><form method="POST" action="{{ route('sekolah.siswa.update', $siswa) }}" class="row g-2">@csrf @method('PUT')<div class="col-12 col-md-5"><label class="form-label small">Nama siswa</label><input class="form-control" name="nama_siswa" value="{{ $siswa->nama_siswa }}" required></div><div class="col-12 col-md-3"><label class="form-label small">NIS</label><input class="form-control" name="nis" value="{{ $siswa->nis }}" required></div><div class="col-12 col-md-2"><label class="form-label small">Kelas</label><select class="form-select" name="kelas_id">@foreach ($kelasList as $pilihanKelas)<option value="{{ $pilihanKelas->kelas_id }}" @selected($siswa->kelas_id === $pilihanKelas->kelas_id)>{{ $pilihanKelas->nama_kelas }}</option>@endforeach</select></div><div class="col-12 col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Simpan</button></div></form></td></tr>
                            @endforeach
                        </tbody></table></div>@endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="card"><div class="empty-state"><i class="bi bi-people"></i>Belum ada kelas. Tambahkan kelas untuk mulai mengelola siswa.</div></div></div>
        @endforelse
    </div>
    @if ($kelasList->hasPages())<div class="mt-3">{{ $kelasList->links() }}</div>@endif
@endsection
