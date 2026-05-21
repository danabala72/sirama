<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        $jurusanId = $user->jurusan->id ?? null;

        $mahasiswa = Mahasiswa::with('user')
            ->when($jurusanId, function ($query) use ($jurusanId) {
                $query->whereHas('user', function ($q) use ($jurusanId) {
                    $q->where('jurusan_id', $jurusanId);
                });
            })
            ->get();

        return $mahasiswa;
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
