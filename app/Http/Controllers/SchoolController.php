<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(): View
    {
        $kelasList = Kelas::with(['siswa' => fn ($query) => $query->orderBy('nama_siswa')])
            ->withCount('siswa')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->paginate(12);

        $pengajaran = DB::table('guru_kelas_mapel')
            ->join('kelas', 'kelas.kelas_id', '=', 'guru_kelas_mapel.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.mapel_id', '=', 'guru_kelas_mapel.mapel_id')
            ->where('guru_kelas_mapel.guru_id', auth()->id())
            ->select('guru_kelas_mapel.id', 'guru_kelas_mapel.kelas_id', 'guru_kelas_mapel.mapel_id', 'kelas.nama_kelas', 'mata_pelajaran.nama_mapel')
            ->get()
            ->groupBy('kelas_id');

        return view('sekolah.index', [
            'kelasList' => $kelasList,
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'pengajaran' => $pengajaran,
        ]);
    }

    public function create(): View
    {
        return view('sekolah.kelas-form', ['kelas' => new Kelas]);
    }

    public function store(Request $request): RedirectResponse
    {
        Kelas::create($this->validateKelas($request));

        return redirect()->route('sekolah.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas): View
    {
        return view('sekolah.kelas-form', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($this->validateKelas($request));

        return redirect()->route('sekolah.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->siswa()->exists()) {
            return back()->withErrors(['kelas' => 'Kelas belum dapat dihapus karena masih memiliki siswa. Pindahkan atau hapus data siswa terlebih dahulu.']);
        }
        if (DB::table('materi_pelajaran')->where('kelas_id', $kelas->kelas_id)->exists()
            || DB::table('ulangan')->where('kelas_id', $kelas->kelas_id)->exists()) {
            return back()->withErrors(['kelas' => 'Kelas ini masih digunakan pada materi atau ulangan. Hapus atau pindahkan data tersebut terlebih dahulu.']);
        }

        $kelas->delete();

        return redirect()->route('sekolah.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function storeStudent(Request $request, Kelas $kelas): RedirectResponse
    {
        $data = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:255', 'unique:siswa,nis'],
        ], [
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS ini sudah terdaftar.',
        ]);
        $data['kelas_id'] = $kelas->kelas_id;
        Siswa::create($data);

        return redirect()->route('sekolah.kelas.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function storeTeachingAssignment(Request $request, Kelas $kelas): RedirectResponse
    {
        $data = $request->validate([
            'mapel_id' => ['required', 'exists:mata_pelajaran,mapel_id'],
        ], ['mapel_id.required' => 'Pilih mata pelajaran yang diajarkan di kelas ini.']);

        $exists = DB::table('guru_kelas_mapel')
            ->where('guru_id', auth()->id())
            ->where('kelas_id', $kelas->kelas_id)
            ->where('mapel_id', $data['mapel_id'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['mapel_id' => 'Mata pelajaran ini sudah ditambahkan untuk kelas tersebut.']);
        }

        DB::table('guru_kelas_mapel')->insert([
            'guru_id' => auth()->id(),
            'kelas_id' => $kelas->kelas_id,
            'mapel_id' => $data['mapel_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('sekolah.kelas.index')->with('success', 'Mata pelajaran berhasil ditambahkan ke kelas.');
    }

    public function destroyTeachingAssignment(int $pengajaran): RedirectResponse
    {
        $deleted = DB::table('guru_kelas_mapel')
            ->where('id', $pengajaran)
            ->where('guru_id', auth()->id())
            ->delete();
        abort_if($deleted === 0, 404);

        return redirect()->route('sekolah.kelas.index')->with('success', 'Mata pelajaran berhasil dihapus dari kelas.');
    }

    public function updateStudent(Request $request, Siswa $siswa): RedirectResponse
    {
        $data = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:255', 'unique:siswa,nis,'.$siswa->siswa_id.',siswa_id'],
            'kelas_id' => ['required', 'exists:kelas,kelas_id'],
        ]);
        $siswa->update($data);

        return redirect()->route('sekolah.kelas.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroyStudent(Siswa $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()->route('sekolah.kelas.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    private function validateKelas(Request $request): array
    {
        return $request->validate([
            'nama_kelas' => ['required', 'string', 'max:100'],
            'tingkat' => ['required', 'string', 'max:20'],
            'status_aktif' => ['nullable', 'boolean'],
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'tingkat.required' => 'Pilih tingkat kelas.',
        ]) + ['status_aktif' => $request->boolean('status_aktif')];
    }
}
