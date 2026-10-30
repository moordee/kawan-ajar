<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id('guru_id');
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('kata_sandi');
            $table->string('peran')->default('guru');
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id('kelas_id');
            $table->string('nama_kelas');
            $table->string('tingkat', 20);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id('mapel_id');
            $table->string('kode_mapel')->unique();
            $table->string('nama_mapel');
            $table->timestamps();
        });

        Schema::create('siswa', function (Blueprint $table) {
            $table->id('siswa_id');
            $table->string('nama_siswa');
            $table->string('nis')->unique();
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('guru_kelas_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru', 'guru_id')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran', 'mapel_id')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['guru_id', 'kelas_id', 'mapel_id']);
        });

        Schema::create('materi_pelajaran', function (Blueprint $table) {
            $table->id('materi_id');
            $table->string('judul_materi');
            $table->longText('isi_materi');
            $table->string('lampiran_dokumen')->nullable();
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->foreignId('guru_id')->constrained('guru', 'guru_id')->restrictOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->restrictOnDelete();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran', 'mapel_id')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('sesi_pembuatan_soal_ai', function (Blueprint $table) {
            $table->id('sesi_id');
            $table->text('prompt_instruksi');
            $table->unsignedSmallInteger('jumlah_soal_diminta');
            $table->json('jenis_soal_diminta');
            $table->json('tingkat_kesulitan_diminta');
            $table->timestamp('tanggal_sesi')->useCurrent();
            $table->foreignId('guru_id')->constrained('guru', 'guru_id')->restrictOnDelete();
            $table->foreignId('materi_id')->constrained('materi_pelajaran', 'materi_id')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('soal', function (Blueprint $table) {
            $table->id('soal_id');
            $table->longText('teks_soal');
            $table->string('jenis_soal', 30);
            $table->string('tingkat_kesulitan', 20);
            $table->string('bab_materi')->nullable();
            $table->string('sumber_soal', 30);
            $table->text('prompt_yang_digunakan')->nullable();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran', 'mapel_id')->restrictOnDelete();
            $table->foreignId('sesi_id')->nullable()->constrained('sesi_pembuatan_soal_ai', 'sesi_id')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pilihan_jawaban', function (Blueprint $table) {
            $table->id('pilihan_id');
            $table->string('huruf_opsi', 1);
            $table->text('teks_opsi');
            $table->boolean('is_kunci_jawaban')->default(false);
            $table->foreignId('soal_id')->constrained('soal', 'soal_id')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['soal_id', 'huruf_opsi']);
        });

        Schema::create('ulangan', function (Blueprint $table) {
            $table->id('ulangan_id');
            $table->string('judul_ulangan');
            $table->json('jenis_soal_diizinkan');
            $table->boolean('acak_soal')->default(false);
            $table->boolean('acak_opsi')->default(false);
            $table->boolean('tampilkan_hasil')->default(true);
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->foreignId('guru_id')->constrained('guru', 'guru_id')->restrictOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->restrictOnDelete();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran', 'mapel_id')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('ujian_besar', function (Blueprint $table) {
            $table->id('ujian_id');
            $table->string('judul_ujian');
            $table->string('semester', 30);
            $table->unsignedSmallInteger('waktu_ujian');
            $table->unsignedSmallInteger('jumlah_soal');
            $table->boolean('acak_soal')->default(false);
            $table->boolean('acak_pilihan')->default(false);
            $table->json('bobot_nilai_per_bab')->nullable();
            $table->json('bobot_nilai_per_tingkat_kesulitan')->nullable();
            $table->decimal('total_bobot', 7, 2)->default(100);
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->foreignId('guru_id')->constrained('guru', 'guru_id')->restrictOnDelete();
            $table->foreignId('mapel_id')->constrained('mata_pelajaran', 'mapel_id')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('ulangan_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ulangan_id')->constrained('ulangan', 'ulangan_id')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soal', 'soal_id')->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor_urut');
            $table->decimal('bobot_nilai', 7, 2);
            $table->timestamps();
            $table->unique(['ulangan_id', 'soal_id']);
            $table->unique(['ulangan_id', 'nomor_urut']);
        });

        Schema::create('ujian_besar_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_besar_id')->constrained('ujian_besar', 'ujian_id')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soal', 'soal_id')->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor_urut');
            $table->decimal('bobot_nilai', 7, 2);
            $table->timestamps();
            $table->unique(['ujian_besar_id', 'soal_id']);
            $table->unique(['ujian_besar_id', 'nomor_urut']);
        });

        Schema::create('siswa_ulangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa', 'siswa_id')->cascadeOnDelete();
            $table->foreignId('ulangan_id')->constrained('ulangan', 'ulangan_id')->cascadeOnDelete();
            $table->decimal('skor', 7, 2)->nullable();
            $table->timestamp('tanggal_dikerjakan')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id', 'ulangan_id']);
        });

        Schema::create('siswa_ujian_besar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa', 'siswa_id')->cascadeOnDelete();
            $table->foreignId('ujian_besar_id')->constrained('ujian_besar', 'ujian_id')->cascadeOnDelete();
            $table->decimal('skor', 7, 2)->nullable();
            $table->timestamp('tanggal_dikerjakan')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id', 'ujian_besar_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_ujian_besar');
        Schema::dropIfExists('siswa_ulangan');
        Schema::dropIfExists('ujian_besar_soal');
        Schema::dropIfExists('ulangan_soal');
        Schema::dropIfExists('ujian_besar');
        Schema::dropIfExists('ulangan');
        Schema::dropIfExists('pilihan_jawaban');
        Schema::dropIfExists('soal');
        Schema::dropIfExists('sesi_pembuatan_soal_ai');
        Schema::dropIfExists('materi_pelajaran');
        Schema::dropIfExists('guru_kelas_mapel');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('guru');
    }
};
