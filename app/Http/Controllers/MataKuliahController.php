<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $mks = MataKuliah::with(['jurusan', 'semester'])
            ->when($request->jurusan_id, function ($query, $jurusan_id) {
                return $query->where('jurusan_id', $jurusan_id);
            })
            ->get();
        return view('mata-kuliah.index', compact('mks'));
    }

    public function edit(MataKuliah $mk)
    {
        $semuaSemester = Semester::orderBy('kode', 'asc')->get();
        $mk = $mk->load(['cps', 'semester']);

        return view('mata-kuliah.edit', compact('mk', 'semuaSemester'));
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
        $headers = ["kode_mk", "indikator_capaian"];
        $callback = function () use ($headers, $kode_mk) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, [$kode_mk, 'Deskripsi capaian pembelajaran mata kuliah']);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=template_cpmk.csv",
            "Pragma" => "no-cache",
            "Expires" => "0",
        ]);
    }

    public function importCpmk(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');
        fgetcsv($file); // Skip header [kode_mk, indikator_capaian]

        $successCount = 0;

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            // row[0] = kode_mk, row[1] = indikator_capaian
            if (!empty($row[0]) && !empty($row[1])) {

                // 1. Cari Mata Kuliah berdasarkan kode_mk
                $mk = MataKuliah::where('kode_mk', trim($row[0]))->first();

                if ($mk) {
                    // 2. Sync (Gunakan updateOrCreate agar tidak duplikat jika diimport ulang)
                    $mk->cps()->updateOrCreate([
                        'indikator_capaian' => trim($row[1])
                    ]);
                    $successCount++;
                }
            }
        }
        fclose($file);

        return back()->with('success', "Berhasil sinkronisasi $successCount indikator CPMK.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliah,kode_mk',
            'nama_mk' => 'required|string',
            'jurusan_id' => 'required|exists:jurusan,id',
            'sks' => 'required|integer|min:1',
            'nilai_minimum' => 'nullable|integer'
        ]);

        MataKuliah::create([
            'jurusan_id' => $request->jurusan_id,
            'kode_mk' => strtoupper($request->kode_mk),
            'nama_mk' => $request->nama_mk,
            'sks' => $request->sks,
            'nilai_minimum' => $request->nilai_minimum ?? 60,
        ]);

        // Kembali ke halaman edit jurusan asal
        return redirect()->route('jurusan.edit', $request->jurusan_id)
            ->with('success', 'Mata kuliah berhasil ditambahkan!');
    }
}
