@extends('layouts.app')

@section('title', $materi->exists ? 'Ubah Materi' : 'Tambah Materi')

@section('content')
    <div class="page-heading"><h1>{{ $materi->exists ? 'Ubah materi' : 'Tambah materi' }}</h1><p class="mb-0">Lengkapi informasi agar materi mudah ditemukan dan digunakan kembali.</p></div>
    <form method="POST" enctype="multipart/form-data" action="{{ $materi->exists ? route('materi.update', $materi) : route('materi.store') }}">
        @csrf @if ($materi->exists) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-12 col-xl-8">
                <div class="card mb-3">
                    <div class="card-header">Isi materi</div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label required-label" for="judul_materi">Judul materi</label><input class="form-control" id="judul_materi" name="judul_materi" value="{{ old('judul_materi', $materi->judul_materi) }}" placeholder="Contoh: Mengenal pecahan sederhana" required></div>
                        <div><label class="form-label required-label" for="isi_materi">Isi materi</label><textarea class="form-control" id="isi_materi" name="isi_materi" rows="9" placeholder="Tuliskan penjelasan dengan bahasa yang mudah dipahami siswa..." required>{{ old('isi_materi', $materi->isi_materi) }}</textarea><div class="form-text">Gunakan penjelasan singkat dan sesuai dengan tingkat kelas.</div></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Lampiran (opsional)</div>
                    <div class="card-body">
                        <label class="form-label" for="lampiran">Pilih berkas</label>
                        <input class="form-control" id="lampiran" name="lampiran" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
                        <div class="form-text">PDF, dokumen, presentasi, atau gambar. Maksimal 10 MB.</div>
                        @if ($materi->lampiran_dokumen)<div class="small mt-2">Lampiran saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($materi->lampiran_dokumen) }}" target="_blank" rel="noopener">Lihat berkas</a></div>@endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">Untuk siapa materi ini?</div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label required-label" for="kelas_id">Kelas</label><select class="form-select" id="kelas_id" name="kelas_id" required><option value="">Pilih kelas</option>@foreach ($kelasList as $kelas)<option value="{{ $kelas->kelas_id }}" @selected(old('kelas_id', $materi->kelas_id) == $kelas->kelas_id)>{{ $kelas->nama_kelas }} — Kelas {{ $kelas->tingkat }}</option>@endforeach</select></div>
                        <div class="mb-3"><label class="form-label required-label" for="mapel_id">Mata pelajaran</label><select class="form-select" id="mapel_id" name="mapel_id" required><option value="">Pilih mata pelajaran</option>@foreach ($mapelList as $mapel)<option value="{{ $mapel->mapel_id }}" @selected(old('mapel_id', $materi->mapel_id) == $mapel->mapel_id)>{{ $mapel->nama_mapel }}</option>@endforeach</select></div>
                        @if ($kelasList->isEmpty() || $mapelList->isEmpty())<div class="alert alert-warning small">Tambahkan data kelas dan mata pelajaran terlebih dahulu.</div>@endif
                        <div class="d-grid gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-2"></i>Simpan materi</button><a class="btn btn-outline-secondary" href="{{ route('materi.index') }}">Batal</a></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
