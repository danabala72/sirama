<?php

namespace App\Exports;

use App\Models\Jurusan;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class TemplateMKExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    protected $jurusan;

    public function __construct($jurusanId = null)
    {
        $this->jurusan = $jurusanId ? Jurusan::find($jurusanId) : null;
    }

    /**
     * Menentukan Header Excel
     */
    public function headings(): array
    {
        return [
            'kode_jurusan',
            'kode_mk',
            'nama_mk',
            'semester',
            'sks',
            'nilai_minimum'
        ];
    }

    /**
     * Mengisi baris contoh di bawah header
     */
    public function array(): array
    {
        // Ambil label semester yang sedang aktif
        $semesterAktif = Semester::where('is_active', 1)->first()?->kode ?? '20261';

        return [
            [
                $this->jurusan->kode_jurusan ?? 'Kode Jurusan',
                'MK001',
                'Nama Mata Kuliah',
                $semesterAktif,
                3,
                60
            ]
        ];
    }

    public function title(): string
    {
        return 'Template Import MK';
    }
}
