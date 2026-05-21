<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; 
use Maatwebsite\Excel\Concerns\WithMapping;

class TemplateUpdateNimExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
          return Mahasiswa::with('user')->get(); 
    }
    public function headings(): array
    {
        return [
            'username',
            'nama',
            'nim'
        ];
    }

    public function map($mahasiswa): array
    {
        return [
            $mahasiswa->user->username ?? '', 
            $mahasiswa->name,
            $mahasiswa->nim, 
        ];
    }
}
