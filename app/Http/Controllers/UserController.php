<?php

namespace App\Http\Controllers;

use App\Exports\AsesmenExport;
use App\Exports\LaporanRplExport;
use App\Exports\TemplateAsesorExport;
use App\Exports\TemplateMahasiswaExport;
use App\Exports\TemplateUpdateNimExport;
use App\Imports\AsesorImport;
use App\Imports\MahasiswaImport;
use App\Imports\UpdateNimImport;
use App\Models\Asesor;
use App\Models\Mahasiswa;
use App\Models\MataKuliahSemester;
use App\Models\Role;
use App\Models\ROLES;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->role;
        $jurusanId = $user->jurusan_id;

        $mahasiswa = User::query()
            ->whereHas('role', function ($q) {
                $q->where('role', ROLES::MAHASISWA);
            })

            // 🔥 semua role wajib punya jurusan
            ->whereNotNull('jurusan_id')

            // 🔥 filter tambahan hanya untuk AdminJurusan
            ->when($role === 'AdminJurusan', function ($q) use ($jurusanId) {
                $q->where('jurusan_id', $jurusanId);
            })

            ->with([
                'mahasiswa' => function ($query) {
                    $query->with(['mataKuliahPilihan' => function ($q) {
                        $q->whereHas('attachment', function ($subQ) {
                            $subQ->whereIn('label', ['ijazah', 'transkrip']);
                        });
                        $q->withCount(['attachment' => function ($subQ) {
                            $subQ->whereIn('label', ['ijazah', 'transkrip']);
                        }]);
                    }]);
                },
                'asesor',
                'role',
                'jurusan'
            ])
            ->get();
        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function edit(User $user)
    {
        $asesors = Asesor::with('user')->get();
        $selectedAsesors = $user->mahasiswa ? $user->mahasiswa->asesors->pluck('id')->toArray() : [];
        $skemas = \App\Models\Skema::where('jurusan_id', Auth::user()->jurusan_id)->get();
        return view('mahasiswa.edit', compact('user', 'asesors', 'selectedAsesors', 'skemas'));
    }

    public function asesorEdit(User $user)
    {
        $user->load('asesor');

        return view('asesor.edit', compact('user'));
    }

    public function create()
    {
        $skemas = \App\Models\Skema::where('jurusan_id', Auth::user()->jurusan_id)->get();
        return view('mahasiswa.create', compact('skemas'));
    }

    public function asesorCreate()
    {
        return view('asesor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'skema_id' => 'nullable|exists:skema,id',
        ]);

        $roleMhs = Role::where('role', ROLES::MAHASISWA)->first();
        $jurusanId = Auth::user()->jurusan->id;

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $roleMhs->id ?? null,
            'jurusan_id' => $jurusanId,
            'skema_id' => $request->skema_id,
        ]);

        return redirect()->route('mahasiswa.index')->with('success', 'User baru berhasil dibuat.');
    }


    public function asesorStore(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',

            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255|unique:asesor,email',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        $role = Role::where('role', ROLES::ASESOR)->first();

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $role->id ?? null,
        ]);

        Asesor::create([
            'user_id'       => $user->id,
            'name'          => $request->name,
            'email'         => $request->email,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp'         => $request->no_hp,
        ]);

        return redirect()->route('asesor.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            // 'unique:table,column,except,idColumn'
            'username' => 'required|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'required_with:password|same:password',
            'asesor_ids' => 'nullable|array',
            'asesor_ids.*' => 'exists:asesor,id',
            'skema_id' => 'nullable|exists:skema,id',

        ]);

        $roleMhs = Role::where('role', ROLES::MAHASISWA)->first();
        $user->username = $request->username;
        $user->role_id = $roleMhs->id;
        $user->skema_id = $request->skema_id;

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($user->mahasiswa) {
            $user->mahasiswa->asesors()->sync($request->asesor_ids ?? []);
        }

        return redirect()->route('mahasiswa.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function asesorUpdate(Request $request, User $user)
    {
        $request->validate([
            'username'      => 'required|string|max:255|unique:users,username,' . $user->id,
            'password'      => 'nullable|string|min:6|confirmed',
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255|unique:asesor,email,' . ($user->asesor->id ?? 'NULL'),
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Update Data User
            $user->username = $request->username;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // 2. Update atau Buat Data Asesor
            // Menggunakan updateOrCreate agar jika data asesor belum ada, otomatis dibuatkan
            $user->asesor()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'no_hp'         => $request->no_hp,
                ]
            );
        });

        return redirect()->route('asesor.index')->with('success', 'Data user dan profil asesor berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $loged = Auth::user();
        if ($loged->id == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $user = User::find($id);

        $user->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'User berhasil dihapus.');
    }

    public function asesordestroy($id)
    {
        $loged = Auth::user();
        if ($loged->id == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::find($id);

        $user->delete();

        return redirect()->route('asesor.index')->with('success', 'User berhasil dihapus.');
    }

    public function templateDownload()
    {
        $jurusanId = Auth::user()->jurusan?->id;

        $fileName = 'template_import_mahasiswa_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new TemplateMahasiswaExport($jurusanId), $fileName);
    }

    public function asesorTemplateDownload()
    {
        $fileName = 'template_import_asesor_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new TemplateAsesorExport, $fileName);
    }



    public function userImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal adalah 2MB.'
        ]);

        try {
            // 2. Jalankan proses import
            Excel::import(new MahasiswaImport, $request->file('file'));

            // 3. Beri feedback sukses
            return back()->with('success', 'Data Mahasiswa berhasil diimport dan disinkronkan!');
        } catch (\Exception $e) {
            // 4. Tangani jika ada error (format salah, data duplikat, dll)
            return back()->withErrors(['file' => 'Gagal mengimport data: ' . $e->getMessage()]);
        }
    }

    public function asesorImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new AsesorImport, $request->file('file'));
            return back()->with('success', 'Data user dan asesor berhasil diimport!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal import: ' . $e->getMessage()]);
        }
    }


    public function asesorIndex()
    {
        $asesor = User::whereHas('role', function ($query) {
            $query->where('role', '=', ROLES::ASESOR);
        })->with(['mahasiswa', 'asesor', 'role'])->get();

        return view('asesor.index', compact('asesor'));
    }

    public function laporanForm1($id)
    {
        $mhs = Mahasiswa::findOrFail($id);
        $templatePath = storage_path('app/public/template/FORM 1 (Rincian Data Peserta  Calon peserta).docx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        $templateProcessor->setValues([
            'name'          => $mhs->name,
            'ttl'           => $mhs->tempat_lahir . ', ' . Carbon::parse($mhs->tgl_lahir)->translatedFormat('d F Y'),
            'jenis_kelamin' => $mhs->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            'status'        => $mhs->status_perkawinan,
            'kebangsaan'    => $mhs->kebangsaan,
            'alamat_rumah'  => $mhs->alamat_rumah,
            'kode_pos'      => $mhs->kode_pos,
            'no_hp'         => $mhs->no_hp,
            'alamat_kantor' => $mhs->alamat_kantor,
            'email'         => $mhs->email,

            // Data Pendidikan Terakhir
            'nama_sekolah'  => $mhs->nama_sekolah ?? '-',
            'alamat_sekolah' => $mhs->alamat_sekolah ?? '-',
            'tahun_lulus_sekolah' => $mhs->tahun_lulus_sekolah ?? '-',
            'nama_pt'       => $mhs->nama_pt ?? '-',
            'prodi_pt'      => $mhs->prodi_pt ?? '-',
            'program_pt'    => $mhs->program_pt ?? '-',
            'tahun_lulus_pt' => $mhs->tahun_lulus_pt ?? '-',
        ]);
        $fileName = "FORM 1 (Rincian Data Peserta  Calon peserta)-" . str_replace(' ', '_', $mhs->name) . ".docx";

        // Download langsung tanpa simpan di server secara permanen
        $tempFile = storage_path('framework/cache/temp_word_' . time() . '.docx');
        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function laporanAsesmen(Request $request, $id, ?string $jenis = null)
    {
        $jenis = strtolower($jenis ?? $request->query('jenis', $request->query('type', $request->query('param', 'final'))));
        $jenis = str_replace([' ', '-', '_'], '', $jenis);
        $jenis = $jenis === 'informal' ? 'nonformal' : $jenis;

        $allowedJenis = ['final', 'formal', 'nonformal'];

        abort_unless(in_array($jenis, $allowedJenis, true), 404);

        $mhs = Mahasiswa::with([
            'user',
            'asesors',

            // MK yang dipilih mahasiswa
            'mataKuliahPilihan.mataKuliah',

            // formal
            'mataKuliahPilihan.transferSks.penilaian',

            // nonformal
            'mataKuliahPilihan.transferSksNonFormal.penilaian',
        ])->findOrFail($id);

        // =========================
        // SEMUA MK
        // =========================

        $jurusanId = $mhs->jurusan->id ?? null;

        $mataKuliah = MataKuliahSemester::with([
            'mataKuliah',
            'semester',
        ])
            ->whereHas('semester', function ($q) {
                $q->where('is_active', 1);
            })
            ->whereHas('mataKuliah', function ($query) use ($jurusanId) {
                if ($jurusanId) {
                    $query->where('jurusan_id', $jurusanId);
                }
            })
            ->orderBy('semester_id')
            ->get();

        $rows = [];
        $jurusan = $mhs->jurusan->toArray();
        $asesorNames = $mhs->asesors
            ->take(3)
            ->values()
            ->map(fn($asesor) => $asesor->name)
            ->toArray();

        foreach ($mataKuliah as $index => $mkSemester) {

            $mk = $mkSemester->mataKuliah;


            if (!$mk) {
                continue;
            }



            // cek MK dipilih mahasiswa
            $mkPilihan = $mhs->mataKuliahPilihan
                ->first(function ($item) use ($mk, $jenis) {
                    $hasMatchingMk = $item->kode_mk === $mk->kode_mk;
                    $hasFormal = (bool) $item->transferSks;
                    $hasNonFormal = (bool) $item->transferSksNonFormal;

                    return match ($jenis) {
                        'formal' => $hasMatchingMk && $hasFormal,
                        'nonformal' => $hasMatchingMk && $hasNonFormal,
                        default => $hasMatchingMk && ($hasFormal || $hasNonFormal),
                    };
                });
            // default kosong
            $nilaiMandiri = null;

            $asesor1 = null;
            $asesor2 = null;
            $asesor3 = null;

            $rataRata = null;

            $status = $mk->status;

            // isi hanya jika dipilih
            if ($mkPilihan) {
                $nilaiMandiri = $mkPilihan->nilai_angka;
                [$asesor1, $asesor2, $asesor3, $rataRata] = $this->calculateAssessmentValue($mkPilihan, $jenis);
            }


            $rows[] = [
                'no' => $index + 1,

                'semester' => $mkSemester->semester->label ?? '',

                'kode_mk' => $mk->kode_mk,

                'mata_kuliah' => $mk->nama_mk,

                'nilai_mandiri' => $nilaiMandiri,

                'asesor_1' => $asesor1,
                'asesor_2' => $asesor2,
                'asesor_3' => $asesor3,

                'rata_rata' => $rataRata,

                'minimum' => $mk->nilai_minimum,

                'status' => $status,
            ];
        }
        $namaClean = str_replace(' ', '_', $mhs['name']);

        $timestamp = time();

        $jenisLabel = match ($jenis) {
            'formal' => 'Formal',
            'nonformal' => 'Nonformal',
            default => 'Final',
        };

        $namaFile = "Rekap_Asesmen_" . $jenisLabel . "_" . $namaClean . "_" . $timestamp . ".xlsx";

        return Excel::download(new AsesmenExport($mhs->toArray(), $rows, $jurusan, $jenis, $asesorNames), $namaFile);
    }

    public function laporanMkRpl($id)
    {
        $mahasiswa = Mahasiswa::with(
            'mataKuliahPilihan.mataKuliah',
            'mataKuliahPilihan.transferSks.penilaian',
            'mataKuliahPilihan.transferSksNonFormal.penilaian'
        )->findOrFail($id);

        $laporan = $mahasiswa->mataKuliahPilihan->map(function ($mkPilihan) use ($mahasiswa) {
            [$asesor1, $asesor2, $asesor3, $rataRata] = $this->calculateFinalValue($mkPilihan);

            $huruf = $this->calculateGrade($rataRata);
            $mkPnb = $mkPilihan->mataKuliah;
            $transferSks = $mkPilihan->transferSks;
            $data = [
                'nim'               => $mahasiswa->nim ?? '',
                'kode_mk_asal'      => $transferSks->kode_mk_asal ?? '',
                'nama_mk_asal'      => $transferSks->nama_mk_asal ?? '',
                'sks_mk_asal'       => $mkPilihan->sks ?? '',
                'nilai_huruf_asal'  => $mkPilihan->nilai_huruf ?? '',
                'kode_mk_pnb'       => $mkPnb->kode_mk ?? '',
                'nilai_huruf_pnb'   => $huruf ?? '',
                'index_diakui'      => $rataRata ?? '',
                'sks_mk_pnb'        => $mkPnb->sks ?? '',
                'nama_mk_pnb'       => $mkPnb->nama_mk ?? ''
            ];

            return $data;
        })->toArray();

        $namaMhs = isset($mahasiswa->name) ? str_replace(' ', '_', trim($mahasiswa->name)) : 'data';
        $namaFile = 'Laporan_MK_RPL_' . $namaMhs . '_' . time() . '.xlsx';

        return Excel::download(new LaporanRplExport($laporan), $namaFile);
    }

    public function unlock($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->update([
            'is_editable' => true
        ]);

        return back()->with('success', 'Data berhasil dibuka kembali (unlock).');
    }

    private function calculateFinalValue($mkPilihan): array
    {
        return $this->calculateAssessmentValue($mkPilihan, 'final');
    }

    private function calculateAssessmentValue($mkPilihan, string $jenis = 'final'): array
    {
        if (!$mkPilihan) {
            return [null, null, null, null];
        }

        // =========================================================
        // LOAD DATA
        // =========================================================
        $mkPilihan->loadMissing([
            'transferSks.penilaian',
            'transferSksNonFormal.penilaian',
            'mahasiswa.asesors',
        ]);

        // =========================================================
        // MASTER ASESOR MAHASISWA
        // =========================================================
        $mahasiswa = $mkPilihan->mahasiswa;

        $masterAsesorIds = $mahasiswa?->asesors
            ? $mahasiswa->asesors
            ->pluck('id')
            ->values()
            : collect();

        // =========================================================
        // DATA BARU - FORMAL
        // penilaian_transfer_sks
        //
        // asesor_id + hasil
        // =========================================================
        $formalNilai = collect();

        if ($mkPilihan->transferSks) {
            $formalNilai = $mkPilihan->transferSks
                ->penilaian
                ->filter(
                    fn($p) =>
                    $p->asesor_id !== null &&
                        is_numeric($p->hasil)
                )
                ->keyBy('asesor_id');
        }

        // =========================================================
        // DATA BARU - NONFORMAL
        // penilaian_transfer_nonformal
        //
        // asesor_id + nilai
        // =========================================================
        $nonformalNilai = collect();

        if ($mkPilihan->transferSksNonFormal) {
            $nonformalNilai = $mkPilihan->transferSksNonFormal
                ->penilaian
                ->filter(
                    fn($p) =>
                    $p->asesor_id !== null &&
                        is_numeric($p->nilai)
                )
                ->keyBy('asesor_id');
        }

        // =========================================================
        // LEGACY VALUE
        //
        // Data lama:
        //
        // transfer_sks.hasil
        // transfer_sks_nonformal.nilai
        //
        // Kita simpan sebagai fallback saja.
        // =========================================================
        $legacyFormal = null;
        $legacyNonFormal = null;

        if ($mkPilihan->transferSks) {
            $legacyFormal = is_numeric($mkPilihan->transferSks->hasil ?? null)
                ? (float) $mkPilihan->transferSks->hasil
                : null;
        }

        if ($mkPilihan->transferSksNonFormal) {
            $legacyNonFormal = is_numeric($mkPilihan->transferSksNonFormal->nilai ?? null)
                ? (float) $mkPilihan->transferSksNonFormal->nilai
                : null;
        }

        // =========================================================
        // JIKA TIDAK ADA ASESOR MASTER
        //
        // Untuk data baru, gunakan asesor_id yang ada di penilaian.
        // =========================================================
        if ($masterAsesorIds->isEmpty()) {
            $masterAsesorIds = $formalNilai
                ->keys()
                ->concat($nonformalNilai->keys())
                ->unique()
                ->values();
        }

        // =========================================================
        // JIKA MASIH TIDAK ADA ASESOR
        // =========================================================
        if ($masterAsesorIds->isEmpty()) {
            return [
                null,
                null,
                null,
                null,
            ];
        }

        // =========================================================
        // HASIL
        // =========================================================
        $asesor1 = null;
        $asesor2 = null;
        $asesor3 = null;

        // =========================================================
        // LOOP ASESOR
        // =========================================================
        foreach ($masterAsesorIds->take(3) as $posisi => $asesorId) {

            $pformal = $formalNilai->get($asesorId);
            $pnonformal = $nonformalNilai->get($asesorId);

            $hasFormalBaru =
                $pformal &&
                is_numeric($pformal->hasil);

            $hasNonFormalBaru =
                $pnonformal &&
                is_numeric($pnonformal->nilai);

            // =====================================================
            // DEFAULT
            // =====================================================
            $nilaiAkhir = null;

            // =====================================================
            // FORMAL
            // =====================================================
            if ($jenis === 'formal') {

                if ($hasFormalBaru) {

                    // PRIORITAS 1: DATA BARU
                    $nilaiAkhir = (float) $pformal->hasil;
                } elseif ($legacyFormal !== null) {

                    // PRIORITAS 2: DATA LEGACY
                    //
                    // Legacy tidak mempunyai asesor_id.
                    // Karena itu nilai legacy dianggap sebagai
                    // nilai bersama untuk asesor yang ditampilkan.
                    $nilaiAkhir = $legacyFormal;
                }

                // =====================================================
                // NONFORMAL
                // =====================================================
            } elseif ($jenis === 'nonformal') {

                if ($hasNonFormalBaru) {

                    // PRIORITAS 1: DATA BARU
                    $nilaiAkhir = (float) $pnonformal->nilai;
                } elseif ($legacyNonFormal !== null) {

                    // PRIORITAS 2: DATA LEGACY
                    $nilaiAkhir = $legacyNonFormal;
                }

                // =====================================================
                // FINAL
                // =====================================================
            } else {

                // -------------------------------------------------
                // FORMAL
                // -------------------------------------------------
                $fNilai = $hasFormalBaru
                    ? (float) $pformal->hasil
                    : ($legacyFormal !== null ? $legacyFormal : null);

                // -------------------------------------------------
                // NONFORMAL
                // -------------------------------------------------
                $nfNilai = $hasNonFormalBaru
                    ? (float) $pnonformal->nilai
                    : ($legacyNonFormal !== null ? $legacyNonFormal : null);

                // -------------------------------------------------
                // Keduanya tidak ada
                // -------------------------------------------------
                if ($fNilai === null && $nfNilai === null) {

                    $nilaiAkhir = null;

                    // -------------------------------------------------
                    // Formal tidak ada → nonformal
                    // -------------------------------------------------
                } elseif ($fNilai === null) {

                    $nilaiAkhir = $nfNilai;

                    // -------------------------------------------------
                    // Formal ada → formal + nonformal 10%
                    // -------------------------------------------------
                } else {

                    $nilaiAkhir =
                        $fNilai +
                        (($nfNilai ?? 0) * 0.1);
                }
            }

            // =====================================================
            // ROUND
            // =====================================================
            if ($nilaiAkhir !== null) {

                $nilaiAkhir = (float) round($nilaiAkhir, 2);

                // FINAL MAX 85
                if ($jenis === 'final') {
                    $nilaiAkhir = min($nilaiAkhir, 85);
                }
            }

            // =====================================================
            // MASUKKAN KE POSISI ASESOR
            // =====================================================
            if ($posisi === 0) {
                $asesor1 = $nilaiAkhir;
            } elseif ($posisi === 1) {
                $asesor2 = $nilaiAkhir;
            } elseif ($posisi === 2) {
                $asesor3 = $nilaiAkhir;
            }
        }

        // =========================================================
        // RATA-RATA
        // =========================================================
        $nilaiAsesor = collect([
            $asesor1,
            $asesor2,
            $asesor3,
        ])->filter(fn($value) => $value !== null);

        $rataRata = $nilaiAsesor->isNotEmpty()
            ? (float) round($nilaiAsesor->avg(), 2)
            : null;

        return [
            $asesor1,
            $asesor2,
            $asesor3,
            $rataRata,
        ];
    }

    private function calculateGrade(?float $finalValue): string
    {
        if ($finalValue === null) {
            return '-';
        }

        return match (true) {
            $finalValue >= 81 && $finalValue <= 100 => 'A',
            $finalValue >= 76 && $finalValue < 81   => 'AB',
            $finalValue >= 66 && $finalValue < 76   => 'B',
            $finalValue >= 61 && $finalValue < 66   => 'BC',
            $finalValue >= 56 && $finalValue < 61   => 'C',
            $finalValue >= 41 && $finalValue < 56   => 'D',
            default                                 => 'E',
        };
    }

    public function templateNim()
    {
        $timestamp = time();

        $filename = "template_update_nim_mahasiswa_" . $timestamp . ".xlsx";
        return Excel::download(new TemplateUpdateNimExport,  $filename);
    }

    public function importNim(Request $request)
    {
        // Validasi input file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        // Memanggil class import untuk memproses file Excel yang diunggah
        Excel::import(new UpdateNimImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data NIM berhasil diperbarui!');
    }
}
