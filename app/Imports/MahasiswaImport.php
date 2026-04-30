<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\Jurusan;
use App\Models\ROLES;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $role = Role::where('role', ROLES::MAHASISWA)->first();

            $jurusanExcel = Jurusan::where('kode_jurusan', $row['kode_jurusan'])->first();

            $jurusanId = $jurusanExcel ? $jurusanExcel->id : Auth::user()->jurusan?->id;

            if (!$jurusanId) {
                throw new \Exception("Gagal Import: Kode Jurusan tidak ditemukan.");
            }

            // 2. Sinkronisasi User
            $user = User::updateOrCreate(
                ['username' => $row['username']],
                [
                    // Gunakan bcrypt atau Hash::make
                    'password'   => Hash::make($row['password']),
                    'role_id'    => $role->id,
                    'jurusan_id' => $jurusanId, // Masuk ke tabel users
                ]
            );

            // 3. Sinkronisasi Profil Mahasiswa
            Mahasiswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'          => $row['nama_lengkap'],
                    'email'         => $row['email'],
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'tempat_lahir'  => $row['tempat_lahir'] ?? '-',
                    'tgl_lahir'     => $row['tgl_lahir'] ?? now(),
                    'no_hp'         => isset($row['no_hp']) ? str_replace("'", "", $row['no_hp']) : null,
                    'kebangsaan'        => $row['kebangsaan'] ?? 'Indonesia',
                    'alamat_rumah'      => $row['alamat_rumah'] ?? '-',
                    'kode_pos'          => $row['kode_pos'] ?? '-',
                    'alamat_kantor'     => $row['alamat_kantor'] ?? '-',
                    'status_perkawinan' => $row['status_perkawinan'] ?? 'Belum Kawin',
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
