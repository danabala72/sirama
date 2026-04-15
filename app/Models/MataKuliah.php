<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'jurusan_id',
        'kode_mk',
        'nama_mk',
        'sks'
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'mata_kuliah_id');
    }

    public function semester(): BelongsToMany
    {
        return $this->belongsToMany(
            Semester::class,
            'mata_kuliah_semester',
            'mata_kuliah_id',
            'semester_id'
        )->withTimestamps();
    }

    public function cps()
    {
        return $this->hasMany(CpMataKuliah::class);
    }
}
