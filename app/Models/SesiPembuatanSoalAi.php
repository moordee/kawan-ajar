<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiPembuatanSoalAi extends Model
{
    protected $table = 'sesi_pembuatan_soal_ai';

    protected $primaryKey = 'sesi_id';

    protected $fillable = ['prompt_instruksi', 'jumlah_soal_diminta', 'jenis_soal_diminta', 'tingkat_kesulitan_diminta', 'tanggal_sesi', 'guru_id', 'materi_id'];

    protected function casts(): array
    {
        return ['jenis_soal_diminta' => 'array', 'tingkat_kesulitan_diminta' => 'array', 'tanggal_sesi' => 'datetime'];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'guru_id');
    }

    public function materi(): BelongsTo
    {
        return $this->belongsTo(MateriPelajaran::class, 'materi_id', 'materi_id');
    }

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'sesi_id', 'sesi_id');
    }
}
