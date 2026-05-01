<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Semester extends Model
{
    protected $table = 'semester';

    protected $fillable = [
        'id',
        'kode',
        'label',
        'is_active'
    ];

    public function mataKuliah()
    {
        return $this->belongsToMany(MataKuliah::class, 'mata_kuliah_semester')
            ->withPivot('id')
            ->withTimestamps();
    }
}
