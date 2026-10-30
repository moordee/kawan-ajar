<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soal extends Model
{
    protected $table = 'soal';

    protected $primaryKey = 'soal_id';

    protected $fillable = ['teks_soal', 'jenis_soal', 'tingkat_kesulitan', 'bab_materi', 'sumber_soal', 'prompt_yang_digunakan', 'mapel_id', 'sesi_id'];

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id', 'mapel_id');
    }

    public function pilihan(): HasMany
    {
        return $this->hasMany(PilihanJawaban::class, 'soal_id', 'soal_id')->orderBy('huruf_opsi');
    }

    public function sesiAi(): BelongsTo
    {
        return $this->belongsTo(SesiPembuatanSoalAi::class, 'sesi_id', 'sesi_id');
    }

    public function ulangan(): BelongsToMany
    {
        return $this->belongsToMany(Ulangan::class, 'ulangan_soal', 'soal_id', 'ulangan_id', 'soal_id', 'ulangan_id')
            ->withPivot(['nomor_urut', 'bobot_nilai'])->withTimestamps();
    }

    public function ujianBesar(): BelongsToMany
    {
        return $this->belongsToMany(UjianBesar::class, 'ujian_besar_soal', 'soal_id', 'ujian_besar_id', 'soal_id', 'ujian_id')
            ->withPivot(['nomor_urut', 'bobot_nilai'])->withTimestamps();
    }
}
