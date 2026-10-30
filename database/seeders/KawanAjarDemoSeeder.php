<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\MateriPelajaran;
use App\Models\PilihanJawaban;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\UjianBesar;
use App\Models\Ulangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KawanAjarDemoSeeder extends Seeder
{
    public function run(): void
    {
        $guru = Guru::updateOrCreate(
            ['email' => 'demo@kawanajar.id'],
            ['nama' => 'Ibu Guru Demo', 'kata_sandi' => Hash::make('Kawanajar#Demo2026'), 'peran' => 'guru'],
        );

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => '4A'],
            ['tingkat' => '4', 'status_aktif' => true],
        );

        $siswaContoh = [
            ['nama_siswa' => 'Alya Putri', 'nis' => 'DEMO4001'],
            ['nama_siswa' => 'Bima Pratama', 'nis' => 'DEMO4002'],
            ['nama_siswa' => 'Citra Lestari', 'nis' => 'DEMO4003'],
            ['nama_siswa' => 'Danu Saputra', 'nis' => 'DEMO4004'],
            ['nama_siswa' => 'Eka Safitri', 'nis' => 'DEMO4005'],
        ];
        foreach ($siswaContoh as $siswa) {
            Siswa::firstOrCreate(['nis' => $siswa['nis']], $siswa + ['kelas_id' => $kelas->kelas_id]);
        }

        $subjects = [
            'MAT4' => 'Matematika',
            'BIN4' => 'Bahasa Indonesia',
            'IPAS4' => 'IPAS',
            'PPKN4' => 'Pendidikan Pancasila',
        ];
        foreach ($subjects as $code => $name) {
            MataPelajaran::firstOrCreate(['kode_mapel' => $code], ['nama_mapel' => $name]);
        }
        $matematika = MataPelajaran::where('kode_mapel', 'MAT4')->firstOrFail();
        $bahasa = MataPelajaran::where('kode_mapel', 'BIN4')->firstOrFail();

        foreach ([$matematika, $bahasa] as $mapel) {
            DB::table('guru_kelas_mapel')->updateOrInsert(
                ['guru_id' => $guru->guru_id, 'kelas_id' => $kelas->kelas_id, 'mapel_id' => $mapel->mapel_id],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }

        $materi = MateriPelajaran::firstOrCreate(
            ['guru_id' => $guru->guru_id, 'judul_materi' => 'Mengenal Pecahan di Sekitar Kita'],
            [
                'isi_materi' => 'Pecahan menunjukkan sebagian dari satu benda utuh. Pembilang adalah angka di atas garis pecahan yang menunjukkan jumlah bagian yang kita ambil. Penyebut adalah angka di bawah garis pecahan yang menunjukkan jumlah seluruh bagian yang sama besar. Contoh: satu potong dari empat potong pizza ditulis 1/4.',
                'tanggal_dibuat' => now(),
                'kelas_id' => $kelas->kelas_id,
                'mapel_id' => $matematika->mapel_id,
            ],
        );

        $questions = [
            [
                'teks_soal' => 'Sebuah kue dipotong menjadi 4 bagian sama besar. Jika Rani mengambil 1 bagian, pecahan yang menunjukkan bagian kue yang diambil Rani adalah ....',
                'tingkat_kesulitan' => 'Mudah',
                'bab_materi' => 'Pecahan sederhana',
                'pilihan' => ['1/2', '1/3', '1/4', '4/1'],
                'kunci' => 'C',
            ],
            [
                'teks_soal' => 'Pada pecahan 3/5, angka yang menunjukkan banyaknya seluruh bagian yang sama besar adalah ....',
                'tingkat_kesulitan' => 'Mudah',
                'bab_materi' => 'Pembilang dan penyebut',
                'pilihan' => ['3', '5', '8', '2'],
                'kunci' => 'B',
            ],
            [
                'teks_soal' => 'Ibu memotong semangka menjadi 8 bagian sama besar. Adik makan 2 bagian. Tuliskan pecahan bagian semangka yang dimakan adik.',
                'tingkat_kesulitan' => 'Sedang',
                'bab_materi' => 'Pecahan dalam kehidupan sehari-hari',
                'pilihan' => [],
                'kunci' => null,
            ],
        ];
        $savedQuestions = [];
        foreach ($questions as $question) {
            $soal = Soal::firstOrCreate(
                ['teks_soal' => $question['teks_soal'], 'mapel_id' => $matematika->mapel_id],
                [
                    'jenis_soal' => $question['pilihan'] ? 'Pilihan Ganda' : 'Esai',
                    'tingkat_kesulitan' => $question['tingkat_kesulitan'],
                    'bab_materi' => $question['bab_materi'],
                    'sumber_soal' => 'Dibuat Manual',
                ],
            );
            foreach ($question['pilihan'] as $index => $choiceText) {
                $letter = chr(65 + $index);
                PilihanJawaban::firstOrCreate(
                    ['soal_id' => $soal->soal_id, 'huruf_opsi' => $letter],
                    ['teks_opsi' => $choiceText, 'is_kunci_jawaban' => $letter === $question['kunci']],
                );
            }
            $savedQuestions[] = $soal;
        }

        $storyQuestion = Soal::firstOrCreate(
            ['teks_soal' => 'Bacalah kalimat berikut: "Setiap pagi, Siti menyiram bunga di halaman rumah." Siapakah yang menyiram bunga?', 'mapel_id' => $bahasa->mapel_id],
            [
                'jenis_soal' => 'Pilihan Ganda',
                'tingkat_kesulitan' => 'Mudah',
                'bab_materi' => 'Menemukan informasi dalam kalimat',
                'sumber_soal' => 'Dibuat Manual',
            ],
        );
        foreach (['Siti', 'Ibu', 'Ayah', 'Kakak'] as $index => $choiceText) {
            $letter = chr(65 + $index);
            PilihanJawaban::firstOrCreate(
                ['soal_id' => $storyQuestion->soal_id, 'huruf_opsi' => $letter],
                ['teks_opsi' => $choiceText, 'is_kunci_jawaban' => $letter === 'A'],
            );
        }

        $ulangan = Ulangan::firstOrCreate(
            ['guru_id' => $guru->guru_id, 'judul_ulangan' => 'Latihan Pecahan — Kelas 4A'],
            [
                'jenis_soal_diizinkan' => ['Pilihan Ganda', 'Esai'],
                'acak_soal' => false,
                'acak_opsi' => false,
                'tampilkan_hasil' => true,
                'tanggal_dibuat' => now(),
                'kelas_id' => $kelas->kelas_id,
                'mapel_id' => $matematika->mapel_id,
            ],
        );
        $ulangan->soal()->syncWithoutDetaching(
            collect($savedQuestions)->mapWithKeys(fn (Soal $soal, int $index) => [
                $soal->soal_id => ['nomor_urut' => $index + 1, 'bobot_nilai' => 1],
            ])->all(),
        );

        $ujian = UjianBesar::firstOrCreate(
            ['guru_id' => $guru->guru_id, 'judul_ujian' => 'Latihan Tengah Semester — Matematika'],
            [
                'semester' => 'Semester 1',
                'waktu_ujian' => 60,
                'jumlah_soal' => 3,
                'acak_soal' => false,
                'acak_pilihan' => false,
                'bobot_nilai_per_bab' => ['Pecahan sederhana' => 50, 'Pembilang dan penyebut' => 50],
                'bobot_nilai_per_tingkat_kesulitan' => ['Mudah' => 2, 'Sedang' => 1],
                'total_bobot' => 100,
                'tanggal_dibuat' => now(),
                'mapel_id' => $matematika->mapel_id,
            ],
        );
        $ujian->soal()->syncWithoutDetaching(
            collect($savedQuestions)->mapWithKeys(fn (Soal $soal, int $index) => [
                $soal->soal_id => ['nomor_urut' => $index + 1, 'bobot_nilai' => 1],
            ])->all(),
        );
    }
}
