<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable
{
    use Notifiable;

    protected $table = 'guru';

    protected $primaryKey = 'guru_id';

    protected $fillable = ['nama', 'email', 'kata_sandi', 'peran'];

    protected $hidden = ['kata_sandi', 'remember_token'];

    protected function casts(): array
    {
        return ['kata_sandi' => 'hashed'];
    }

    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function materi(): HasMany
    {
        return $this->hasMany(MateriPelajaran::class, 'guru_id', 'guru_id');
    }

    public function ulangan(): HasMany
    {
        return $this->hasMany(Ulangan::class, 'guru_id', 'guru_id');
    }

    public function ujianBesar(): HasMany
    {
        return $this->hasMany(UjianBesar::class, 'guru_id', 'guru_id');
    }
}
