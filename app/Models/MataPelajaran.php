<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $primaryKey = 'mapel_id';

    protected $fillable = ['kode_mapel', 'nama_mapel'];

    public function materi(): HasMany
    {
        return $this->hasMany(MateriPelajaran::class, 'mapel_id', 'mapel_id');
    }

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'mapel_id', 'mapel_id');
    }
}
