@extends('layouts.app')

@section('title', $ujian->exists ? 'Ubah UTS / SAS' : 'Susun UTS / SAS')

@section('content')
    <div class="page-heading"><h1>{{ $ujian->exists ? 'Atur ujian besar' : 'Susun UTS / SAS' }}</h1><p class="mb-0">Tentukan informasi ujian, jumlah soal, dan bobot nilai.</p></div>
    <form method="POST" action="{{ $ujian->exists ? route('ujian-besar.update', $ujian) : route('ujian-besar.store') }}">
        @csrf @if ($ujian->exists) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-12 col-xl-7">
                <div class="card mb-3"><div class="card-header">Informasi ujian</div><div class="card-body">
                    <div class="mb-3"><label class="form-label required-label" for="judul_ujian">Nama ujian</label><input class="form-control" id="judul_ujian" name="judul_ujian" value="{{ old('judul_ujian', $ujian->judul_ujian) }}" placeholder="Contoh: SAS Matematika" required></div>
                    <div class="row g-3"><div class="col-12 col-md-6"><label class="form-label required-label" for="semester">Semester</label><select class="form-select" id="semester" name="semester" required><option value="">Pilih semester</option>@foreach (['Semester 1','Semester 2'] as $s)<option @selected(old('semester', $ujian->semester) === $s)>{{ $s }}</option>@endforeach</select></div>
                        <div class="col-12 col-md-6"><label class="form-label required-label" for="mapel_id">Mata pelajaran</label><select class="form-select" id="mapel_id" name="mapel_id" required><option value="">Pilih mata pelajaran</option>@foreach ($mapelList as $mapel)<option value="{{ $mapel->mapel_id }}" @selected(old('mapel_id', $ujian->mapel_id) == $mapel->mapel_id)>{{ $mapel->nama_mapel }}</option>@endforeach</select></div>
                        <div class="col-6"><label class="form-label required-label" for="waktu_ujian">Waktu (menit)</label><input class="form-control" type="number" id="waktu_ujian" name="waktu_ujian" min="1" max="600" value="{{ old('waktu_ujian', $ujian->waktu_ujian ?? 90) }}" required></div>
                        <div class="col-6"><label class="form-label required-label" for="jumlah_soal">Jumlah soal</label><input class="form-control" type="number" id="jumlah_soal" name="jumlah_soal" min="1" max="500" value="{{ old('jumlah_soal', $ujian->jumlah_soal ?? 30) }}" required></div>
                    </div>
                </div></div>
                <div class="card mb-3"><div class="card-header">Pilih soal dari bank soal</div><div class="card-body">
                    @php($selected = old('soal_ids', isset($ujian) && $ujian->exists ? $ujian->soal->pluck('soal_id')->all() : []))
                    @forelse ($soalList as $soal)<label class="question-option d-flex gap-3 align-items-start mb-2"><input class="form-check-input mt-1" type="checkbox" name="soal_ids[]" value="{{ $soal->soal_id }}" @checked(in_array($soal->soal_id, $selected))><span><span class="fw-semibold">{{ \Illuminate\Support\Str::limit($soal->teks_soal, 135) }}</span><span class="d-block small text-muted">{{ $soal->mapel->nama_mapel }} · {{ $soal->jenis_soal }} · {{ $soal->tingkat_kesulitan }}</span></span></label>@empty<div class="empty-state"><i class="bi bi-patch-question"></i>Bank soal masih kosong. <a href="{{ route('bank-soal.create') }}">Buat soal terlebih dahulu.</a></div>@endforelse
                </div></div>
            </div>
            <div class="col-12 col-xl-5"><div class="card"><div class="card-header">Pengaturan nilai</div><div class="card-body">
                @foreach ([['acak_soal','Acak urutan soal'],['acak_pilihan','Acak pilihan jawaban']] as [$field,$label])<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="{{ $field }}" value="1" id="{{ $field }}" @checked(old($field, $ujian->$field ?? false))><label class="form-check-label" for="{{ $field }}">{{ $label }}</label></div>@endforeach
                <hr><div class="mb-3"><label class="form-label required-label" for="total_bobot">Total bobot nilai</label><input class="form-control" type="number" name="total_bobot" id="total_bobot" min="0" max="1000" step="0.5" value="{{ old('total_bobot', $ujian->total_bobot ?? 100) }}" required><div class="form-text">Contoh: nilai akhir dari 100.</div></div>
                <label class="form-label">Bobot per tingkat kesulitan (opsional)</label>
                @foreach (['Mudah','Sedang','Sulit'] as $level)<div class="input-group mb-2"><span class="input-group-text">{{ $level }}</span><input class="form-control" type="number" min="0" step="0.5" name="bobot_nilai_per_tingkat_kesulitan[{{ $level }}]" value="{{ old('bobot_nilai_per_tingkat_kesulitan.'.$level, $ujian->bobot_nilai_per_tingkat_kesulitan[$level] ?? '') }}" placeholder="Bobot"></div>@endforeach
                <div class="alert alert-light border small mt-3"><i class="bi bi-info-circle me-2"></i>Urutan soal yang dipilih menjadi urutan awal. Periksa kembali jumlah soal yang dipilih.</div>
                <div class="d-grid gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-2"></i>Simpan ujian</button><a href="{{ route('ujian-besar.index') }}" class="btn btn-outline-secondary">Batal</a></div>
            </div></div></div>
        </div>
    </form>
@endsection
