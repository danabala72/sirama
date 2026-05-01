<?php

namespace App\Http\Controllers;

use App\Exports\TemplateCpmkExport;
use App\Exports\TemplateMKExport;
use App\Imports\CpmkImport;
use App\Imports\MataKuliahImport;
use App\Models\Jurusan;
use App\Models\MataKuliah;
use App\Models\MataKuliahSemester;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->role;
        $jurusanId = $user->jurusan_id;
        $semuaSemester = Semester::all();
        $semuaJurusan = Jurusan::all();

        $mks = MataKuliah::with(['jurusan', 'semester'])
            ->when($role === 'AdminJurusan', function ($query) use ($jurusanId) {
                return $query->where('jurusan_id', $jurusanId);
            })
            ->when($role === 'Admin' && $request->jurusan_id, function ($query) use ($request) {
                return $query->where('jurusan_id', $request->jurusan_id);
            })
            ->get();
        return view('mata-kuliah.index', compact('mks', 'semuaSemester', 'semuaJurusan'));
    }

    public function edit(MataKuliah $mk)
    {
        // 1. Cari semester yang sedang aktif (is_active = 1)
        $semesterAktif = Semester::where('is_active', 1)->first();

        if (!$semesterAktif) {
            return back()->with('error', 'Tidak ada semester yang sedang aktif. Silakan aktifkan satu semester di menu Semester.');
        }

        // 2. Gunakan firstOrCreate agar jika data jembatannya tidak ada, langsung dibuat
        $mkSemester = MataKuliahSemester::firstOrCreate([
            'mata_kuliah_id' => $mk->id,
            'semester_id'    => $semesterAktif->id
        ]);

        $semuaSemester = Semester::orderBy('kode', 'asc')->get();

        return view('mata-kuliah.edit', compact('mk', 'semuaSemester', 'mkSemester'));
    }

    public function destroy($id)
    {
        $mataKuliah = MataKuliah::find($id);
        $mataKuliah->delete();
        return redirect()->route('mk.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'kode_mk'       => 'required|string|max:255|unique:mata_kuliah,kode_mk,' . $id,
            'nama_mk'       => 'required|string|max:255',
            'semester_id'   => 'required|integer',
            'sks'           => 'required|integer|min:1',
            'nilai_minimum' => 'nullable|integer|min:0|max:100',
            'jurusan_id'    => 'required|exists:jurusan,id',
        ], [
            'kode_mk.unique' => 'Kode Mata Kuliah ini sudah digunakan oleh MK lain.',
        ]);

        // 2. Cari Data dan Update
        $mk = MataKuliah::findOrFail($id);

        $mk->update([
            'jurusan_id'    => $request->jurusan_id,
            'kode_mk'       => strtoupper($request->kode_mk), // Konsistensi Uppercase
            'nama_mk'       => $request->nama_mk,
            'sks'           => $request->sks,
            'nilai_minimum' => $request->nilai_minimum ?? 60, // Default 60 jika kosong
        ]);
        if ($request->semester_id) {
            // Cari apakah sudah ada relasi di tabel pivot, jika ada update, jika tidak insert
            DB::table('mata_kuliah_semester')->updateOrInsert(
                ['mata_kuliah_id' => $mk->id],
                [
                    'semester_id' => $request->semester_id, // Set semester baru
                    'updated_at'  => now()
                ]
            );
        }

        // 3. Redirect dengan membawa parameter jurusan_id agar filter tetap aktif
        return redirect()->route('mk.index', ['jurusan_id' => $mk->jurusan_id])
            ->with('success', 'Mata kuliah ' . $mk->nama_mk . ' berhasil diperbarui.');
    }

    public function templateCpmk($kode_mk)
    {
        $fileName = 'template_cpmk_' . strtolower($kode_mk) . '-' . date('Ymd_His') . '.xlsx';

        return Excel::download(new TemplateCpmkExport($kode_mk), $fileName);
    }

    public function importCpmk(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new CpmkImport, $request->file('file'));

            return back()->with('success', "Proses sinkronisasi indikator CPMK berhasil diselesaikan.");
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal sinkronisasi: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliah,kode_mk',
            'nama_mk' => 'required|string',
            'jurusan_id' => 'required|exists:jurusan,id',
            'sks' => 'required|integer|min:1',
            'nilai_minimum' => 'nullable|integer',
            'semester_id' => 'required|integer|exists:semester,id'
        ]);

        $mk = MataKuliah::create([
            'jurusan_id' => $request->jurusan_id,
            'kode_mk' => strtoupper($request->kode_mk),
            'nama_mk' => $request->nama_mk,
            'sks' => $request->sks,
            'nilai_minimum' => $request->nilai_minimum ?? 60,
        ]);

        if ($request->semester_id) {
            DB::table('mata_kuliah_semester')->updateOrInsert(
                ['mata_kuliah_id' => $mk->id],
                [
                    'semester_id' => $request->semester_id,
                    'updated_at'  => now()
                ]
            );
        }


        // Kembali ke halaman edit jurusan asal
        return redirect()->route('jurusan.edit', $request->jurusan_id)
            ->with('success', 'Mata kuliah berhasil ditambahkan!');
    }

    public function toggleStatus($id)
    {
        $mk = MataKuliah::findOrFail($id);
        $mk->status = !$mk->status;
        $mk->save();

        return back()->with('success', 'Status mata kuliah berhasil diperbarui!');
    }

    public function templateDownload()
    {
        $jurusanId = Auth::user()->jurusan?->id;
        $fileName = 'template_import_mk_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new TemplateMKExport($jurusanId), $fileName);
    }

    public function import(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MataKuliahImport, $request->file('file'));
            return back()->with('success', 'Data Mata Kuliah berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}
