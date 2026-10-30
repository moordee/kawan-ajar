@extends('layouts.app')

@section('title', $ulangan->exists ? 'Ubah Ulangan' : 'Buat Ulangan')

@section('content')
    <div class="page-heading"><h1>{{ $ulangan->exists ? 'Atur ulangan' : 'Susun ulangan harian' }}</h1><p class="mb-0">Tentukan peserta dan pilih soal yang akan dikerjakan.</p></div>
    <form method="POST" action="{{ $ulangan->exists ? route('ulangan.update', $ulangan) : route('ulangan.store') }}">
        @csrf @if ($ulangan->exists) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-12 col-xl-7">
                <div class="card mb-3"><div class="card-header">Informasi ulangan</div><div class="card-body">
                    <div class="mb-3"><label class="form-label required-label" for="judul_ulangan">Nama ulangan</label><input class="form-control" id="judul_ulangan" name="judul_ulangan" value="{{ old('judul_ulangan', $ulangan->judul_ulangan) }}" placeholder="Contoh: Ulangan Harian — Pecahan" required></div>
                    <div class="row g-3"><div class="col-12 col-md-6"><label class="form-label required-label" for="kelas_id">Kelas</label><select class="form-select" name="kelas_id" id="kelas_id" required><option value="">Pilih kelas</option>@foreach ($kelasList as $kelas)<option value="{{ $kelas->kelas_id }}" @selected(old('kelas_id', $ulangan->kelas_id) == $kelas->kelas_id)>{{ $kelas->nama_kelas }}</option>@endforeach</select></div><div class="col-12 col-md-6"><label class="form-label required-label" for="mapel_id">Mata pelajaran</label><select class="form-select" name="mapel_id" id="mapel_id" required><option value="">Pilih mata pelajaran</option>@foreach ($mapelList as $mapel)<option value="{{ $mapel->mapel_id }}" @selected(old('mapel_id', $ulangan->mapel_id) == $mapel->mapel_id)>{{ $mapel->nama_mapel }}</option>@endforeach</select></div></div>
                    <hr><span class="form-label d-block">Jenis soal yang diizinkan</span>
                    @foreach (['Pilihan Ganda','Esai'] as $type)<div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="jenis_soal_diizinkan[]" id="tipe-{{ $loop->index }}" value="{{ $type }}" @checked(in_array($type, old('jenis_soal_diizinkan', $ulangan->jenis_soal_diizinkan ?? ['Pilihan Ganda','Esai']), true))><label class="form-check-label" for="tipe-{{ $loop->index }}">{{ $type }}</label></div>@endforeach
                </div></div>
                <div class="card"><div class="card-header d-flex justify-content-between"><span>Pilih soal</span><span class="small text-muted">Pilih sedikitnya satu soal</span></div><div class="card-body">
                    @php($selected = old('soal_ids', isset($ulangan) && $ulangan->exists ? $ulangan->soal->pluck('soal_id')->all() : []))
                    @forelse ($soalList as $soal)<label class="question-option d-flex gap-3 align-items-start mb-2"><input class="form-check-input mt-1" type="checkbox" name="soal_ids[]" value="{{ $soal->soal_id }}" @checked(in_array($soal->soal_id, $selected))><span><span class="fw-semibold">{{ \Illuminate\Support\Str::limit($soal->teks_soal, 135) }}</span><span class="d-block small text-muted">{{ $soal->mapel->nama_mapel }} · {{ $soal->jenis_soal }} · {{ $soal->tingkat_kesulitan }}</span></span></label>@empty<div class="empty-state"><i class="bi bi-patch-question"></i>Bank soal masih kosong. <a href="{{ route('bank-soal.create') }}">Buat soal terlebih dahulu.</a></div>@endforelse
                </div></div>
            </div>
            <div class="col-12 col-xl-5">
                <div class="card"><div class="card-header">Pengaturan pengerjaan</div><div class="card-body">
                    @foreach ([['acak_soal','Acak urutan soal'],['acak_opsi','Acak pilihan jawaban'],['tampilkan_hasil','Tampilkan hasil setelah selesai']] as [$field,$label])<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="{{ $field }}" value="1" id="{{ $field }}" @checked(old($field, $ulangan->$field ?? ($field === 'tampilkan_hasil')))><label class="form-check-label" for="{{ $field }}">{{ $label }}</label></div>@endforeach
                    <div class="alert alert-light border small"><i class="bi bi-info-circle me-2"></i>Urutan soal yang dipilih menjadi urutan awal saat ulangan dibuka.</div>
                    <div class="d-grid gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-2"></i>Simpan ulangan</button><a class="btn btn-outline-secondary" href="{{ route('ulangan.index') }}">Batal</a></div>
                </div></div>
            </div>
        </div>
    </form>
@endsection
