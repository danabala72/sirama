<?php

namespace App\Imports;

use App\Models\MataKuliah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CpmkImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $mk = MataKuliah::where('kode_mk', trim($row['kode_mk']))->first();

        if ($mk) {
            return $mk->cps()->updateOrCreate([
                'indikator_capaian' => trim($row['indikator_capaian'])
            ]);
        }

        return null;
    }
}
