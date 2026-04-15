<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpLevelKompetensi extends Model
{
    protected $table = 'cp_level_kompetensi';

    protected $fillable = [
        'mata_kuliah_pilihan_id',
        'cp_mata_kuliah_id',
        'level_kompetensi',
    ];

    protected $casts = [
        'level_kompetensi' => 'boolean',
    ];

    public function cp()
    {
        return $this->belongsTo(CpMataKuliah::class, 'cp_mata_kuliah_id');
    }

    public function mataKuliahPilihan()
    {
        return $this->belongsTo(MataKuliahPilihan::class);
    }
}
