<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
