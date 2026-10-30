<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ulangan extends Model
{
    protected $table = 'ulangan';

    protected $primaryKey = 'ulangan_id';

    protected $fillable = ['judul_ulangan', 'jenis_soal_diizinkan', 'acak_soal', 'acak_opsi', 'tampilkan_hasil', 'tanggal_dibuat', 'guru_id', 'kelas_id', 'mapel_id'];

    protected function casts(): array
    {
        return ['jenis_soal_diizinkan' => 'array', 'acak_soal' => 'boolean', 'acak_opsi' => 'boolean', 'tampilkan_hasil' => 'boolean', 'tanggal_dibuat' => 'datetime'];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'guru_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id', 'mapel_id');
    }

    public function soal(): BelongsToMany
    {
        return $this->belongsToMany(Soal::class, 'ulangan_soal', 'ulangan_id', 'soal_id', 'ulangan_id', 'soal_id')
            ->withPivot(['nomor_urut', 'bobot_nilai'])->withTimestamps();
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'siswa_ulangan', 'ulangan_id', 'siswa_id', 'ulangan_id', 'siswa_id')
            ->withPivot(['skor', 'tanggal_dikerjakan'])->withTimestamps();
    }
}
