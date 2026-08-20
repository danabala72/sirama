<?php

namespace App\Imports;

use App\Models\MataKuliah;
use App\Models\Jurusan;
use App\Models\Semester;
use App\Models\Skema;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class MataKuliahImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $jurusan = Jurusan::where('kode_jurusan', $row['kode_jurusan'])->first();
        $semester = Semester::where('kode', $row['semester'])->first();

        if (!$jurusan || !$semester) {
            return null;
        }

        $skemaIds = [];
        if (!empty($row['nama_skema'])) {
            $names = array_map('trim', explode(',', $row['nama_skema']));
            $skemaIds = Skema::where('jurusan_id', $jurusan->id)
                ->whereIn('nama_skema', $names)
                ->pluck('id')
                ->toArray();
        }

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

        if (!empty($skemaIds)) {
            $mk->skema()->sync($skemaIds);
        } else {
            $mk->skema()->detach();
        }

        $mk->semester()->syncWithoutDetaching([$semester->id]);

        return $mk;
    }

    public function onError(Throwable $e)
    {
        throw $e;
    }
}
