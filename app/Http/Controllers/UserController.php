<?php

namespace App\Http\Controllers;

use App\Exports\AsesmenExport;
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

            ->with(['mahasiswa', 'asesor', 'role', 'jurusan'])
            ->get();

        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function edit(User $user)
    {
        $asesors = Asesor::with('user')->get();
        $selectedAsesors = $user->mahasiswa ? $user->mahasiswa->asesors->pluck('id')->toArray() : [];
        return view('mahasiswa.edit', compact('user', 'asesors', 'selectedAsesors'));
    }

    public function asesorEdit(User $user)
    {
        $user->load('asesor');

        return view('asesor.edit', compact('user'));
    }

    public function create()
    {
        return view('mahasiswa.create');
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
        ]);

        $roleMhs = Role::where('role', ROLES::MAHASISWA)->first();
        $jurusanId = Auth::user()->jurusan->id;

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $roleMhs->id ?? null,
            'jurusan_id' => $jurusanId
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

        ]);

        $roleMhs = Role::where('role', ROLES::MAHASISWA)->first();
        $user->username = $request->username;
        $user->role_id = $roleMhs->id;

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

    public function laporanAsesmen($id)
    {
        $mhs = Mahasiswa::with([
            'user',

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
        $mataKuliah = MataKuliahSemester::with([
            'mataKuliah',
            'semester',
        ])
            ->whereHas('semester', function ($q) {
                $q->where('is_active', 1);
            })
            ->whereHas('mataKuliah')
            ->orderBy('semester_id')
            ->get();

        $rows = [];
        $jurusan = $mhs->jurusan->toArray();

        foreach ($mataKuliah as $index => $mkSemester) {

            $mk = $mkSemester->mataKuliah;


            if (!$mk) {
                continue;
            }



            // cek MK dipilih mahasiswa
            $mkPilihan = $mhs->mataKuliahPilihan
                ->first(function ($item) use ($mk) {

                    return
                        $item->kode_mk === $mk->kode_mk &&
                        $item->transferSks &&
                        $item->transferSks->cpmkItems->count();
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
                [$asesor1, $asesor2, $asesor3, $rataRata] = $this->calculateFinalValue($mkPilihan);
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

        $namaFile = "Rekap_Asesmen_" . $namaClean . "_" . $timestamp . ".xlsx";

        return Excel::download(new AsesmenExport($mhs->toArray(), $rows, $jurusan), $namaFile);
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
        $formalNilai = collect();
        $nonformalNilai = collect();

        // =========================
        // FORMAL
        // =========================
        if ($mkPilihan?->transferSks) {
            $formalNilai = $mkPilihan
                ->transferSks
                ->penilaian
                ->pluck('hasil')
                ->filter(fn($v) => $v !== null)
                ->take(3)
                ->values();
        }

        // =========================
        // NONFORMAL
        // =========================
        if ($mkPilihan?->transferSksNonFormal) {
            $nonformalNilai = $mkPilihan
                ->transferSksNonFormal
                ->penilaian
                ->pluck('nilai')
                ->filter(fn($v) => $v !== null)
                ->take(3)
                ->values();
        }

        // =========================
        // FINAL PER ASESOR
        // =========================

        // --- ASESOR 1 ---
        $bonus1 = isset($nonformalNilai[0]) ? ($nonformalNilai[0] * 0.1) : 0;
        $asesor1 = isset($formalNilai[0])
            ? (int) min(round($formalNilai[0] + $bonus1), 85)
            : null;

        // --- ASESOR 2 ---
        $bonus2 = isset($nonformalNilai[1]) ? ($nonformalNilai[1] * 0.1) : 0;
        $asesor2 = isset($formalNilai[1])
            ? (int) min(round($formalNilai[1] + $bonus2), 85)
            : null;

        // --- ASESOR 3 ---
        $bonus3 = isset($nonformalNilai[2]) ? ($nonformalNilai[2] * 0.1) : 0;
        $asesor3 = isset($formalNilai[2])
            ? (int) min(round($formalNilai[2] + $bonus3), 85)
            : null;

        // =========================
        // RATA-RATA FINAL
        // =========================
        $nilaiAsesor = collect([$asesor1, $asesor2, $asesor3])
            ->filter(fn($v) => $v !== null);

        $rataRata = $nilaiAsesor->count()
            ? (int) round($nilaiAsesor->avg())
            : null;

        // =========================
        // RETURN SEMUA NILAI
        // =========================
        return [$asesor1, $asesor2, $asesor3, $rataRata];
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
