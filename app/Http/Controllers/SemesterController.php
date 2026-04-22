<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function setAktif($id)
    {
        Semester::where('is_active', 1)->update(['is_active' => 0]);

        $semester = Semester::findOrFail($id);
        $semester->update(['is_active' => 1]);

        return redirect()->back()->with('success', "Semester {$semester->label} sekarang menjadi semester aktif.");
    }

    public function index()
    {
        $semesters = Semester::orderBy('kode', 'desc')->get();
        return view('semester.index', compact('semesters'));
    }

    public function store(Request $request)
    {
        $request->validate(['kode' => 'required|unique:semester', 'label' => 'required']);
        Semester::create($request->all());
        return back()->with('success', 'Semester berhasil ditambah.');
    }

    public function update(Request $request, Semester $semester)
    {
        $semester->update($request->all());
        return back()->with('success', 'Semester berhasil diupdate.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return back()->with('success', 'Semester berhasil dihapus.');
    }
}
