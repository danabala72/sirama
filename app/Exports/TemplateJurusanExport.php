<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TemplateJurusanExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'kode_jurusan',
            'nama_jurusan'
        ];
    }

    public function array(): array
    {
        // Memberikan beberapa baris contoh data jurusan
        return [
            ['30', 'Teknik Elektro'],
            ['31', 'Teknik Informatika'],
            ['32', 'Sistem Informasi'],
        ];
    }
}
