<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpMataKuliah extends Model
{
    protected $table = 'cp_mata_kuliah';

     protected $fillable = [
        'mata_kuliah_semester_id',
        'indikator_capaian',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function mkSemester()
    {
        return $this->belongsTo(MataKuliahSemester::class, 'mata_kuliah_semester_id');
    }

    public function levelKompetensi()
    {
        return $this->hasMany(CpLevelKompetensi::class);
    }
}
