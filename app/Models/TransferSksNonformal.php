<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TransferSksNonformal extends Model
{
    protected $table = 'transfer_sks_nonformal';

    protected $fillable = [
        'mata_kuliah_pilihan_id',
    ];

    public function penilaian()
    {
        return $this->hasMany(PenilaianTransferNonformal::class, 'transfer_nonformal_id');
    }

    public function mataKuliahPilihan()
    {
        return $this->belongsTo(MataKuliahPilihan::class, 'mata_kuliah_pilihan_id');
    }
}
