<?php

namespace App\Http\Controllers;

use App\Exports\TemplateJurusanExport;
use App\Imports\JurusanImport;
use App\Models\Jurusan;
use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
        $semuaSemester = Semester::orderBy('kode', 'asc')->get();
        $jurusan = $jurusan->load('mataKuliah.semester');
        return view('jurusan.edit', compact('jurusan', 'semuaSemester'));
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
        $namaFile = 'template_jurusan_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new TemplateJurusanExport, $namaFile);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new JurusanImport, $request->file('file'));

            return back()->with('success', 'Berhasil: Data Jurusan telah disinkronkan.');
        } catch (\Exception $e) {
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
