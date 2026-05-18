<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CpLevelKompetensi extends Model
{
    protected $table = 'cp_level_kompetensi';

    protected $fillable = [
        'mata_kuliah_pilihan_id',
        'cp_mata_kuliah_id',
        'level_kompetensi'      
    ];

    protected $casts = [
        'level_kompetensi' => 'boolean',
    ];

    public function penilaian()
    {
        return $this->hasMany(PenilaianCpKompetensi::class, 'cp_level_kompetensi_id');
    }

    public function cp()
    {
        return $this->belongsTo(CpMataKuliah::class, 'cp_mata_kuliah_id');
    }

    public function mataKuliahPilihan()
    {
        return $this->belongsTo(MataKuliahPilihan::class);
    }

    public function penilaianAsesorLogin()
    {
        return $this->hasOne(PenilaianCpKompetensi::class, 'cp_level_kompetensi_id')
            ->where('asesor_id', Auth::user()->asesor->id ?? 0);
    }
}
