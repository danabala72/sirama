<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    protected $table = 'jurusan';
    protected $fillable = [
        'id',
        'kode_jurusan',
        'nama_jurusan'
    ];

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class, 'jurusan_id');
    }

    public function adminJurusan()
    {
        return $this->hasOne(AdminJurusan::class, 'jurusan_id');
    }
}
