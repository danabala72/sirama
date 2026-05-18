<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianTransferNonformal extends Model
{
    protected $table = 'penilaian_transfer_nonformal';

    protected $fillable = [
        'transfer_nonformal_id',
        'asesor_id',
        'kesenjangan',
        'nilai',
        'catatan_asesor'
    ];
}
