<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TemplateAsesorExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'username',
            'password',
            'nama_lengkap',
            'email',
            'jenis_kelamin',
            'no_hp'
        ];
    }

    public function array(): array
    {
        return [
            ['asesor_001', 'password123', 'Budi Santoso', 'budi@example.com', 'L', '08123456789'],
            ['asesor_002', 'password123', 'Siti Aminah', 'siti@example.com', 'P', '08123456789'],
        ];
    }
}
