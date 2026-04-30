<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Asesor;
use App\Models\Role;
use App\Models\ROLES;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class AsesorImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $role = Role::where('role', ROLES::ASESOR)->first();

            // 1. Sinkronisasi User
            $user = User::updateOrCreate(
                ['username' => $row['username']],
                [
                    'password' => Hash::make($row['password']),
                    'role_id'  => $role->id,
                ]
            );

            // 2. Bersihkan no_hp (jika ada petik dari Excel)
            $no_hp = isset($row['no_hp']) ? str_replace("'", "", $row['no_hp']) : null;

            // 3. Sinkronisasi Profil Asesor
            Asesor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'          => $row['nama_lengkap'],
                    'email'         => $row['email'],
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'no_hp'         => $no_hp,
                ]
            );

            return $user;
        });
    }
    public function onError(Throwable $e)
    {
        throw $e;
    }
}
