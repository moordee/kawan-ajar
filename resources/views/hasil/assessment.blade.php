@extends('layouts.app')

@section('title', 'Catat Nilai')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <h1>Catat nilai siswa</h1>
            <p class="mb-0">{{ $assessmentName }} · {{ $subjectName }} · {{ $className }}</p>
        </div>
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="alert alert-light border"><i class="bi bi-info-circle me-2"></i>Masukkan nilai 0–100 dan tanggal siswa mengerjakan. Simpan setiap siswa setelah selesai mengisi.</div>
    <div class="card">
        <div class="card-header"><i class="bi bi-people me-2"></i>Daftar siswa <span class="badge badge-soft ms-1">{{ $students->count() }}</span></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Nama siswa</th><th>Kelas</th><th>NIS</th><th style="min-width:120px">Nilai</th><th style="min-width:165px">Tanggal dikerjakan</th><th></th></tr></thead>
                <tbody>
                    @forelse ($students as $student)
                        @php($attempt = $attempts->get($student->siswa_id))
                        <tr>
                            <td class="fw-semibold">{{ $student->nama_siswa }}</td>
                            <td>{{ $student->kelas->nama_kelas }}</td>
                            <td>{{ $student->nis }}</td>
                            <td colspan="3">
                                <form action="{{ $resultAction }}" method="POST" class="row g-2 align-items-center">
                                    @csrf
                                    <input type="hidden" name="siswa_id" value="{{ $student->siswa_id }}">
                                    <div class="col-4 col-md-3"><label class="visually-hidden" for="skor-{{ $student->siswa_id }}">Nilai {{ $student->nama_siswa }}</label><input class="form-control form-control-sm" id="skor-{{ $student->siswa_id }}" type="number" name="skor" min="0" max="100" step="0.01" value="{{ old('siswa_id') == $student->siswa_id ? old('skor') : ($attempt->skor ?? '') }}" placeholder="0–100" required></div>
                                    <div class="col-5 col-md-5"><label class="visually-hidden" for="tanggal-{{ $student->siswa_id }}">Tanggal dikerjakan {{ $student->nama_siswa }}</label><input class="form-control form-control-sm" id="tanggal-{{ $student->siswa_id }}" type="date" name="tanggal_dikerjakan" value="{{ old('siswa_id') == $student->siswa_id ? old('tanggal_dikerjakan') : (isset($attempt->tanggal_dikerjakan) ? \Illuminate\Support\Carbon::parse($attempt->tanggal_dikerjakan)->format('Y-m-d') : now()->format('Y-m-d')) }}" required></div>
                                    <div class="col-3 col-md-4"><button class="btn btn-sm btn-outline-primary w-100" type="submit"><i class="bi bi-check-lg me-1"></i>Simpan</button></div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-person-x"></i>Belum ada siswa untuk dicatat. Tambahkan siswa terlebih dahulu di menu Kelas & Siswa.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
