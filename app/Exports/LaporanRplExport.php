<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanRplExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $dataLaporan;

    // Menangkap data array dari Controller
    public function __construct(array $dataLaporan)
    {
        $this->dataLaporan = $dataLaporan;
    }

    // Mengubah data menjadi koleksi agar bisa dibaca package
    public function collection()
    {
        return collect($this->dataLaporan);
    }

    // Menentukan Baris Judul Kolom (Header) Excel
    public function headings(): array
    {
        return [
            'NIM MAHASISWA',
            'MK KODE ASAL',
            'MK NAMA ASAL',
            'MK SKS ASAL',
            'MK NILAI HURUF ASAL',
            'MK KODE PNB',
            'MK NILAI HURUF PNB',
            'NILAI INDEKS DIAKUI',
            'MK SKS PNB',
            'MK NAMA PNB'
        ];
    }

    public function map($row): array
    {
        return [
            $row['nim'] ?? '',
            $row['kode_mk_asal'] ?? '',
            $row['nama_mk_asal'] ?? '',
            $row['sks_mk_asal'] ?? '',
            $row['nilai_huruf_asal'] ?? '',
            $row['kode_mk_pnb'] ?? '',
            $row['nilai_huruf_pnb'] ?? '',
            is_numeric($row['index_diakui']) ? number_format((float)$row['index_diakui'], 2, '.', '') : '0.00',
            $row['sks_mk_pnb'] ?? '',
            $row['nama_mk_pnb'] ?? '',
        ];
    }

    // Membuat teks header baris pertama menjadi tebal (Bold)
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
