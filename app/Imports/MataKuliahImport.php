<?php

namespace App\Imports;

use App\Models\MataKuliah;
use App\Models\Jurusan;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Agar bisa baca header teks
use Throwable;

class MataKuliahImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cari Jurusan berdasarkan Kode (dari Excel)
        $jurusan = Jurusan::where('kode_jurusan', $row['kode_jurusan'])->first();

        // 2. Cari Semester berdasarkan Label (dari Excel)
        $semester = Semester::where('kode', $row['semester'])->first();

        // Jika data referensi tidak ditemukan, baris ini dilewati (skip)
        if (!$jurusan || !$semester) {
            return null;
        }

        // 3. Simpan ke Database
        $mk = MataKuliah::updateOrCreate(
            ['kode_mk' => trim($row['kode_mk'])],
            [
                'jurusan_id'    => $jurusan->id,
                'nama_mk'       => $row['nama_mk'],
                'sks'           => $row['sks'],
                'nilai_minimum' => $row['nilai_minimum'],
                'status'        => 1,
            ]
        );

        $mk->semester()->syncWithoutDetaching([$semester->id]);

        return $mk;
    }

    public function onError(Throwable $e)
    {
        throw $e;
    }
}
