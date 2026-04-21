<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MataKuliahPilihan;
use App\Models\TransferSks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferSksController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
        ]);

        // 2. Gunakan Transaction untuk keamanan data (Atomic)
        DB::transaction(function () use ($request) {
            foreach ($request->data as $mkPilihanId => $input) {

                if (empty($input['kode_mk_asal']) || empty($input['nama_mk_asal'])) {
                    continue;
                }

                $transfer = \App\Models\TransferSks::updateOrCreate(
                    ['mata_kuliah_pilihan_id' => $mkPilihanId],
                    [
                        'kode_mk_asal' => $input['kode_mk_asal'] ?? null,
                        'nama_mk_asal' => $input['nama_mk_asal'] ?? null,
                    ]
                );

                $transfer->cpmkItems()->delete();

                if (isset($input['item_cpmk']) && is_array($input['item_cpmk'])) {

                    foreach ($input['item_cpmk'] as $poin) {
                        if (!empty($poin)) {
                            $transfer->cpmkItems()->create([
                                'cpmk' => $poin
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Data transfer SKS berhasil diperbarui.');
    }

    public function asesorIndex()
    {
        $user = Auth::user();
        $asesorId = $user->asesor->id;

        $mahasiswas = Mahasiswa::whereHas('asesors', function ($query) use ($asesorId) {
            $query->where('asesor_id', $asesorId);
        })
            ->whereHas('mataKuliahPilihan.transferSks.cpmkItems')
            ->with([
                'mataKuliahPilihan.transferSks' => function ($query) {
                    $query->withCount('cpmkItems');
                },
                'jurusan'
            ])
            ->get();

        return view('asesor.asesmen.formal.index', compact('mahasiswas'));
    }


    public function formalReview($id)
    {
        $mahasiswa = Mahasiswa::select('id', 'name')->findOrFail($id);

        $pilihanMk = MataKuliahPilihan::with(['transferSks.cpmkItems', 'mataKuliah.cps'])
            ->where('mahasiswa_id', $id)
            ->whereHas('transferSks')
            ->get();

        return view('asesor.asesmen.formal.review', [
            'namaMahasiswa' => $mahasiswa->name,
            'pilihanMk'     => $pilihanMk
        ]);
    }

    public function formalReviewUpdate(Request $request)
    {
        $request->validate([
            'penilaian' => 'required|array',
            'penilaian.*.kesenjangan' => 'required|string',
            'penilaian.*.hasil' => 'required|integer|min:1|max:100',
            'penilaian.*.catatan_asesor' => 'required|string',
        ], [
            // Pesan Error Kustom (menggunakan wildcard * agar berlaku untuk semua baris)
            'penilaian.*.kesenjangan.required' => 'Kolom Kesenjangan wajib diisi untuk semua mata kuliah.',
            'penilaian.*.hasil.required' => 'Nilai Hasil wajib diisi (1-100).',
            'penilaian.*.hasil.min' => 'Nilai minimal adalah 1.',
            'penilaian.*.hasil.max' => 'Nilai maksimal adalah 100.',
            'penilaian.*.catatan_asesor.required' => 'Catatan Asesor wajib diisi.',
        ]);

        // 2. Proses Update Kolektif
        try {
            // Gunakan Transaction agar jika satu gagal, semua dibatalkan (opsional tapi disarankan)
            DB::transaction(function () use ($request) {
                foreach ($request->penilaian as $id => $data) {
                    TransferSks::where('id', $id)->update([
                        'kesenjangan'    => $data['kesenjangan'],
                        'hasil'          => $data['hasil'],
                        'catatan_asesor' => $data['catatan_asesor'],
                    ]);
                }
            });

            return redirect()->route('asesmen.formal')
                ->with('success', 'Semua penilaian mata kuliah berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
