<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mahasiswa::create([
            'user_id' => 2,
            'jurusan_id' => 1,
            'name' => 'Budi',
            'tempat_lahir' => 'Denpasar',
            'tgl_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'status_perkawinan' => 'Belum Kawin',
            'kebangsaan' => 'Indonesia',
            'alamat_rumah' => 'Jl. Mawar No 1',
            'kode_pos' => '80111',
            'no_hp' => '08123456789',
            'alamat_kantor' => 'Kampus',
            'email' => 'budi@example.com',
        ]);
    }
}
