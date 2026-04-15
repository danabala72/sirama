<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $table = 'attachment';
    protected $fillable = [
        'id',
        'label',
        'mahasiswa_id',
        'mata_kuliah_id',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size'
    ];

    public function mataKuliah (): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }
}
