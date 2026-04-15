<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    protected $table = 'asesor';
    protected $fillable = [
        'user_id',
        'name',
        'jenis_kelamin',
        'no_hp',
        'email',
    ];

    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class);
    }
}
