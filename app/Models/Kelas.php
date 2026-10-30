<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $primaryKey = 'kelas_id';

    protected $fillable = ['nama_kelas', 'tingkat', 'status_aktif'];

    protected function casts(): array
    {
        return ['status_aktif' => 'boolean'];
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id', 'kelas_id');
    }
}
