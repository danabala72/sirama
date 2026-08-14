<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PenilaianTransferNonformal;
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

        $mahasiswas = Mahasiswa::query()
            ->locked()
            ->whereHas('asesors', function ($query) use ($asesorId) {
                $query->where('asesor_id', $asesorId);
            })
            ->whereHas('mataKuliahPilihan')
            ->with([
                'mataKuliahPilihan.transferSksNonFormal.penilaian' => fn ($q) => $q->where('asesor_id', $asesorId),
                'mataKuliahPilihan.attachment',
                'user.jurusan'
            ])
            ->get();

        return view('asesor.asesmen.nonformal.index', compact('mahasiswas', 'asesorId'));
    }

    public function nonFormalReview($id)
    {
        $asesorId = Auth::user()->asesor->id;

        $mhs = Mahasiswa::with([
            'user.jurusan',
            'mataKuliahPilihan.mataKuliah.cps',
            'mataKuliahPilihan.transferSksNonFormal.penilaian' => fn ($q) => $q->where('asesor_id', $asesorId),
            'mataKuliahPilihan.attachment' => function ($query) {
                $query->whereNotIn('label', ['cv', 'pernyataan']);
            }
        ])->findOrFail($id);

        foreach ($mhs->mataKuliahPilihan as $mk) {
            TransferSksNonformal::firstOrCreate([
                'mata_kuliah_pilihan_id' => $mk->id,
            ]);
        }

        return view('asesor.asesmen.nonformal.review', [
            'namaMahasiswa' => $mhs->nama,
            'mhs'           => $mhs,
            'pilihanMk'     => $mhs->mataKuliahPilihan,
            'asesorId'      => $asesorId,
        ]);
    }

    public function nonFormalReviewUpdate(Request $request)
    {
        $asesorId = Auth::user()->asesor->id;

        $rules = [
            'penilaian' => 'required|array',
            'penilaian.*.nilai' => 'required|numeric|min:0|max:100',
            'penilaian.*.kesenjangan' => 'required|string|min:5',
            'penilaian.*.catatan_asesor' => 'required|string|min:5',
        ];

        $messages = [
            'penilaian.required' => 'Data penilaian tidak ditemukan.',
            'penilaian.*.nilai.required' => 'Kolom Nilai wajib diisi angka 0-100.',
            'penilaian.*.nilai.numeric' => 'Nilai Nilai harus berupa angka.',
            'penilaian.*.nilai.min' => 'Nilai minimal adalah 0.',
            'penilaian.*.nilai.max' => 'Nilai maksimal adalah 100.',
            'penilaian.*.kesenjangan.required' => 'Analisis kesenjangan tidak boleh kosong.',
            'penilaian.*.kesenjangan.min' => 'Analisis kesenjangan minimal 5 karakter.',
            'penilaian.*.catatan_asesor.required' => 'Catatan asesor wajib diisi sebagai bukti evaluasi.',
            'penilaian.*.catatan_asesor.min' => 'Catatan asesor minimal 5 karakter.',
        ];

        $request->validate($rules, $messages);

        try {
            $penilaianData = $request->input('penilaian');

            foreach ($penilaianData as $transferNonFormalId => $data) {
                if (! $transferNonFormalId) {
                    continue;
                }

                PenilaianTransferNonformal::updateOrCreate(
                    [
                        'transfer_nonformal_id' => $transferNonFormalId,
                        'asesor_id' => $asesorId,
                    ],
                    [
                        'kesenjangan'    => $data['kesenjangan'] ?? null,
                        'nilai'          => $data['nilai'] ?? null,
                        'catatan_asesor' => $data['catatan_asesor'] ?? null,
                    ]
                );
            }

            return redirect()->back()->with('success', 'Penilaian berhasil diperbarui!');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Gagal: Data penilaian tidak ditemukan di database.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
