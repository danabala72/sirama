<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class SemesterPivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataSemester = [
            ['id' => 1, 'kode' => '20252','label' => '2025/2026', 'is_active' => true],
            ['id' => 2, 'kode' => '20261','label' => '2026/2027', 'is_active' => false],
            ['id' => 3, 'kode' => '20262','label' => '2027/2028', 'is_active' => false],
        ];
        
        foreach($dataSemester as $semester){
            Semester::create($semester);
        }

        $mataKuliah = MataKuliah::all();

        foreach ($mataKuliah as $mk) {
            DB::table('mata_kuliah_semester')->insert([
                'mata_kuliah_id' => $mk->id,
                'semester_id' => 1,
                'created_at' => now(),
            ]);
        }
    }
}
