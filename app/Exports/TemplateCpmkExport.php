<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class TemplateCpmkExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    protected $kode_mk;

    public function __construct($kode_mk)
    {
        $this->kode_mk = $kode_mk;
    }

    public function headings(): array
    {
        return [
            'kode_mk',
            'indikator_capaian'
        ];
    }

    public function array(): array
    {
        return [
            [
                $this->kode_mk, 
                'Mahasiswa mampu menjelaskan konsep dasar...'
            ]
        ];
    }

    public function title(): string
    {
        return 'Template Import CPMK';
    }
}
