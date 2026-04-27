<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurusanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->role;
        $jurusanId = $user->jurusan_id;


        $jurusan = Jurusan::with('mataKuliah')
            ->when($role === 'AdminJurusan', function ($query) use ($jurusanId) {
                return $query->where('id', $jurusanId);
            })
            ->get();
        return view('jurusan.index', compact('jurusan'));
    }

    public function edit(Jurusan $jurusan)
    {
        $jurusan = $jurusan->load('mataKuliah.semester');
        return view('jurusan.edit', compact('jurusan'));
    }

    public function create()
    {
        return view('jurusan.create');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::find($id);
        $jurusan->delete();
        return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil dihapus.');
    }

    public function templateDownload()
    {
        // Header sesuai dengan struktur tabel Jurusan dan Mata Kuliah
        $headers = [
            "kode_jurusan",
            "nama_jurusan",
            "kode_mk",
            "nama_mk",
            "sks",
            "nilai_minimum"
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Contoh baris data untuk Teknik Informatika
            fputcsv($file, ['32', 'Teknik Informatika', 'TI101', 'Algoritma Pemrograman', '3', '60']);
            fputcsv($file, ['32', 'Teknik Informatika', 'TI102', 'Struktur Data', '3', '65']);

            // Contoh baris data untuk Sistem Informasi
            fputcsv($file, ['33', 'Sistem Informasi', 'SI201', 'Manajemen Basis Data', '4', '60']);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=template_jurusan_mk.csv",
            "Pragma" => "no-cache",
            "Expires" => "0",
        ]);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($file); // Ambil header baris pertama

        try {
            while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
                // Pastikan baris tidak kosong
                if (empty($row[0])) continue;

                // 1. Sync Jurusan (Cocokkan kode_jurusan)
                $jurusan = Jurusan::updateOrCreate(
                    ['kode_jurusan' => $row[0]], // Kolom 0: kode_jurusan
                    ['nama_jurusan' => $row[1]]  // Kolom 1: nama_jurusan
                );

                // 2. Sync Mata Kuliah (Cocokkan kode_mk)
                MataKuliah::updateOrCreate(
                    ['kode_mk' => $row[2]], // Kolom 2: kode_mk
                    [
                        'jurusan_id'    => $jurusan->id,
                        'nama_mk'       => $row[3], // Kolom 3: nama_mk
                        'sks'           => $row[4], // Kolom 4: sks
                        'nilai_minimum' => $row[5] ?? 60 // Kolom 5: nilai_minimum
                    ]
                );
            }
            fclose($file);

            return back()->with('success', 'Import Berhasil: Data Jurusan dan Mata Kuliah telah disinkronkan.');
        } catch (\Exception $e) {
            fclose($file);
            return back()->withErrors(['error' => 'Gagal import: ' . $e->getMessage()]);
        }
    }


    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'kode_jurusan' => 'required|string|max:255|unique:jurusan,kode_jurusan',
            'nama_jurusan' => 'required|string|max:255',
        ], [
            // Custom Pesan Error (Opsional)
            'kode_jurusan.unique' => 'Kode jurusan ini sudah terdaftar dalam sistem.',
            'kode_jurusan.required' => 'Kode jurusan wajib diisi.',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi.',
        ]);

        // 2. Simpan ke Database
        Jurusan::create([
            'kode_jurusan' => strtoupper($request->kode_jurusan), // Otomatis Uppercase
            'nama_jurusan' => $request->nama_jurusan,
        ]);

        // 3. Redirect kembali ke index dengan pesan sukses
        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan baru berhasil ditambahkan!');
    }


    public function update(Request $request, $id)
    {
        // 1. Validasi input
        $request->validate([
            'kode_jurusan' => 'required|string|max:255|unique:jurusan,kode_jurusan,' . $id,
            'nama_jurusan' => 'required|string|max:255',
        ], [
            'kode_jurusan.unique' => 'Kode jurusan sudah digunakan oleh program studi lain.',
            'kode_jurusan.required' => 'Kode jurusan wajib diisi.',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi.',
        ]);

        try {
            // 2. Cari data jurusan
            $jurusan = Jurusan::findOrFail($id);

            // 3. Update data
            $jurusan->update([
                'kode_jurusan' => $request->kode_jurusan,
                'nama_jurusan' => $request->nama_jurusan,
            ]);

            // 4. Redirect dengan pesan sukses
            return redirect()->route('jurusan.index')
                ->with('success', 'Data jurusan berhasil diperbarui!');
        } catch (\Exception $e) {
            // Tangani error jika terjadi kegagalan sistem
            return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
