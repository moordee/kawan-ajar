<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MateriPelajaran extends Model
{
    protected $table = 'materi_pelajaran';

    protected $primaryKey = 'materi_id';

    protected $fillable = ['judul_materi', 'isi_materi', 'lampiran_dokumen', 'tanggal_dibuat', 'guru_id', 'kelas_id', 'mapel_id'];

    protected function casts(): array
    {
        return ['tanggal_dibuat' => 'datetime'];
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

    public function sesiAi(): HasMany
    {
        return $this->hasMany(SesiPembuatanSoalAi::class, 'materi_id', 'materi_id');
    }
}
