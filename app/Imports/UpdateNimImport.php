<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpdateNimImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!empty($row['username'])) {
            
            $user = User::where('username', (string) $row['username'])->first();

            if ($user) {
                $nimValue = isset($row['nim']) ? trim($row['nim']) : '';

                if ($nimValue === '') {
                    $nimTerakhir = null;
                } elseif (preg_match('/^[0-9]+$/', $nimValue)) {
                    $nimTerakhir = $nimValue;
                } else {
                    return null; 
                }

                $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
                if ($mahasiswa) {
                    $mahasiswa->nim = $nimTerakhir;
                    $mahasiswa->save(); 
                }
            }
        }

        return null;
    }
}
