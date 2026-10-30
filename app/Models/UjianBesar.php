<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UjianBesar extends Model
{
    protected $table = 'ujian_besar';

    protected $primaryKey = 'ujian_id';

    protected $fillable = ['judul_ujian', 'semester', 'waktu_ujian', 'jumlah_soal', 'acak_soal', 'acak_pilihan', 'bobot_nilai_per_bab', 'bobot_nilai_per_tingkat_kesulitan', 'total_bobot', 'tanggal_dibuat', 'guru_id', 'mapel_id'];

    protected function casts(): array
    {
        return ['acak_soal' => 'boolean', 'acak_pilihan' => 'boolean', 'bobot_nilai_per_bab' => 'array', 'bobot_nilai_per_tingkat_kesulitan' => 'array', 'tanggal_dibuat' => 'datetime'];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'guru_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id', 'mapel_id');
    }

    public function soal(): BelongsToMany
    {
        return $this->belongsToMany(Soal::class, 'ujian_besar_soal', 'ujian_besar_id', 'soal_id', 'ujian_id', 'soal_id')
            ->withPivot(['nomor_urut', 'bobot_nilai'])->withTimestamps();
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'siswa_ujian_besar', 'ujian_besar_id', 'siswa_id', 'ujian_id', 'siswa_id')
            ->withPivot(['skor', 'tanggal_dikerjakan'])->withTimestamps();
    }
}
