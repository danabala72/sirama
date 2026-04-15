<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferSks extends Model
{
    //
    protected $table = 'transfer_sks';

     protected $fillable = [
        'mata_kuliah_pilihan_id',
        'kode_mk_asal',
        'nama_mk_asal',       
    ];


    public function mataKuliahPilihan()
    {
        return $this->belongsTo(MataKuliahPilihan::class, 'mata_kuliah_pilihan_id');
    }

    public function cpmkItems()
    {
        return $this->hasMany(TransferSksCpmk::class, 'transfer_sks_id');
    }

}
