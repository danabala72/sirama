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
            'no_hp',
            // Tambahan Kolom Pendidikan
            'nama_sekolah',
            'alamat_sekolah',
            'tahun_lulus_sekolah',
            'nama_perguruan_tinggi',
            'prodi_pt',
            'program_pt',
            'tahun_lulus_pt'
        ];
    }

    public function array(): array
    {
        // Ambil kode jurusan jika ada
        $kode = $this->jurusanId ? Jurusan::find($this->jurusanId)?->kode_jurusan : 'Kode Jurusan';

        return [
            [
                $kode, 
                'mhs_001', 
                'password123', 
                'Budi Gunawan', 
                'budi@example.com', 
                'L', 
                'Jakarta', 
                '2000-01-01', 
                '08123456789',
                // Contoh Data Pendidikan
                'SMAN 1 Jakarta',
                'Jl. Budi Utomo No. 7',
                '2018',
                'Universitas Terbuka',
                'Akuntansi',
                'D3',
                '2021'
            ],
            [
                $kode, 
                'mhs_002', 
                'password123', 
                'Siti Aminah', 
                'siti@example.com', 
                'P', 
                'Bandung', 
                '2000-02-02', 
                '08123456789',
                // Kosongkan jika ingin mencontohkan nullable
                '', '', '', '', '', '', ''
            ],
        ];
    }
}
