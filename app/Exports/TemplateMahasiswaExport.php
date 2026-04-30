<?php

namespace App\Exports;

use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TemplateMahasiswaExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $jurusanId;

    public function __construct($jurusanId = null)
    {
        $this->jurusanId = $jurusanId;
    }

    public function headings(): array
    {
        return [
            'kode_jurusan',
            'username',
            'password',
            'nama_lengkap',
            'email',
            'jenis_kelamin',
            'tempat_lahir',
            'tgl_lahir',
            'no_hp'
        ];
    }

    public function array(): array
    {
        // Ambil kode jurusan jika ada
        $kode = $this->jurusanId ? Jurusan::find($this->jurusanId)?->kode_jurusan : 'Kode Jurusan';

        return [
            [$kode, 'mhs_001', 'password123', 'Budi Gunawan', 'budi@example.com', 'L', 'Jakarta', '2000-01-01', '08123456789'],
            [$kode, 'mhs_002', 'password123', 'Siti Aminah', 'siti@example.com', 'P', 'Bandung', '2000-02-02', '08123456789'],
        ];
    }
}
