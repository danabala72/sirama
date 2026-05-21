<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $fillable = [
        'nim',
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
        'nama_sekolah',
        'alamat_sekolah',
        'tahun_lulus_sekolah',
        'nama_pt',
        'prodi_pt',
        'program_pt',
        'tahun_lulus_pt',
        'is_editable'
    ];

    public function mataKuliahPilihan(): HasMany
    {
        return $this->hasMany(MataKuliahPilihan::class, 'mahasiswa_id');
    }

    public function asesors()
    {
        return $this->belongsToMany(Asesor::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getJurusanAttribute()
    {
        return $this->user->jurusan;
    }
    public function scopeLocked($query)
    {
        return $query->where('is_editable', 0);
    }
}
