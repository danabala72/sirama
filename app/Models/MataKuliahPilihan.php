<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliahPilihan extends Model
{
    protected $table = 'mata_kuliah_pilihan';
    protected $fillable = ['mahasiswa_id', 'kode_mk', 'nama_mk', 'nilai_angka', 'nilai_huruf', 'sks'];

    public function attachment()
    {
        return $this->belongsToMany(
            Attachment::class,
            'mata_kuliah_attachment',
            'mata_kuliah_pilihan_id',
            'attachment_id'
        );
    }


    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function cpLevel()
    {
        return $this->hasMany(CpLevelKompetensi::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'kode_mk', 'kode_mk');
    }
    
    public function transferSks()
    {
        return $this->hasOne(TransferSks::class, 'mata_kuliah_pilihan_id');
    }
    
}
