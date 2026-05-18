<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianCpKompetensi extends Model
{
    protected $table = 'penilaian_cp_kompetensi';

    protected $fillable = [
        'cp_level_kompetensi_id',
        'asesor_id',
        'valid',
        'asli',
        'terkini',
        'memadai'
    ];
    public function cpLevelKompetensi()
    {
        return $this->belongsTo(CpLevelKompetensi::class, 'cp_level_kompetensi_id');
    }

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'asesor_id');
    }
}
