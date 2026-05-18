<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianTransferSks extends Model
{
    protected $table = 'penilaian_transfer_sks';

    protected $fillable = [
        'transfer_sks_id',
        'asesor_id',
        'kesenjangan',
        'hasil',
        'catatan_asesor'
    ];

    public function transferSks()
    {
        return $this->belongsTo(TransferSks::class, 'transfer_sks_id');
    }

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'asesor_id');
    }
}
