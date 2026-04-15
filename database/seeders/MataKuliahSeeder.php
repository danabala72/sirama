<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataKuliah = [[
            'id' => 1,
            'jurusan_id' => 1,
            'kode_mk' => '20403',
            'nama_mk' => 'Teknik Listrik',
            'sks' => 4
        ],[
            'id'=> 2,
            'jurusan_id' => 1,
            'kode_mk' => '57401',
            'nama_mk' => 'Manajemen Informatika',
            'sks' => 4
        ],
        [
            'id'=> 3,
            'jurusan_id' => 1,
            'kode_mk' => '36304',
            'nama_mk' => 'Teknik Otomasi',
            'sks' => 4
        ],
        [
            'id'=> 4,
            'jurusan_id' => 1,
            'kode_mk' => '56572',
            'nama_mk' => 'Administrasi Jaringan Komputer',
            'sks' => 4
        ],
        [
            'id'=> 5,
            'jurusan_id' => 1,
            'kode_mk' => '58302',
            'nama_mk' => 'Teknologi Rekayasa Perangkat Lunak',
            'sks' => 4
        ]];

        foreach($mataKuliah as $mk){
            MataKuliah::create($mk);
        }
    }
}
