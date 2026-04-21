<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table= 'mahasiswa';

    protected $fillable = [
        'user_id',
        'name',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'status_perkawinan',
        'kebangsaan',
        'alamat_rumah',
        'kode_pos',
        'no_hp',
        'alamat_kantor',
        'email',
    ];

    public function mataKuliahPilihan(): HasMany
    {
        return $this->hasMany(MataKuliahPilihan::class, 'mahasiswa_id');
    }

    public function asesors()
    {
        return $this->belongsToMany(Asesor::class);
    }
    public function jurusan() 
    {
        return $this->belongsTo(Jurusan::class);
    }
}
