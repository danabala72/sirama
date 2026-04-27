<?php

namespace App\Http\Controllers;

use App\Models\CpLevelKompetensi;
use App\Models\Mahasiswa;
use App\Models\MataKuliahPilihan;
use App\Models\TransferSks;
use App\Models\TransferSksCpmk;
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

        $pilihanMk = MataKuliahPilihan::with(['transferSks.cpmkItems', 'mataKuliah.cps', 'attachment', 'cpLevel'])
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
            // Validasi Penilaian Mata Kuliah
            'penilaian' => 'required|array',
            'penilaian.*.kesenjangan' => 'required|string',
            'penilaian.*.hasil' => 'required|integer|min:1|max:100',
            'penilaian.*.catatan_asesor' => 'required|string',

            // Validasi Verifikasi CPMK (V-A-T-M) - Opsional/Nullable karena checkbox
            'verif' => 'nullable|array',
        ], [
            'penilaian.*.kesenjangan.required' => 'Kolom Kesenjangan wajib diisi untuk semua mata kuliah.',
            'penilaian.*.hasil.required' => 'Nilai Hasil wajib diisi (1-100).',
            'penilaian.*.catatan_asesor.required' => 'Catatan Asesor wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. UPDATE DATA MATA KULIAH (TransferSks)
                foreach ($request->penilaian as $id => $data) {
                    TransferSks::where('id', $id)->update([
                        'kesenjangan'    => $data['kesenjangan'],
                        'hasil'          => $data['hasil'],
                        'catatan_asesor' => $data['catatan_asesor'],
                    ]);
                }

                // 2. UPDATE DATA VERIFIKASI CPMK (TransferSksCpmk)
                if ($request->has('verif')) {
                    foreach ($request->verif as $cpmkId => $vData) {
                        // Checkbox jika tidak dicentang tidak masuk ke Request,
                        // maka kita set ke false jika tidak ada di array
                        CpLevelKompetensi::where('id', $cpmkId)->update([
                            'valid'   => isset($vData['valid']),
                            'asli'    => isset($vData['asli']),
                            'terkini' => isset($vData['terkini']),
                            'cukup'   => isset($vData['cukup']),
                        ]);
                    }
                }
            });

            return redirect()->route('asesmen.formal')
                ->with('success', 'Penilaian dan verifikasi dokumen berhasil disimpan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

}
