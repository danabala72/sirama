<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Skema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkemaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $jurusanId = $user->jurusan_id;

        $skemas = Skema::with('jurusan')
            ->when($user->role->role === 'AdminJurusan', function ($query) use ($jurusanId) {
                return $query->where('jurusan_id', $jurusanId);
            })
            ->get();

        $jurusans = Jurusan::all();

        return view('skema.index', compact('skemas', 'jurusans'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_skema' => 'required|string|max:255',
                'deskripsi' => 'nullable|string|max:1000',
                'jurusan_id' => 'required|exists:jurusan,id',
            ]);

            Skema::create($request->all());

            if ($request->jurusan_id) {
                return redirect()->route('jurusan.edit', $request->jurusan_id)->with('success', 'Skema berhasil ditambahkan.');
            }

            return redirect()->route('skema.index')->with('success', 'Skema berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan skema: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $skema = Skema::findOrFail($id);
        $jurusans = Jurusan::all();

        return response()->json([
            'skema' => $skema,
            'jurusans' => $jurusans,
        ]);
    }

    public function update(Request $request, $id)
    {
        $skema = Skema::findOrFail($id);

        $request->validate([
            'nama_skema' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'jurusan_id' => 'required|exists:jurusan,id',
        ]);

        $skema->update($request->all());

        if ($skema->jurusan_id) {
            return redirect()->route('jurusan.edit', $skema->jurusan_id)->with('success', 'Skema berhasil diperbarui.');
        }

        return redirect()->route('skema.index')->with('success', 'Skema berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $skema = Skema::findOrFail($id);
        $jurusanId = $skema->jurusan_id;
        $skema->delete();

        if ($jurusanId) {
            return redirect()->route('jurusan.edit', $jurusanId)->with('success', 'Skema berhasil dihapus.');
        }

        return redirect()->route('skema.index')->with('success', 'Skema berhasil dihapus.');
    }
}
