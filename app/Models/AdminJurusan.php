<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminJurusan extends Model
{
    protected $table = 'admin_jurusan';

    protected $fillable = [
        'user_id',
        'nama',
        'jenis_kelamin',
        'email',
        'no_hp'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function jurusan(){
        return $this->user->jurusan();
    }
}
