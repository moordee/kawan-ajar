<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\MateriPelajaran;
use App\Models\SesiPembuatanSoalAi;
use App\Models\Soal;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BankSoalController extends Controller
{
    public function index(Request $request): View
    {
        $soal = Soal::with(['mapel', 'pilihan'])
            ->when($request->filled('mapel_id'), fn ($query) => $query->where('mapel_id', $request->integer('mapel_id')))
            ->when($request->filled('jenis_soal'), fn ($query) => $query->where('jenis_soal', $request->string('jenis_soal')->toString()))
            ->when($request->filled('cari'), fn ($query) => $query->where('teks_soal', 'like', '%'.$request->string('cari')->toString().'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('bank-soal.index', ['soalList' => $soal, 'mapelList' => MataPelajaran::orderBy('nama_mapel')->get()]);
    }

    public function create(): View
    {
        return view('bank-soal.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateQuestion($request);
        $this->saveQuestion($data, 'Dibuat Manual');

        return redirect()->route('bank-soal.index')->with('success', 'Soal berhasil ditambahkan ke bank soal.');
    }

    public function edit(Soal $soal): View
    {
        $soal->load('pilihan');

        return view('bank-soal.form', $this->formData() + ['soal' => $soal]);
    }

    public function update(Request $request, Soal $soal): RedirectResponse
    {
        $data = $this->validateQuestion($request);
        DB::transaction(function () use ($data, $soal) {
            $soal->update(collect($data)->except(['pilihan', 'kunci'])->all());
            $this->syncChoices($soal, $data['pilihan'] ?? [], $data['kunci'] ?? null);
        });

        return redirect()->route('bank-soal.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Soal $soal): RedirectResponse
    {
        $soal->delete();

        return redirect()->route('bank-soal.index')->with('success', 'Soal berhasil dihapus.');
    }

    public function generateWithAi(Request $request): RedirectResponse
    {
        if (! config('services.mistral.key') || config('services.mistral.key') === 'your_mistral_api_key_here') {
            return back()->withErrors(['ai' => 'Kunci Mistral belum tersedia. Tambahkan MISTRAL_API_KEY di berkas .env, lalu coba lagi. Soal tetap bisa dibuat secara manual.'])->withInput();
        }

        $data = $request->validate([
            'materi_id' => ['required', 'exists:materi_pelajaran,materi_id'],
            'jumlah_soal_diminta' => ['required', 'integer', 'min:1', 'max:10'],
            'jenis_soal_diminta' => ['required', 'array', 'min:1'],
            'jenis_soal_diminta.*' => ['required', 'in:Pilihan Ganda,Esai'],
            'tingkat_kesulitan_diminta' => ['required', 'array', 'min:1'],
            'tingkat_kesulitan_diminta.*' => ['required', 'in:Mudah,Sedang,Sulit'],
            'prompt_instruksi' => ['nullable', 'string', 'max:1000'],
        ]);

        $materi = MateriPelajaran::findOrFail($data['materi_id']);
        abort_unless($materi->guru_id === auth()->id(), 403);

        $prompt = 'Buat tepat '.$data['jumlah_soal_diminta'].' soal berbahasa Indonesia untuk siswa sekolah dasar berdasarkan materi berikut. '
            .'Jenis soal yang diizinkan: '.implode(', ', $data['jenis_soal_diminta']).'. Tingkat kesulitan: '.implode(', ', $data['tingkat_kesulitan_diminta']).'. '
            .'Instruksi tambahan: '.($data['prompt_instruksi'] ?? 'Gunakan kalimat sederhana dan sesuai usia siswa.')."\n\nMateri:\n".$materi->isi_materi
            ."\n\nBalas hanya JSON valid berbentuk {\"soal\":[{\"teks_soal\":\"...\",\"jenis_soal\":\"Pilihan Ganda atau Esai\",\"tingkat_kesulitan\":\"Mudah, Sedang, atau Sulit\",\"bab_materi\":\"...\",\"pilihan\":[{\"huruf_opsi\":\"A\",\"teks_opsi\":\"...\",\"is_kunci_jawaban\":true}]}]}. Pilihan berjumlah 4 untuk pilihan ganda; esai tanpa pilihan.";

        try {
            $response = Http::withToken(config('services.mistral.key'))
                ->acceptJson()
                ->timeout(45)
                ->post(config('services.mistral.endpoint'), [
                    'model' => config('services.mistral.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'Anda membantu guru sekolah dasar Indonesia membuat soal yang jelas dan sesuai tingkat usia.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.5,
                    'response_format' => ['type' => 'json_object'],
                ]);
        } catch (ConnectionException $exception) {
            report($exception);

            return back()->withErrors(['ai' => 'Layanan AI tidak dapat dijangkau saat ini. Periksa koneksi internet, lalu coba kembali.'])->withInput();
        }

        if ($response->tooManyRequests()) {
            return back()->withErrors(['ai' => 'Batas penggunaan Mistral tercapai. Tunggu sebentar atau periksa kuota dan batas permintaan akun Mistral, lalu coba kembali.'])->withInput();
        }

        if ($response->failed()) {
            return back()->withErrors(['ai' => 'Layanan pembuat soal belum dapat dihubungi. Periksa kunci dan koneksi Mistral, lalu coba kembali.'])->withInput();
        }

        $content = $response->json('choices.0.message.content');
        $generated = is_string($content) ? json_decode($content, true) : null;
        $questions = $generated['soal'] ?? null;

        if (! is_array($questions) || count($questions) !== (int) $data['jumlah_soal_diminta']) {
            return back()->withErrors(['ai' => 'Jawaban AI belum sesuai format. Silakan coba kembali atau buat soal secara manual.'])->withInput();
        }

        foreach ($questions as $question) {
            if (! is_array($question)
                || ! is_string($question['teks_soal'] ?? null)
                || ! in_array($question['jenis_soal'] ?? null, $data['jenis_soal_diminta'], true)
                || ! in_array($question['tingkat_kesulitan'] ?? null, $data['tingkat_kesulitan_diminta'], true)) {
                return back()->withErrors(['ai' => 'Ada soal AI yang belum sesuai dengan jenis atau tingkat kesulitan pilihan. Silakan coba kembali.'])->withInput();
            }

            $choices = $question['pilihan'] ?? [];
            if (! is_array($choices)
                || ($question['jenis_soal'] === 'Pilihan Ganda' && (count($choices) < 2 || count($choices) > 5))
                || ($question['jenis_soal'] === 'Esai' && count($choices) !== 0)) {
                return back()->withErrors(['ai' => 'Pilihan jawaban soal AI belum lengkap atau tidak sesuai. Silakan coba kembali.'])->withInput();
            }

            if ($question['jenis_soal'] === 'Pilihan Ganda') {
                $letters = [];
                $answerCount = 0;
                foreach ($choices as $choice) {
                    if (! is_array($choice)
                        || ! in_array($choice['huruf_opsi'] ?? null, ['A', 'B', 'C', 'D', 'E'], true)
                        || ! is_string($choice['teks_opsi'] ?? null)) {
                        return back()->withErrors(['ai' => 'Format pilihan jawaban dari AI belum sesuai. Silakan coba kembali.'])->withInput();
                    }
                    $letters[] = $choice['huruf_opsi'];
                    $answerCount += (bool) ($choice['is_kunci_jawaban'] ?? false) ? 1 : 0;
                }
                if (count(array_unique($letters)) !== count($letters) || $answerCount !== 1) {
                    return back()->withErrors(['ai' => 'Pastikan setiap soal pilihan ganda memiliki pilihan dan satu kunci jawaban.'])->withInput();
                }
            }
        }

        DB::transaction(function () use ($questions, $data, $materi, $prompt) {
            $session = SesiPembuatanSoalAi::create([
                'prompt_instruksi' => $data['prompt_instruksi'] ?? 'Buat soal dari materi '.$materi->judul_materi,
                'jumlah_soal_diminta' => $data['jumlah_soal_diminta'],
                'jenis_soal_diminta' => $data['jenis_soal_diminta'],
                'tingkat_kesulitan_diminta' => $data['tingkat_kesulitan_diminta'],
                'tanggal_sesi' => now(),
                'guru_id' => auth()->id(),
                'materi_id' => $materi->materi_id,
            ]);

            foreach ($questions as $question) {
                $soal = Soal::create([
                    'teks_soal' => $question['teks_soal'],
                    'jenis_soal' => $question['jenis_soal'],
                    'tingkat_kesulitan' => $question['tingkat_kesulitan'],
                    'bab_materi' => $question['bab_materi'] ?? $materi->judul_materi,
                    'sumber_soal' => 'Dihasilkan AI',
                    'prompt_yang_digunakan' => $prompt,
                    'mapel_id' => $materi->mapel_id,
                    'sesi_id' => $session->sesi_id,
                ]);
                foreach ($question['pilihan'] ?? [] as $choice) {
                    $soal->pilihan()->create([
                        'huruf_opsi' => $choice['huruf_opsi'],
                        'teks_opsi' => $choice['teks_opsi'],
                        'is_kunci_jawaban' => (bool) ($choice['is_kunci_jawaban'] ?? false),
                    ]);
                }
            }
        });

        return redirect()->route('bank-soal.index')->with('success', 'Soal berhasil dibuat dengan bantuan AI dan ditambahkan ke bank soal.');
    }

    private function formData(): array
    {
        return [
            'mapelList' => MataPelajaran::orderBy('nama_mapel')->get(),
            'materiList' => MateriPelajaran::where('guru_id', auth()->id())->orderBy('judul_materi')->get(),
        ];
    }

    private function validateQuestion(Request $request): array
    {
        $data = $request->validate([
            'teks_soal' => ['required', 'string'],
            'jenis_soal' => ['required', 'in:Pilihan Ganda,Esai'],
            'tingkat_kesulitan' => ['required', 'in:Mudah,Sedang,Sulit'],
            'bab_materi' => ['nullable', 'string', 'max:255'],
            'mapel_id' => ['required', 'exists:mata_pelajaran,mapel_id'],
            'pilihan' => ['required_if:jenis_soal,Pilihan Ganda', 'array'],
            'pilihan.*' => ['nullable', 'string', 'max:1000'],
            'kunci' => ['required_if:jenis_soal,Pilihan Ganda', 'nullable', 'in:A,B,C,D'],
        ], [
            'teks_soal.required' => 'Teks soal wajib diisi.',
            'jenis_soal.required' => 'Pilih jenis soal.',
            'tingkat_kesulitan.required' => 'Pilih tingkat kesulitan.',
            'mapel_id.required' => 'Pilih mata pelajaran.',
            'kunci.required_if' => 'Tentukan jawaban yang benar.',
        ]);

        if (($data['jenis_soal'] ?? null) === 'Pilihan Ganda') {
            $choices = array_values(array_filter($data['pilihan'] ?? [], fn ($value) => filled($value)));
            if (count($choices) < 2) {
                throw ValidationException::withMessages(['pilihan' => 'Isi sedikitnya dua pilihan jawaban.']);
            }
            $answerIndex = ord($data['kunci']) - 65;
            if (! filled($data['pilihan'][$answerIndex] ?? null)) {
                throw ValidationException::withMessages(['kunci' => 'Isi pilihan jawaban yang ditandai sebagai jawaban benar.']);
            }
            $data['pilihan'] = array_slice($data['pilihan'], 0, 4);
        } else {
            $data['pilihan'] = [];
            $data['kunci'] = null;
        }

        return $data;
    }

    private function saveQuestion(array $data, string $source, ?int $sessionId = null): Soal
    {
        return DB::transaction(function () use ($data, $source, $sessionId) {
            $soal = Soal::create([
                'teks_soal' => $data['teks_soal'],
                'jenis_soal' => $data['jenis_soal'],
                'tingkat_kesulitan' => $data['tingkat_kesulitan'],
                'bab_materi' => $data['bab_materi'] ?? null,
                'sumber_soal' => $source,
                'prompt_yang_digunakan' => null,
                'mapel_id' => $data['mapel_id'],
                'sesi_id' => $sessionId,
            ]);
            $this->syncChoices($soal, $data['pilihan'] ?? [], $data['kunci'] ?? null);

            return $soal;
        });
    }

    private function syncChoices(Soal $soal, array $choices, ?string $answer): void
    {
        $soal->pilihan()->delete();
        foreach ($choices as $index => $choice) {
            if (filled($choice)) {
                $letter = chr(65 + $index);
                $soal->pilihan()->create([
                    'huruf_opsi' => $letter,
                    'teks_opsi' => $choice,
                    'is_kunci_jawaban' => $answer === $letter,
                ]);
            }
        }
    }
}
