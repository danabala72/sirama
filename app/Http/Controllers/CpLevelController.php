<?php

namespace App\Http\Controllers;

use App\Models\CpLevelKompetensi;
use App\Models\MataKuliahPilihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CpLevelController extends Controller
{
    public function store(Request $request)
    {
        $cpData = $request->input('cp', []);

        $mataKuliah = MataKuliahPilihan::with('mataKuliah.cps')
            ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->get();

        foreach ($mataKuliah as $mk) {

            if (!$mk->mataKuliah) continue;

            foreach ($mk->mataKuliah->cps as $cp) {

                $value = $cpData[$mk->id][$cp->id] ?? 0;

                CpLevelKompetensi::updateOrCreate(
                    [
                        'mata_kuliah_pilihan_id' => $mk->id,
                        'cp_mata_kuliah_id' => $cp->id,
                    ],
                    [
                        'level_kompetensi' => $value
                    ]
                );
            }
        }

        return back()->with('success', 'Data berhasil disimpan');
    }
}
