<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferSksNonformal extends Model
{
    protected $table = 'transfer_sks_nonformal';

    protected $fillable = [
        'mata_kuliah_pilihan_id',
        'kesenjangan',
        'nilai',
        'catatan_asesor'
    ];

    public function mataKuliahPilihan()
    {
        return $this->belongsTo(MataKuliahPilihan::class, 'mata_kuliah_pilihan_id');
    }
}
