<?php

namespace App\Http\Controllers;

use App\Models\CpMataKuliah;
use Illuminate\Http\Request;

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
        $request->validate([
            'mata_kuliah_id'    => 'required|exists:mata_kuliah,id',
            'indikator_capaian' => 'required|string|min:1',
        ]);

        // 2. Simpan ke Database
        CpMataKuliah::create([
            'mata_kuliah_id'    => $request->mata_kuliah_id,
            'indikator_capaian' => $request->indikator_capaian,
        ]);

        // 3. Redirect balik dengan pesan sukses
        return back()->with('success', 'Indikator capaian berhasil ditambahkan.');
    }
}
