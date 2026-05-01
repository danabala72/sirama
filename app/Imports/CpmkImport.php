<?php

namespace App\Imports;

use App\Models\CpMataKuliah;
use App\Models\MataKuliah;
use App\Models\MataKuliahSemester;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CpmkImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $mk = MataKuliah::where('kode_mk', trim($row['kode_mk']))->first();

        if ($mk) {
            $semesterAktif = Semester::where('is_active', 1)->first();

            if ($semesterAktif) {
                $mkSemester = MataKuliahSemester::where('mata_kuliah_id', $mk->id)
                    ->where('semester_id', $semesterAktif->id)
                    ->first();

                if ($mkSemester) {
                    // Gunakan updateOrCreate
                    // Parameter 1: Kunci pencarian (Unique constraint)
                    // Parameter 2: Data yang ingin diupdate/disimpan
                    return CpMataKuliah::updateOrCreate(
                        [
                            'mata_kuliah_semester_id' => $mkSemester->id,
                            'indikator_capaian'       => trim($row['indikator_capaian'])
                        ],
                        [
                            // Jika Anda punya kolom lain seperti 'bobot', taruh di sini
                            'updated_at' => now()
                        ]
                    );
                }

                // Opsional: Lempar error jika MK belum dibuka di semester aktif
                throw new \Exception("MK {$mk->nama_mk} belum dibuka di semester aktif.");
            }
        }

        return null;
    }
}
