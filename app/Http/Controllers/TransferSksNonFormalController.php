<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\TransferSksNonformal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferSksNonFormalController extends Controller
{
    public function asesorIndex()
    {
        $user = Auth::user();
        $asesorId = $user->asesor->id;

        $mahasiswas = Mahasiswa::whereHas('asesors', function ($query) use ($asesorId) {
            $query->where('asesor_id', $asesorId);
        })
            ->whereHas('mataKuliahPilihan')
            ->with([
                'mataKuliahPilihan.transferSksNonFormal',
                'mataKuliahPilihan.attachment',
                'jurusan'
            ])
            ->get();

        return view('asesor.asesmen.nonformal.index', compact('mahasiswas'));
    }

    public function nonFormalReview($id)
    {
        $mhs = Mahasiswa::with([
            'jurusan',
            'mataKuliahPilihan.mataKuliah.cps',
            'mataKuliahPilihan.transferSksNonFormal',
            'mataKuliahPilihan.attachment' => function ($query) {
                $query->whereNotIn('label', ['cv', 'pernyataan']);
            }
        ])->findOrFail($id);

        // Tetap gunakan firstOrCreate untuk memastikan baris penilaian ada
        foreach ($mhs->mataKuliahPilihan as $mk) {
            TransferSksNonformal::firstOrCreate(
                ['mata_kuliah_pilihan_id' => $mk->id],
                ['kesenjangan' => null, 'hasil' => null, 'catatan_asesor' => null]
            );
        }

        return view('asesor.asesmen.nonformal.review', [
            'namaMahasiswa' => $mhs->nama,
            'mhs'           => $mhs,
            'pilihanMk'     => $mhs->mataKuliahPilihan,
        ]);
    }

    public function nonFormalReviewUpdate(Request $request)
    {
        $rules = [
            'penilaian' => 'required|array',
            'penilaian.*.nilai' => 'required|numeric|min:0|max:100', // Wajib diisi
            'penilaian.*.kesenjangan' => 'required|string', // Wajib diisi, minimal 5 karakter
            'penilaian.*.catatan_asesor' => 'required|string', // Wajib diisi, minimal 5 karakter
        ];

        $messages = [
            'penilaian.required' => 'Data penilaian tidak ditemukan.',
            'penilaian.*.nilai.required' => 'Kolom Niali wajib diisi angka 0-100.',
            'penilaian.*.nilai.numeric' => 'Nilai Niali harus berupa angka.',
            'penilaian.*.nilai.min' => 'Nilai minimal adalah 0.',
            'penilaian.*.nilai.max' => 'Nilai maksimal adalah 100.',
            'penilaian.*.kesenjangan.required' => 'Analisis kesenjangan tidak boleh kosong.',
            'penilaian.*.catatan_asesor.required' => 'Catatan asesor wajib diisi sebagai bukti evaluasi.',
        ];


        $request->validate($rules, $messages);

        try {
            $penilaianData = $request->input('penilaian');

            foreach ($penilaianData as $id => $data) {
                $transferNonFormal = TransferSksNonformal::findOrFail($id);

                // Simpan data ke tabel nonformal
                $transferNonFormal->update([
                    'kesenjangan'    => $data['kesenjangan'] ?? null,
                    'nilai'          => $data['nilai'] ?? null,
                    'catatan_asesor' => $data['catatan_asesor'] ?? null,
                ]);
            }

            return redirect()->back()->with('success', 'Penilaian berhasil diperbarui!');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Gagal: Data penilaian tidak ditemukan di database.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
