<?php

namespace App\Http\Controllers;

use App\Models\MataKuliahPilihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MataKuliahPilihanController extends Controller
{
    public function store(Request $request)
    {
        $mhs = Auth::user()->mahasiswa->id;
        $messages = [
            'kode_mk.required'      => 'Kode mata kuliah tidak terdeteksi. Silakan pilih ulang.',
            'kode_mk.unique'        => 'Mata kuliah ini sudah ada dalam daftar pilihan Anda.',
            'nama_mk.required'      => 'Nama mata kuliah harus terisi.',
            'sks.required'          => 'Jumlah SKS wajib diisi.',
            'sks.integer'           => 'Format SKS harus berupa angka.',
            'nilai_angka.min'       => 'Nilai angka minimal adalah 0.',
            'nilai_angka.max'       => 'Nilai angka maksimal adalah 100.',
            'nilai_huruf.max'       => 'Nilai huruf tidak boleh lebih dari 5 karakter.',
            'attachment_ids.array'  => 'Format lampiran tidak valid.',
            'attachment_ids.*.exists' => 'Salah satu file bukti tidak ditemukan di database.',
        ];

        $validated = $request->validate([
            'kode_mk' => [
                'required',
                'string',
                Rule::unique('mata_kuliah_pilihan')
                    ->where(function ($query) use ($mhs) {
                        return $query->where('mahasiswa_id', $mhs);
                    }),
            ],
            'nama_mk'        => 'required|string',
            'sks'            => 'required|integer',
            'nilai_huruf'    => 'nullable|string|max:5',
            'nilai_angka'    => 'nullable|integer|min:0|max:100',
            'attachment_ids' => 'nullable|array',
            'attachment_ids.*' => 'exists:attachment,id',
        ], $messages);

        try {
            DB::beginTransaction();

            $mkp = MataKuliahPilihan::create([
                'mahasiswa_id' => $mhs,
                'kode_mk'      => $validated['kode_mk'],
                'nama_mk'      => $validated['nama_mk'],
                'sks'          => $validated['sks'],
                'nilai_huruf'  => isset($validated['nilai_huruf'])
                    ? strtoupper($validated['nilai_huruf'])
                    : null,
                'nilai_angka'  => $validated['nilai_angka'] ?? null,
            ]);

            if (!empty($validated['attachment_ids'])) {
                $mkp->attachment()->sync($validated['attachment_ids']);
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                "Mata kuliah <b>{$validated['kode_mk']}</b> berhasil disimpan!"
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan data.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $item = MataKuliahPilihan::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Mata kuliah berhasil dihapus');
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Data
        $request->validate([
            'mata_kuliah_id' => 'required',
            'sks'            => 'required|numeric',
            'nilai_huruf'    => 'required',
            'nilai_angka'    => 'required|numeric',
        ]);

        // 2. Cari dan Update Data
        $mkPilihan = MataKuliahPilihan::findOrFail($id);
        $mkPilihan->update([
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'sks'            => $request->sks,
            'nilai_huruf'    => $request->nilai_huruf,
            'nilai_angka'    => $request->nilai_angka,
        ]);

        // 3. Update Attachment jika ada (Opsional sesuai data Anda)
        if ($request->has('attachment_ids')) {
            $mkPilihan->attachment()->sync($request->attachment_ids);
        }

        // 4. SOLUSI UTAMA: Gunakan Redirect, BUKAN return view()
        // Ini akan memicu method index() dijalankan ulang sehingga semua variabel ($mataKuliah dll) terisi kembali.
        return redirect()->back()
            ->with('success', 'Data mata kuliah berhasil diperbarui');
    }
}
