<?php

namespace App\Imports;

use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class JurusanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Hanya sinkronisasi data Jurusan
        return Jurusan::updateOrCreate(
            ['kode_jurusan' => $row['kode_jurusan']],
            ['nama_jurusan' => $row['nama_jurusan']]
        );
    }
    public function onError(Throwable $e)
    {
        throw $e;
    }
}
