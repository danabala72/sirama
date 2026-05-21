<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class AsesmenExport implements WithEvents, WithStyles, WithDrawings
{
    protected $mahasiswa;
    protected $rows;
    protected $jurusan;

    // Menggunakan variabel $rows sesuai struktur data Anda
    public function __construct(array $mahasiswa, array $rows, array $jurusan)
    {
        $this->mahasiswa = $mahasiswa;
        $this->rows = $rows;
        $this->jurusan = $jurusan;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo Instansi');
        $drawing->setDescription('Logo pada Form Rekapitulasi');
        $drawing->setPath(storage_path('app/public/images/logo-2.png')); 
        $drawing->setHeight(65); 
        
        // LOGO DISET DI BARIS 7 (CENTER DI KOLOM F)
        $drawing->setCoordinates('F7');
        $drawing->setOffsetX(10); 
        $drawing->setOffsetY(10);  
        
        return $drawing;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(4);   // No
        $sheet->getColumnDimension('B')->setWidth(16);  // Kode Mata kuliah
        $sheet->getColumnDimension('C')->setWidth(35);  // Matakuliah bagian 1
        $sheet->getColumnDimension('D')->setWidth(3);   // Tempat Titik Dua (Sangat kecil)
        $sheet->getColumnDimension('E')->setWidth(10);  // Skor Mandiri
        $sheet->getColumnDimension('F')->setWidth(10);  // Asesor 1
        $sheet->getColumnDimension('G')->setWidth(12);  // Asesor 2
        $sheet->getColumnDimension('H')->setWidth(12);  // Asesor 3
        $sheet->getColumnDimension('I')->setWidth(12);  // Rata-rata Asesmen
        $sheet->getColumnDimension('J')->setWidth(12);  // Skor Minimum
        $sheet->getColumnDimension('K')->setWidth(18);  // Status Rapat Pleno

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Menghitung batas baris akhir secara dinamis berdasarkan total data $rows
                $totalRows = 10 + count($this->rows);
                
                // Kunci default font Calibri 8 untuk seluruh cell aktif
                $sheet->getStyle("A1:K{$totalRows}")->getFont()->setName('Calibri')->setSize(8);

                // Pengaturan Cetak (Printing Optimization)
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0); 

                // --- 1. HEADER UTAMA ---
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', 'FORMULIR REKAPITULASI HASIL ASESMEN UNTUK PROGRAM STUDI');
                $sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                
                $sheet->getRowDimension(1)->setRowHeight(24);

                $jenjangSebelumnya = $this->mahasiswa['nama_pt'] 
                    ? ($this->mahasiswa['program_pt'] . ' ' . $this->mahasiswa['nama_pt']) 
                    : $this->mahasiswa['nama_sekolah'];

                // --- 2. TEMPLATE BIODATA ---
                $labels = [
                    2 => ['label' => 'Nama', 'value' => $this->mahasiswa['name']],
                    3 => ['label' => 'Alamat', 'value' => $this->mahasiswa['alamat_rumah']],
                    4 => ['label' => 'No HP', 'value' => $this->mahasiswa['no_hp']],
                    5 => ['label' => 'Email', 'value' => $this->mahasiswa['email']],
                    6 => ['label' => 'Jenjang Pendidikan sebelumnya', 'value' => $jenjangSebelumnya]
                ];

                foreach ($labels as $row => $data) {
                    $sheet->mergeCells("A{$row}:C{$row}");
                    $sheet->setCellValue("A{$row}", $data['label']);
                    $sheet->setCellValue("D{$row}", ':');
                    
                    $sheet->mergeCells("E{$row}:K{$row}");
                    $sheet->setCellValue("E{$row}", $data['value']); 

                    $sheet->getStyle("A{$row}:K{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    $sheet->getRowDimension($row)->setRowHeight(17);
                }

                $sheet->getStyle('A2:K6')->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                // --- 3. SPACING LOGO (Row 7 diperbesar khusus penampung logo) ---
                $sheet->getRowDimension(7)->setRowHeight(55); 

                // --- 4. HEADER TABEL UTAMA (KINI MULAI DARI BARIS 8 & 9) ---
                $sheet->mergeCells('A8:A9')->setCellValue('A8', 'No');
                $sheet->mergeCells('B8:B9')->setCellValue('B8', 'Kode Mata kuliah');
                
                // Menggabungkan kolom C dan D untuk Matakuliah sesuai dengan CP Prodi
                $sheet->mergeCells('C8:D8')->setCellValue('C8', 'Matakuliah');
                $sheet->mergeCells('C9:D9')->setCellValue('C9', 'sesuai dengan CP Prodi');
                
                $sheet->setCellValue('E8', 'Skor');
                $sheet->setCellValue('E9', 'Mandiri');
                
                $sheet->mergeCells('F8:H8')->setCellValue('F8', 'Hasil asesmen');
                $sheet->setCellValue('F9', 'Asesor RPL 1');
                $sheet->setCellValue('G9', 'Asesor RPL 2');
                $sheet->setCellValue('H9', 'Asesor RPL 3');
                
                $sheet->mergeCells('I8:I9')->setCellValue('I8', "Rata-rata\nAsesmen");
                $sheet->mergeCells('J8:J9')->setCellValue('J8', "Skor\nMinimunm");
                
                $sheet->mergeCells('K8:K9')->setCellValue('K8', "Status\nDiisi hasil rapat pleno");

                // Styling Header Tabel Utama
                $sheet->getStyle('A8:K9')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, 
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);
                $sheet->getRowDimension(8)->setRowHeight(20);
                $sheet->getRowDimension(9)->setRowHeight(20);

                // --- 5. LOOPING DATA MATA KULIAH DARI VARIABEL $rows (Mulai Baris 10) ---
                $startRow = 10;
                foreach ($this->rows as $index => $item) {
                    $currentRow = $startRow + $index;

                    $sheet->setCellValue("A{$currentRow}", $item['no'] ?? ($index + 1));
                    $sheet->setCellValue("B{$currentRow}", $item['kode_mk'] ?? '');
                    
                    // Rekatkan kolom C dan D untuk nama matakuliah agar satu kotak border
                    $sheet->mergeCells("C{$currentRow}:D{$currentRow}");
                    $sheet->setCellValue("C{$currentRow}", $item['mata_kuliah'] ?? '');
                    
                    $sheet->setCellValue("E{$currentRow}", $item['nilai_mandiri'] ?? '');
                    $sheet->setCellValue("F{$currentRow}", $item['asesor_1'] ?? ''); 
                    $sheet->setCellValue("G{$currentRow}", $item['asesor_2'] ?? ''); 
                    $sheet->setCellValue("H{$currentRow}", $item['asesor_3'] ?? '');

                    // Inject Formula Excel Rata-rata otomatis dari kolom F sampai H
                    $sheet->setCellValue("I{$currentRow}", $item['rata_rata'] ?? '');
                    $sheet->setCellValue("J{$currentRow}", $item['minimum'] ?? 66);
                    $sheet->setCellValue("K{$currentRow}", '');

                    // Format border dan alignments data baris
                    $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$currentRow}:J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A{$currentRow}:K{$currentRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getRowDimension($currentRow)->setRowHeight(18);
                }
                $signStartRow = $startRow + count($this->rows) + 2; 

                // Teks Tanda Tangan digabung mulai dari Kolom A sampai D agar simetris di sisi paling kiri
                $sheet->mergeCells("A{$signStartRow}:D{$signStartRow}")->setCellValue("A{$signStartRow}", "Badung,  ".date('Y'));
                
                $row1 = $signStartRow + 1;
                $sheet->mergeCells("A{$row1}:D{$row1}")->setCellValue("A{$row1}", "Jurusan " . $this->jurusan['nama_jurusan']);
                
                $row2 = $signStartRow + 2;
                $sheet->mergeCells("A{$row2}:D{$row2}")->setCellValue("A{$row2}", "Ketua,");
                
                // Ruang tanda tangan fisik (jeda baris)
                $rowName = $signStartRow + 6;
                $sheet->mergeCells("A{$rowName}:D{$rowName}")->setCellValue("A{$rowName}", "(".$this->jurusan['ketua_jurusan'].")");

                // Styling Blok Tanda Tangan Kiri Ujung (Kolom A-D)
                $sheet->getStyle("A{$signStartRow}:D{$rowName}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$rowName}:D{$rowName}")->getFont()->setBold(true);

                // Mengatur tinggi baris blok tanda tangan
                for ($r = $signStartRow; $r <= $rowName; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(16);
                }
            },
        ];
    }
}
