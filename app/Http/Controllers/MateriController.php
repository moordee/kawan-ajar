<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\MateriPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MateriController extends Controller
{
    public function index(Request $request): View
    {
        $materi = MateriPelajaran::with(['kelas', 'mapel'])
            ->where('guru_id', auth()->id())
            ->when($request->filled('cari'), fn ($query) => $query->where('judul_materi', 'like', '%'.$request->string('cari')->toString().'%'))
            ->when($request->filled('kelas_id'), fn ($query) => $query->where('kelas_id', $request->integer('kelas_id')))
            ->latest('tanggal_dibuat')
            ->paginate(10)
            ->withQueryString();

        return view('materi.index', ['materi' => $materi, 'kelasList' => Kelas::orderBy('nama_kelas')->get()]);
    }

    public function create(): View
    {
        return view('materi.form', $this->formData(new MateriPelajaran));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateMateri($request);
        $data['guru_id'] = auth()->id();
        $data['tanggal_dibuat'] = now();
        $data['lampiran_dokumen'] = $this->storeAttachment($request);
        MateriPelajaran::create($data);

        return redirect()->route('materi.index')->with('success', 'Materi berhasil disimpan.');
    }

    public function edit(MateriPelajaran $materi): View
    {
        $this->authorizeOwner($materi);

        return view('materi.form', $this->formData($materi) + ['materi' => $materi]);
    }

    public function update(Request $request, MateriPelajaran $materi): RedirectResponse
    {
        $this->authorizeOwner($materi);
        $data = $this->validateMateri($request);
        $attachment = $this->storeAttachment($request);
        $previousAttachment = $materi->lampiran_dokumen;
        $data['lampiran_dokumen'] = $attachment ?? $materi->lampiran_dokumen;
        $materi->update($data);
        if ($attachment && $previousAttachment && $previousAttachment !== $attachment) {
            Storage::disk('public')->delete($previousAttachment);
        }

        return redirect()->route('materi.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(MateriPelajaran $materi): RedirectResponse
    {
        $this->authorizeOwner($materi);
        if ($materi->sesiAi()->exists()) {
            return back()->withErrors(['materi' => 'Materi ini digunakan sebagai acuan sesi soal AI, sehingga belum dapat dihapus.']);
        }
        $attachment = $materi->lampiran_dokumen;
        $materi->delete();
        if ($attachment) {
            Storage::disk('public')->delete($attachment);
        }

        return redirect()->route('materi.index')->with('success', 'Materi berhasil dihapus.');
    }

    private function formData(MateriPelajaran $materi): array
    {
        return [
            'materi' => $materi,
            'kelasList' => Kelas::where('status_aktif', true)->orderBy('nama_kelas')->get(),
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
        ];
    }

    private function validateMateri(Request $request): array
    {
        return $request->validate([
            'judul_materi' => ['required', 'string', 'max:255'],
            'isi_materi' => ['required', 'string'],
            'kelas_id' => ['required', 'exists:kelas,kelas_id'],
            'mapel_id' => ['required', 'exists:mata_pelajaran,mapel_id'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png', 'max:10240'],
        ], [
            'judul_materi.required' => 'Judul materi wajib diisi.',
            'isi_materi.required' => 'Isi materi wajib diisi.',
            'kelas_id.required' => 'Pilih kelas tujuan.',
            'mapel_id.required' => 'Pilih mata pelajaran.',
            'lampiran.mimes' => 'Lampiran harus berupa PDF, dokumen, presentasi, atau gambar.',
            'lampiran.max' => 'Ukuran lampiran maksimal 10 MB.',
        ]);
    }

    private function storeAttachment(Request $request): ?string
    {
        return $request->file('lampiran')?->store('lampiran-materi', 'public');
    }

    private function authorizeOwner(MateriPelajaran $materi): void
    {
        abort_unless($materi->guru_id === auth()->id(), 403);
    }
}
