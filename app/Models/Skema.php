<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skema extends Model
{
    protected $table = 'skema';

    protected $fillable = [
        'jurusan_id',
        'nama_skema',
        'deskripsi',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function mataKuliah(): BelongsToMany
    {
        return $this->belongsToMany(MataKuliah::class, 'mata_kuliah_skema');
    }
}
