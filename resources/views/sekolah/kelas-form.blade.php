@extends('layouts.app')

@section('title', $kelas->exists ? 'Ubah Kelas' : 'Tambah Kelas')

@section('content')
    <div class="page-heading"><h1>{{ $kelas->exists ? 'Ubah data kelas' : 'Tambah kelas' }}</h1><p class="mb-0">Contoh nama kelas: 4A atau 5B.</p></div>
    <div class="card" style="max-width: 720px"><form method="POST" action="{{ $kelas->exists ? route('sekolah.kelas.update', $kelas) : route('sekolah.kelas.store') }}">
        @csrf @if ($kelas->exists) @method('PUT') @endif
        <div class="card-body">
            <div class="mb-3"><label class="form-label required-label" for="nama_kelas">Nama kelas</label><input class="form-control" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" placeholder="Contoh: 4A" required></div>
            <div class="mb-3"><label class="form-label required-label" for="tingkat">Tingkat</label><select class="form-select" id="tingkat" name="tingkat" required><option value="">Pilih tingkat</option>@foreach (range(1, 6) as $tingkat)<option value="{{ $tingkat }}" @selected(old('tingkat', $kelas->tingkat) == $tingkat)>Kelas {{ $tingkat }}</option>@endforeach</select></div>
            <div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" name="status_aktif" value="1" id="status_aktif" @checked(old('status_aktif', $kelas->exists ? $kelas->status_aktif : true))><label class="form-check-label" for="status_aktif">Kelas masih aktif</label></div>
            <div class="d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Simpan kelas</button><a class="btn btn-outline-secondary" href="{{ route('sekolah.kelas.index') }}">Batal</a></div>
        </div>
    </form></div>
@endsection
