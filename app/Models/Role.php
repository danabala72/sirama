<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'id',
        'role'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}

class ROLES
{
    public const ADMIN = 'Admin';
    public const MAHASISWA = 'Mahasiswa';
    public const  ASESOR =  'Asesor';
}
