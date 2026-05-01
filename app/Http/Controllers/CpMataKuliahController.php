<?php

namespace App\Http\Controllers;

use App\Models\CpMataKuliah;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CpMataKuliahController extends Controller
{
    public function update(Request $request, $id)
    {
        // 1. Validasi input
        $request->validate([
            'indikator_capaian' => 'required|string|min:5',
        ]);

        // 2. Cari data CPMK
        $cpmk = CpMataKuliah::findOrFail($id);

        // 3. Update data
        $cpmk->update([
            'indikator_capaian' => $request->indikator_capaian
        ]);

        // 4. Redirect kembali ke halaman Edit Mata Kuliah
        return back()->with('success', 'Indikator capaian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // 1. Cari data indikator
        $cpmk = CpMataKuliah::findOrFail($id);

        // 2. Lakukan penghapusan
        $cpmk->delete();

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Indikator capaian berhasil dihapus.');
    }

    public function store(Request $request)
    {
        // 1. Ambil ID Semester yang aktif saat ini
        $semesterAktif = Semester::where('is_active', 1)->first();

        $request->validate([
            'mata_kuliah_semester_id' => [
                'required',
                // Gunakan Rule::exists untuk cek kecocokan id DAN semester_id aktif
                Rule::exists('mata_kuliah_semester', 'id')->where(function ($query) use ($semesterAktif) {
                    return $query->where('semester_id', $semesterAktif->id);
                }),
            ],
            'indikator_capaian' => 'required|string|min:1',
        ], [
            'mata_kuliah_semester_id.exists' => 'Indikator hanya boleh ditambahkan pada semester yang sedang aktif.'
        ]);

        CpMataKuliah::create([
            'mata_kuliah_semester_id' => $request->mata_kuliah_semester_id,
            'indikator_capaian'       => $request->indikator_capaian,
        ]);

        return back()->with('success', 'Indikator capaian berhasil ditambahkan.');
    }
}
