<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Ulangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UlanganController extends Controller
{
    public function index(): View
    {
        return view('ulangan.index', [
            'ulanganList' => Ulangan::with(['kelas', 'mapel'])->where('guru_id', auth()->id())->withCount('soal')->latest('tanggal_dibuat')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('ulangan.form', $this->formData(new Ulangan));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUlangan($request);
        $this->validateQuestionSet($data['soal_ids'], $data['mapel_id'], $data['jenis_soal_diizinkan']);
        $soalIds = $data['soal_ids'];
        unset($data['soal_ids']);
        $data['guru_id'] = auth()->id();
        $data['tanggal_dibuat'] = now();

        $ulangan = DB::transaction(function () use ($data, $soalIds) {
            $ulangan = Ulangan::create($data);
            $ulangan->soal()->attach($this->questionPivots($soalIds));

            return $ulangan;
        });

        return redirect()->route('ulangan.index')->with('success', 'Ulangan berhasil disimpan.');
    }

    public function edit(Ulangan $ulangan): View
    {
        $this->authorizeOwner($ulangan);
        $ulangan->load('soal');

        return view('ulangan.form', $this->formData($ulangan));
    }

    public function update(Request $request, Ulangan $ulangan): RedirectResponse
    {
        $this->authorizeOwner($ulangan);
        $data = $this->validateUlangan($request);
        $this->validateQuestionSet($data['soal_ids'], $data['mapel_id'], $data['jenis_soal_diizinkan']);
        $soalIds = $data['soal_ids'];
        unset($data['soal_ids']);

        DB::transaction(function () use ($ulangan, $data, $soalIds) {
            $ulangan->update($data);
            $ulangan->soal()->sync($this->questionPivots($soalIds));
        });

        return redirect()->route('ulangan.index')->with('success', 'Ulangan berhasil diperbarui.');
    }

    public function destroy(Ulangan $ulangan): RedirectResponse
    {
        $this->authorizeOwner($ulangan);
        $ulangan->delete();

        return redirect()->route('ulangan.index')->with('success', 'Ulangan berhasil dihapus.');
    }

    public function results(Ulangan $ulangan): View
    {
        $this->authorizeOwner($ulangan);
        $students = Siswa::where('kelas_id', $ulangan->kelas_id)->orderBy('nama_siswa')->get();
        $attempts = DB::table('siswa_ulangan')
            ->where('ulangan_id', $ulangan->ulangan_id)
            ->get()
            ->keyBy('siswa_id');

        return view('hasil.assessment', [
            'assessment' => $ulangan,
            'students' => $students,
            'attempts' => $attempts,
            'resultAction' => route('ulangan.results.store', $ulangan),
            'backUrl' => route('ulangan.index'),
            'assessmentName' => $ulangan->judul_ulangan,
            'subjectName' => $ulangan->mapel->nama_mapel,
            'className' => $ulangan->kelas->nama_kelas,
        ]);
    }

    public function storeResult(Request $request, Ulangan $ulangan): RedirectResponse
    {
        $this->authorizeOwner($ulangan);
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
        abort_unless(
            Siswa::where('siswa_id', $data['siswa_id'])->where('kelas_id', $ulangan->kelas_id)->exists(),
            422,
            'Siswa tersebut bukan anggota kelas ulangan ini.',
        );

        DB::table('siswa_ulangan')->updateOrInsert(
            ['siswa_id' => $data['siswa_id'], 'ulangan_id' => $ulangan->ulangan_id],
            ['skor' => $data['skor'], 'tanggal_dikerjakan' => $data['tanggal_dikerjakan'], 'created_at' => now(), 'updated_at' => now()],
        );

        return back()->with('success', 'Nilai siswa berhasil disimpan.');
    }

    private function formData(Ulangan $ulangan): array
    {
        return [
            'ulangan' => $ulangan,
            'kelasList' => Kelas::where('status_aktif', true)->orderBy('nama_kelas')->get(),
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'soalList' => Soal::with('mapel')->whereIn('jenis_soal', ['Pilihan Ganda', 'Esai'])->orderBy('created_at', 'desc')->get(),
        ];
    }

    private function validateUlangan(Request $request): array
    {
        return $request->validate([
            'judul_ulangan' => ['required', 'string', 'max:255'],
            'kelas_id' => ['required', 'exists:kelas,kelas_id'],
            'mapel_id' => ['required', 'exists:mata_pelajaran,mapel_id'],
            'jenis_soal_diizinkan' => ['required', 'array', 'min:1'],
            'jenis_soal_diizinkan.*' => ['in:Pilihan Ganda,Esai'],
            'soal_ids' => ['required', 'array', 'min:1'],
            'soal_ids.*' => ['integer', 'distinct', 'exists:soal,soal_id'],
            'acak_soal' => ['nullable', 'boolean'],
            'acak_opsi' => ['nullable', 'boolean'],
            'tampilkan_hasil' => ['nullable', 'boolean'],
        ], [
            'judul_ulangan.required' => 'Nama ulangan wajib diisi.',
            'kelas_id.required' => 'Pilih kelas yang akan mengikuti ulangan.',
            'mapel_id.required' => 'Pilih mata pelajaran.',
            'jenis_soal_diizinkan.required' => 'Pilih jenis soal yang diizinkan.',
            'soal_ids.required' => 'Pilih sedikitnya satu soal untuk ulangan.',
        ]);
    }

    private function questionPivots(array $ids): array
    {
        $soal = Soal::whereIn('soal_id', $ids)->get(['soal_id', 'jenis_soal']);
        abort_if($soal->count() !== count($ids), 422, 'Beberapa soal tidak ditemukan.');

        return collect($ids)->values()->mapWithKeys(fn ($id, $index) => [$id => ['nomor_urut' => $index + 1, 'bobot_nilai' => 1]])->all();
    }

    private function validateQuestionSet(array $ids, int $mapelId, array $types): void
    {
        $questions = Soal::whereIn('soal_id', $ids)->get(['soal_id', 'mapel_id', 'jenis_soal']);
        if ($questions->count() !== count($ids) || $questions->contains(fn ($question) => (int) $question->mapel_id !== $mapelId || ! in_array($question->jenis_soal, $types, true))) {
            throw ValidationException::withMessages(['soal_ids' => 'Pilih soal dari mata pelajaran dan jenis yang sesuai.']);
        }
    }

    private function authorizeOwner(Ulangan $ulangan): void
    {
        abort_unless($ulangan->guru_id === auth()->id(), 403);
    }
}
