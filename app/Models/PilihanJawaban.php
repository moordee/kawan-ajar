<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilihanJawaban extends Model
{
    protected $table = 'pilihan_jawaban';

    protected $primaryKey = 'pilihan_id';

    protected $fillable = ['huruf_opsi', 'teks_opsi', 'is_kunci_jawaban', 'soal_id'];

    protected function casts(): array
    {
        return ['is_kunci_jawaban' => 'boolean'];
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id', 'soal_id');
    }
}
