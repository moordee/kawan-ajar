<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\UjianBesar;
use App\Models\Ulangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UjianBesarController extends Controller
{
    public function index(): View
    {
        return view('ujian-besar.index', [
            'ujianList' => UjianBesar::with('mapel')->where('guru_id', auth()->id())->withCount('soal')->latest('tanggal_dibuat')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('ujian-besar.form', $this->formData(new UjianBesar));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUjian($request);
        if (count($data['soal_ids']) !== (int) $data['jumlah_soal']) {
            throw ValidationException::withMessages(['soal_ids' => 'Jumlah soal yang dipilih harus sama dengan jumlah soal pada informasi ujian.']);
        }
        $this->validateQuestionSet($data['soal_ids'], $data['mapel_id']);
        $soalIds = $data['soal_ids'];
        unset($data['soal_ids']);
        $data['guru_id'] = auth()->id();
        $data['tanggal_dibuat'] = now();

        $ujian = DB::transaction(function () use ($data, $soalIds) {
            $ujian = UjianBesar::create($data);
            $ujian->soal()->attach($this->questionPivots($soalIds));

            return $ujian;
        });

        return redirect()->route('ujian-besar.index')->with('success', 'UTS / SAS berhasil disimpan.');
    }

    public function edit(UjianBesar $ujian_besar): View
    {
        $this->authorizeOwner($ujian_besar);
        $ujian_besar->load('soal');

        return view('ujian-besar.form', $this->formData($ujian_besar));
    }

    public function update(Request $request, UjianBesar $ujian_besar): RedirectResponse
    {
        $this->authorizeOwner($ujian_besar);
        $data = $this->validateUjian($request);
        if (count($data['soal_ids']) !== (int) $data['jumlah_soal']) {
            throw ValidationException::withMessages(['soal_ids' => 'Jumlah soal yang dipilih harus sama dengan jumlah soal pada informasi ujian.']);
        }
        $this->validateQuestionSet($data['soal_ids'], $data['mapel_id']);
        $soalIds = $data['soal_ids'];
        unset($data['soal_ids']);

        DB::transaction(function () use ($ujian_besar, $data, $soalIds) {
            $ujian_besar->update($data);
            $ujian_besar->soal()->sync($this->questionPivots($soalIds));
        });

        return redirect()->route('ujian-besar.index')->with('success', 'UTS / SAS berhasil diperbarui.');
    }

    public function destroy(UjianBesar $ujian_besar): RedirectResponse
    {
        $this->authorizeOwner($ujian_besar);
        $ujian_besar->delete();

        return redirect()->route('ujian-besar.index')->with('success', 'UTS / SAS berhasil dihapus.');
    }

    public function results(): View
    {
        $ulangan = Ulangan::with(['kelas', 'mapel', 'soal'])
            ->withCount(['siswa as jumlah_selesai' => fn ($query) => $query->whereNotNull('siswa_ulangan.tanggal_dikerjakan')])
            ->where('guru_id', auth()->id())
            ->get();
        $ujian = UjianBesar::with(['mapel', 'soal'])
            ->withCount(['siswa as jumlah_selesai' => fn ($query) => $query->whereNotNull('siswa_ujian_besar.tanggal_dikerjakan')])
            ->where('guru_id', auth()->id())
            ->get();

        return view('hasil.index', [
            'ulanganList' => $ulangan,
            'ujianList' => $ujian,
            'jumlahSiswa' => Siswa::count(),
            'jumlahKelas' => Kelas::where('status_aktif', true)->count(),
        ]);
    }

    public function assessmentResults(UjianBesar $ujian_besar): View
    {
        $this->authorizeOwner($ujian_besar);
        $students = Siswa::with('kelas')->orderBy('nama_siswa')->get();
        $attempts = DB::table('siswa_ujian_besar')
            ->where('ujian_besar_id', $ujian_besar->ujian_id)
            ->get()
            ->keyBy('siswa_id');

        return view('hasil.assessment', [
            'assessment' => $ujian_besar,
            'students' => $students,
            'attempts' => $attempts,
            'resultAction' => route('ujian-besar.results.store', $ujian_besar),
            'backUrl' => route('ujian-besar.index'),
            'assessmentName' => $ujian_besar->judul_ujian,
            'subjectName' => $ujian_besar->mapel->nama_mapel,
            'className' => 'Semua kelas',
        ]);
    }

    public function storeResult(Request $request, UjianBesar $ujian_besar): RedirectResponse
    {
        $this->authorizeOwner($ujian_besar);
        $data = $request->validate([
            'siswa_id' => ['required', 'integer', 'exists:siswa,siswa_id'],
            'skor' => ['required', 'numeric', 'min:0', 'max:100'],
            'tanggal_dikerjakan' => ['required', 'date'],
        ], [
            'siswa_id.required' => 'Pilih siswa terlebih dahulu.',
            'skor.required' => 'Nilai wajib diisi.',
            'skor.min' => 'Nilai tidak boleh kurang dari 0.',
            'skor.max' => 'Nilai tidak boleh lebih dari 100.',
            'tanggal_dikerjakan.required' => 'Tanggal pengerjaan wajib diisi.',
        ]);

        DB::table('siswa_ujian_besar')->updateOrInsert(
            ['siswa_id' => $data['siswa_id'], 'ujian_besar_id' => $ujian_besar->ujian_id],
            ['skor' => $data['skor'], 'tanggal_dikerjakan' => $data['tanggal_dikerjakan'], 'created_at' => now(), 'updated_at' => now()],
        );

        return back()->with('success', 'Nilai siswa berhasil disimpan.');
    }

    private function formData(UjianBesar $ujian): array
    {
        return [
            'ujian' => $ujian,
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'soalList' => Soal::with('mapel')->orderByDesc('created_at')->get(),
        ];
    }

    private function validateUjian(Request $request): array
    {
        return $request->validate([
            'judul_ujian' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:30'],
            'mapel_id' => ['required', 'exists:mata_pelajaran,mapel_id'],
            'waktu_ujian' => ['required', 'integer', 'min:1', 'max:600'],
            'jumlah_soal' => ['required', 'integer', 'min:1', 'max:500'],
            'soal_ids' => ['required', 'array', 'min:1'],
            'soal_ids.*' => ['integer', 'distinct', 'exists:soal,soal_id'],
            'acak_soal' => ['nullable', 'boolean'],
            'acak_pilihan' => ['nullable', 'boolean'],
            'total_bobot' => ['required', 'numeric', 'min:0', 'max:1000'],
            'bobot_nilai_per_bab' => ['nullable', 'array'],
            'bobot_nilai_per_bab.*' => ['nullable', 'numeric', 'min:0'],
            'bobot_nilai_per_tingkat_kesulitan' => ['nullable', 'array'],
            'bobot_nilai_per_tingkat_kesulitan.*' => ['nullable', 'numeric', 'min:0'],
        ], [
            'judul_ujian.required' => 'Nama ujian wajib diisi.',
            'semester.required' => 'Pilih semester ujian.',
            'mapel_id.required' => 'Pilih mata pelajaran.',
            'waktu_ujian.required' => 'Lama ujian wajib diisi.',
            'jumlah_soal.required' => 'Jumlah soal wajib diisi.',
            'soal_ids.required' => 'Pilih sedikitnya satu soal untuk ujian.',
        ]);
    }

    private function questionPivots(array $ids): array
    {
        return collect($ids)->values()->mapWithKeys(fn ($id, $index) => [$id => ['nomor_urut' => $index + 1, 'bobot_nilai' => 1]])->all();
    }

    private function validateQuestionSet(array $ids, int $mapelId): void
    {
        $questions = Soal::whereIn('soal_id', $ids)->get(['soal_id', 'mapel_id']);
        if ($questions->count() !== count($ids) || $questions->contains(fn ($question) => (int) $question->mapel_id !== $mapelId)) {
            throw ValidationException::withMessages(['soal_ids' => 'Pilih soal dari mata pelajaran yang sama dengan ujian.']);
        }
    }

    private function authorizeOwner(UjianBesar $ujian): void
    {
        abort_unless($ujian->guru_id === auth()->id(), 403);
    }
}
