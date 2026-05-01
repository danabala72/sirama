<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliahSemester extends Model
{
    protected $table = 'mata_kuliah_semester';
    public $incrementing = true;

    protected $fillable = [
        'mata_kuliah_id',
        'semester_id'
    ];

    public function cps()
    {
        return $this->hasMany(CpMataKuliah::class, 'mata_kuliah_semester_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
