<?php

namespace App\Http\Controllers;

use App\Models\CpLevelKompetensi;
use App\Models\Mahasiswa;
use App\Models\MataKuliahPilihan;
use App\Models\TransferSks;
use App\Models\TransferSksCpmk;
use App\Models\TransferSksNonformal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        $mahasiswas = Mahasiswa::query()
            ->locked()

            ->whereHas('asesors', function ($query) use ($asesorId) {
                $query->where('asesor_id', $asesorId);
            })

            // hanya MK yang punya cp level != 0
            ->whereHas('mataKuliahPilihan.cpLevel', function ($query) {
                $query->where('level_kompetensi', '!=', 0);
            })

            ->whereHas('mataKuliahPilihan.transferSks.cpmkItems')

            ->with([
                'mataKuliahPilihan' => function ($query) {
                    $query->whereHas('cpLevel', function ($q) {
                        $q->where('level_kompetensi', '!=', 0);
                    });
                },

                'mataKuliahPilihan.transferSks' => function ($query) {
                    $query->withCount('cpmkItems');
                },

                'user.jurusan'
            ])

            ->get();

        return view('asesor.asesmen.index', compact('mahasiswas'));
    }


    public function formalReview($id)
    {
        $mahasiswa = Mahasiswa::select('id', 'name')->findOrFail($id);

        $pilihanMk = MataKuliahPilihan::with([
            'transferSks.cpmkItems',
            'mataKuliah.cps',
            'attachment',

            'cpLevel' => function ($query) {
                $query->where('level_kompetensi', '!=', 0)
                    ->with('cp');
            }
        ])
            ->where('mahasiswa_id', $id)
            ->whereHas('transferSks')
            ->get();

        return view('asesor.asesmen.review', [
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
                            'memadai'   => isset($vData['memadai']),
                        ]);
                    }
                }
            });

            return redirect()->route('asesmen.index')
                ->with('success', 'Penilaian dan verifikasi dokumen berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function reviewUpdate(Request $request)
    {
        // dd($request->all());
        $rules = [

            /*
        |--------------------------------------------------------------------------
        | FORMAL
        |--------------------------------------------------------------------------
        */
            'penilaian'                               => 'nullable|array',
            'penilaian.*.kesenjangan'                 => 'nullable|string',
            'penilaian.*.hasil'                       => 'nullable|numeric|min:0|max:100',
            'penilaian.*.catatan_asesor'              => 'nullable|string',

            /*
        |--------------------------------------------------------------------------
        | NON FORMAL
        |--------------------------------------------------------------------------
        */
            'penilaian_nonformal'                                 => 'nullable|array',
            'penilaian_nonformal.*.kesenjangan'                   => 'nullable|string',
            'penilaian_nonformal.*.nilai'                         => 'nullable|numeric|min:0|max:100',
            'penilaian_nonformal.*.catatan_asesor'                => 'nullable|string',

            /*
        |--------------------------------------------------------------------------
        | VERIFIKASI
        |--------------------------------------------------------------------------
        */
            'verif'                     => 'nullable|array',
        ];

        $messages = [

            /*
        |--------------------------------------------------------------------------
        | FORMAL
        |--------------------------------------------------------------------------
        */
            'penilaian.required' =>
            'Data penilaian formal tidak ditemukan.',

            'penilaian.*.hasil.required' =>
            'Kolom hasil asesmen formal wajib diisi.',

            'penilaian.*.hasil.numeric' =>
            'Nilai hasil asesmen formal harus berupa angka.',

            'penilaian.*.hasil.min' =>
            'Nilai minimal asesmen formal adalah 0.',

            'penilaian.*.hasil.max' =>
            'Nilai maksimal asesmen formal adalah 100.',

            'penilaian.*.kesenjangan.required' =>
            'Analisis kesenjangan formal wajib diisi.',

            'penilaian.*.catatan_asesor.required' =>
            'Catatan asesor formal wajib diisi.',

            /*
        |--------------------------------------------------------------------------
        | NON FORMAL
        |--------------------------------------------------------------------------
        */
            'penilaian_nonformal.required' =>
            'Data penilaian non formal tidak ditemukan.',

            'penilaian_nonformal.*.nilai.required' =>
            'Kolom hasil asesmen non formal wajib diisi.',

            'penilaian_nonformal.*.nilai.numeric' =>
            'Nilai hasil asesmen non formal harus berupa angka.',

            'penilaian_nonformal.*.nilai.min' =>
            'Nilai minimal asesmen non formal adalah 0.',

            'penilaian_nonformal.*.nilai.max' =>
            'Nilai maksimal asesmen non formal adalah 100.',

            'penilaian_nonformal.*.kesenjangan.required' =>
            'Analisis kesenjangan non formal wajib diisi.',

            'penilaian_nonformal.*.catatan_asesor.required' =>
            'Catatan asesor non formal wajib diisi.',
        ];

        $validated = $request->validate($rules, $messages);

        try {

            DB::transaction(function () use ($validated) {

                /*
            |--------------------------------------------------------------------------
            | UPDATE FORMAL
            |--------------------------------------------------------------------------
            */
                foreach (($validated['penilaian'] ?? []) as $id => $data) {

                    $transferFormal = TransferSks::findOrFail($id);

                    $transferFormal->update([
                        'kesenjangan'    => $data['kesenjangan'],
                        'hasil'          => $data['hasil'],
                        'catatan_asesor' => $data['catatan_asesor'],
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | UPDATE NON FORMAL
            |--------------------------------------------------------------------------
            */
                foreach (($validated['penilaian_nonformal'] ?? []) as $id => $data) {

                    $transferNonFormal = TransferSksNonformal::find($id);

                    if (!$transferNonFormal) {
                        dd('ID tidak ditemukan', $id);
                    }

                    $transferNonFormal->kesenjangan = $data['kesenjangan'];
                    $transferNonFormal->nilai = $data['nilai'];
                    $transferNonFormal->catatan_asesor = $data['catatan_asesor'];

                    $saved = $transferNonFormal->save();
                }

                /*
            |--------------------------------------------------------------------------
            | VERIFIKASI FORMAL
            |--------------------------------------------------------------------------
            */
                foreach (($validated['verif'] ?? []) as $cpmkId => $vData) {

                    CpLevelKompetensi::where('id', $cpmkId)->update([
                        'valid'    => isset($vData['valid']),
                        'asli'     => isset($vData['asli']),
                        'terkini'  => isset($vData['terkini']),
                        'memadai'  => isset($vData['memadai']),
                    ]);
                }
            });

            return redirect()
                ->route('asesmen.index')
                ->with(
                    'success',
                    'Penilaian dan verifikasi asesmen berhasil disimpan.'
                );
        } catch (ModelNotFoundException $e) {

            return back()->with(
                'error',
                'Data penilaian tidak ditemukan.'
            );
        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'Gagal menyimpan data: ' . $e->getMessage()
            );
        }
    }
}
