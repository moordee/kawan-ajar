@extends('layouts.app')

@section('title', isset($soal) ? 'Ubah Soal' : 'Tambah Soal')

@section('content')
    <div class="page-heading"><h1>{{ isset($soal) ? 'Ubah soal' : 'Tambah soal' }}</h1><p class="mb-0">Buat soal yang jelas dan sesuai materi pembelajaran.</p></div>
    <div class="row g-3">
        @if (!isset($soal) && $materiList->isNotEmpty())
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header"><i class="bi bi-stars me-2"></i>Buat soal dengan bantuan AI</div>
                    <div class="card-body">
                        @error('ai')<div class="alert alert-warning">{{ $message }}</div>@enderror
                        <p class="text-muted">Pilih materi yang sudah disimpan. AI akan menyusun soal berbahasa Indonesia untuk siswa sekolah dasar. Periksa kembali setiap soal sebelum digunakan.</p>
                        <form method="POST" action="{{ route('bank-soal.generate-ai') }}" class="row g-3">
                            @csrf
                            <div class="col-12 col-md-5"><label class="form-label required-label" for="materi_id">Materi acuan</label><select class="form-select" id="materi_id" name="materi_id" required><option value="">Pilih materi</option>@foreach ($materiList as $materi)<option value="{{ $materi->materi_id }}" @selected(old('materi_id') == $materi->materi_id)>{{ $materi->judul_materi }} — {{ $materi->mapel->nama_mapel }}</option>@endforeach</select></div>
                            <div class="col-6 col-md-2"><label class="form-label required-label" for="jumlah_soal_diminta">Jumlah soal</label><input class="form-control" type="number" id="jumlah_soal_diminta" name="jumlah_soal_diminta" min="1" max="10" value="{{ old('jumlah_soal_diminta', 3) }}" required></div>
                            <div class="col-6 col-md-5"><label class="form-label" for="prompt_instruksi">Instruksi tambahan (opsional)</label><input class="form-control" id="prompt_instruksi" name="prompt_instruksi" value="{{ old('prompt_instruksi') }}" placeholder="Contoh: gunakan situasi sehari-hari"></div>
                            <div class="col-12 col-md-6"><span class="form-label d-block">Jenis soal</span><div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="jenis_soal_diminta[]" value="Pilihan Ganda" id="ai-pg" checked><label class="form-check-label" for="ai-pg">Pilihan Ganda</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="jenis_soal_diminta[]" value="Esai" id="ai-esai"><label class="form-check-label" for="ai-esai">Esai</label></div></div>
                            <div class="col-12 col-md-6"><span class="form-label d-block">Tingkat kesulitan</span>@foreach (['Mudah','Sedang','Sulit'] as $tingkat)<div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tingkat_kesulitan_diminta[]" value="{{ $tingkat }}" id="ai-{{ strtolower($tingkat) }}" @checked($tingkat === 'Sedang')><label class="form-check-label" for="ai-{{ strtolower($tingkat) }}">{{ $tingkat }}</label></div>@endforeach</div>
                            <div class="col-12"><button class="btn btn-outline-primary" type="submit"><i class="bi bi-stars me-2"></i>Buat soal dengan AI</button></div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-12 col-xl-8">
            <form method="POST" action="{{ isset($soal) ? route('bank-soal.update', $soal) : route('bank-soal.store') }}">
                @csrf @if (isset($soal)) @method('PUT') @endif
                <div class="card mb-3"><div class="card-header">Isi soal</div><div class="card-body">
                    <div class="mb-3"><label class="form-label required-label" for="teks_soal">Pertanyaan</label><textarea class="form-control" id="teks_soal" name="teks_soal" rows="4" required placeholder="Tuliskan pertanyaan dengan kalimat yang mudah dipahami">{{ old('teks_soal', $soal->teks_soal ?? '') }}</textarea></div>
                    <div class="row g-3"><div class="col-12 col-md-6"><label class="form-label required-label" for="jenis_soal">Jenis soal</label><select class="form-select" id="jenis_soal" name="jenis_soal" required><option value="">Pilih jenis</option>@foreach (['Pilihan Ganda','Esai'] as $type)<option value="{{ $type }}" @selected(old('jenis_soal', $soal->jenis_soal ?? '') === $type)>{{ $type }}</option>@endforeach</select></div><div class="col-12 col-md-6"><label class="form-label required-label" for="tingkat_kesulitan">Tingkat kesulitan</label><select class="form-select" id="tingkat_kesulitan" name="tingkat_kesulitan" required><option value="">Pilih tingkat</option>@foreach (['Mudah','Sedang','Sulit'] as $level)<option @selected(old('tingkat_kesulitan', $soal->tingkat_kesulitan ?? '') === $level)>{{ $level }}</option>@endforeach</select></div></div>
                </div></div>
                <div class="card mb-3" id="questionChoices"><div class="card-header">Pilihan jawaban</div><div class="card-body">
                    @foreach (['A','B','C','D'] as $letter)<div class="mb-3"><label class="form-label" for="pilihan-{{ $letter }}">Pilihan {{ $letter }}</label><input class="form-control" id="pilihan-{{ $letter }}" name="pilihan[]" value="{{ old('pilihan.'.($loop->index), isset($soal) ? ($soal->pilihan->firstWhere('huruf_opsi', $letter)->teks_opsi ?? '') : '') }}"></div>@endforeach
                    <label class="form-label required-label" for="kunci">Jawaban yang benar</label><select class="form-select" id="kunci" name="kunci"><option value="">Pilih jawaban benar</option>@foreach (['A','B','C','D'] as $letter)<option value="{{ $letter }}" @selected(old('kunci', isset($soal) ? ($soal->pilihan->firstWhere('is_kunci_jawaban', true)->huruf_opsi ?? '') : '') === $letter)>Pilihan {{ $letter }}</option>@endforeach</select>
                </div></div>
                <div class="card"><div class="card-header">Kaitan materi</div><div class="card-body row g-3">
                    <div class="col-12 col-md-6"><label class="form-label required-label" for="mapel_id">Mata pelajaran</label><select class="form-select" name="mapel_id" id="mapel_id" required><option value="">Pilih mata pelajaran</option>@foreach ($mapelList as $mapel)<option value="{{ $mapel->mapel_id }}" @selected(old('mapel_id', $soal->mapel_id ?? '') == $mapel->mapel_id)>{{ $mapel->nama_mapel }}</option>@endforeach</select></div>
                    <div class="col-12 col-md-6"><label class="form-label" for="bab_materi">Bab / topik</label><input class="form-control" name="bab_materi" id="bab_materi" value="{{ old('bab_materi', $soal->bab_materi ?? '') }}" placeholder="Contoh: Pecahan"></div>
                    <div class="col-12 d-flex gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-2"></i>Simpan soal</button><a href="{{ route('bank-soal.index') }}" class="btn btn-outline-secondary">Batal</a></div>
                </div></div>
            </form>
        </div>
        <div class="col-12 col-xl-4"><div class="card"><div class="card-header"><i class="bi bi-info-circle me-2"></i>Petunjuk</div><div class="card-body small text-muted"><p>Tulis satu pertanyaan untuk setiap soal.</p><p>Pilih jawaban benar untuk soal pilihan ganda.</p><p>Soal yang disimpan dapat dipilih kembali saat menyusun ulangan atau UTS/SAS.</p></div></div></div>
    </div>
@endsection
