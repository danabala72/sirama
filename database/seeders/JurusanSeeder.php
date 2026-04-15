<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusan = [
            'id' => 1,
            'kode_jurusan' => '30',
            'nama_jurusan' => 'Teknik Elektro'
        ];

        Jurusan::create($jurusan);
    }
}
