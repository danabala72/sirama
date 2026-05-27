<?php

namespace App\Http\Controllers;

use App\Models\CpLevelKompetensi;
use App\Models\Mahasiswa;
use App\Models\MataKuliahPilihan;
use App\Models\PenilaianCpKompetensi;
use App\Models\PenilaianTransferNonformal;
use App\Models\PenilaianTransferSks;
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

            // mahasiswa yang di-assign ke asesor ini
            ->whereHas('asesors', function ($query) use ($asesorId) {
                $query->where('asesor_id', $asesorId);
            })

            // pastikan ada data MK + CP
            ->whereHas('mataKuliahPilihan.mataKuliah.cps')
            // ->whereHas('mataKuliahPilihan.transferSks.cpmkItems')

            ->with([
                'mataKuliahPilihan' => function ($query) {
                    $query->whereHas('mataKuliah.cps');
                },

                // CP level + penilaian asesor (sudah difilter di controller)
                'mataKuliahPilihan.cpLevels.penilaian' => function ($q) use ($asesorId) {
                    $q->where('asesor_id', $asesorId);
                },

                // transfer SKS + semua penilaian (filter di controller, bukan model)
                'mataKuliahPilihan.transferSks.penilaian' => function ($q) use ($asesorId) {
                    $q->where('asesor_id', $asesorId);
                },

                'user.jurusan'
            ])
            ->get();

        // hitung status kelengkapan
        $mahasiswas->map(function ($mhs) use ($asesorId) {

            $mkList = collect($mhs->mataKuliahPilihan ?? []);

            $mhs->total_mk_pilihan = $mkList->count();

            $mhs->jumlah_belum_dinilai = $mkList->filter(function ($mk) use ($asesorId) {

                // ================= FORMAL =================
                $formalBelum = false;

                if ($mk->transferSks) {

                    $penilaianFormal = $mk->transferSks->penilaian
                        ->where('asesor_id', $asesorId)
                        ->first();

                    $formalBelum =
                        !$penilaianFormal ||
                        is_null($penilaianFormal->kesenjangan) ||
                        is_null($penilaianFormal->hasil) ||
                        is_null($penilaianFormal->catatan_asesor);
                }


                // ================= CP =================
                $cpBelum = true;

                if ($mk->cpLevels->isNotEmpty()) {

                    $cpBelum = !$mk->cpLevels->every(function ($cp) use ($asesorId) {

                        $pCp = $cp->penilaian
                            ->where('asesor_id', $asesorId)
                            ->first();

                        return $pCp &&
                            $pCp->valid == 1 &&
                            $pCp->asli == 1 &&
                            $pCp->terkini == 1 &&
                            $pCp->memadai == 1;
                    });
                }

                return $formalBelum || $cpBelum;
            })->count();

            $mhs->jumlah_sudah_dinilai = $mkList->filter(function ($mk) use ($asesorId) {
                // 1. Cek isi nilai angka Formal (kolom hasil)
                $hasFormalValue = false;
                if ($mk->transferSks) {
                    $pFormal = $mk->transferSks->penilaian
                        ->where('asesor_id', $asesorId)
                        ->first();
                    $hasFormalValue = $pFormal && !is_null($pFormal->hasil);
                }

                // 2. Cek isi nilai angka Non-Formal (kolom nilai)
                $hasNonFormalValue = false;
                if ($mk->transferSksNonFormal) {
                    $pNonFormal = $mk->transferSksNonFormal->penilaian
                        ->where('asesor_id', $asesorId)
                        ->first();
                    $hasNonFormalValue = $pNonFormal && !is_null($pNonFormal->nilai);
                }

                // Kembali true jika salah satu atau kedua nilai sudah diinput oleh asesor
                return $hasFormalValue || $hasNonFormalValue;
            })->count();


            return $mhs;
        });

        return view('asesor.asesmen.index', compact('mahasiswas'));
    }



    public function formalReview($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $asesorId = Auth::user()->asesor->id;

        $pilihanMk = MataKuliahPilihan::with([
            'transferSks.cpmkItems',

            'transferSks.penilaian' => fn($q) => $q->where('asesor_id', $asesorId),

            'transferSksNonFormal.penilaian' => fn($q) => $q->where('asesor_id', $asesorId),

            'mataKuliah.cps',
            'attachment',
            'cpLevels.cp',
            'cpLevels.penilaian' => fn($q) => $q->where('asesor_id', $asesorId),
        ])
            ->where('mahasiswa_id', $id)
            ->where(function ($q) {
                $q->whereHas('transferSks')
                    ->orWhereHas('transferSksNonFormal');
            })
            ->whereHas('mataKuliah.cps')
            ->get();

        return view('asesor.asesmen.review', [
            'mahasiswa'     => $mahasiswa,
            'pilihanMk'     => $pilihanMk,
            'asesorId'      => $asesorId,
        ]);
    }

    public function formalReviewDetail($pilihanMkId)
    {
        $asesorId = Auth::user()->asesor->id;

        // Load relasi lengkap secara mendalam HANYA untuk 1 ID Mata Kuliah Pilihan ini saja
        $mkSpesifik = MataKuliahPilihan::with([
            'mahasiswa',
            'transferSks.cpmkItems',
            'transferSks.penilaian' => fn($q) => $q->where('asesor_id', $asesorId),
            'transferSksNonFormal.penilaian' => fn($q) => $q->where('asesor_id', $asesorId),
            'mataKuliah.cps',
            'attachment',
            'cpLevels.cp',
            'cpLevels.penilaian' => fn($q) => $q->where('asesor_id', $asesorId),
        ])->findOrFail($pilihanMkId);
        $mahasiswa = $mkSpesifik->mahasiswa;

        return view('asesor.asesmen.review-detail', compact('mkSpesifik', 'asesorId', 'mahasiswa'));
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

    public function reviewUpdate(Request $request, $id)
    {
        // 1. Ambil ID Asesor yang sedang login
        $asesorId = Auth::user()->asesor->id;

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
        | VERIFIKASI / CP KOMPETENSI
        |--------------------------------------------------------------------------
        */
            'verif'                                   => 'nullable|array',
            'verif.*'                               => 'nullable|array'
        ];

        $messages = [
            'penilaian.required' => 'Data penilaian formal tidak ditemukan.',
            'penilaian.*.hasil.numeric' => 'Nilai hasil asesmen formal harus berupa angka.',
            'penilaian.*.hasil.min' => 'Nilai minimal asesmen formal adalah 0.',
            'penilaian.*.hasil.max' => 'Nilai maksimal asesmen formal adalah 100.',
            'penilaian_nonformal.required' => 'Data penilaian non formal tidak ditemukan.',
            'penilaian_nonformal.*.nilai.numeric' => 'Nilai hasil asesmen non formal harus berupa angka.',
            'penilaian_nonformal.*.nilai.min' => 'Nilai minimal asesmen non formal adalah 0.',
            'penilaian_nonformal.*.nilai.max' => 'Nilai maksimal asesmen non formal adalah 100.',
        ];

        $validated = $request->validate($rules, $messages);
        try {
            DB::transaction(function () use ($validated, $asesorId, $request) {

                /*

            |--------------------------------------------------------------------------
            | UPDATE FORMAL (Simpan ke Tabel Penilaian Terpisah)
            |--------------------------------------------------------------------------
            */
                foreach (($validated['penilaian'] ?? []) as $transferSksId => $data) {

                    if (!$transferSksId) continue;

                    PenilaianTransferSks::updateOrCreate(
                        [
                            'transfer_sks_id' => $transferSksId,
                            'asesor_id' => $asesorId,
                        ],
                        [
                            'kesenjangan' => $data['kesenjangan'] ?? null,
                            'hasil' => $data['hasil'] ?? null,
                            'catatan_asesor' => $data['catatan_asesor'] ?? null,
                        ]
                    );
                }

                /*

            |--------------------------------------------------------------------------
            | UPDATE NON FORMAL (Simpan ke Tabel Penilaian Terpisah)
            |--------------------------------------------------------------------------
            */
                foreach (($validated['penilaian_nonformal'] ?? []) as $transferNonFormalId => $data) {

                    if (!$transferNonFormalId) continue;

                    PenilaianTransferNonformal::updateOrCreate(
                        [
                            'transfer_nonformal_id' => $transferNonFormalId,
                            'asesor_id'             => $asesorId,
                        ],
                        [
                            'kesenjangan'    => $data['kesenjangan'] ?? null,
                            'nilai'          => $data['nilai'] ?? null,
                            'catatan_asesor' => $data['catatan_asesor'] ?? null,
                        ]
                    );
                }

                /*

            |--------------------------------------------------------------------------
            | VERIFIKASI CP KOMPETENSI (Simpan ke Tabel Penilaian Terpisah)
            |--------------------------------------------------------------------------
            */



                foreach (($validated['verif'] ?? []) as $cpLevelId => $vData) {
                    PenilaianCpKompetensi::updateOrCreate(
                        [
                            'cp_level_kompetensi_id' => $cpLevelId,
                            'asesor_id'              => $asesorId
                        ],
                        [
                            'valid'    => $vData['valid'] ?? 0,
                            'asli'     => $vData['asli'] ?? 0,
                            'terkini'  => $vData['terkini'] ?? 0,
                            'memadai'  => $vData['memadai'] ?? 0,
                        ]
                    );
                }
            });

            return redirect()
                ->route('asesmen.review', $id)
                ->with('success', 'Penilaian dan verifikasi asesmen berhasil disimpan.');
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Data induk ajuan mahasiswa tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
