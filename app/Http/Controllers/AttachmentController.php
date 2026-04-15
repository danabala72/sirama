<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{

    public function storeCv(Request $request)
    {

        $id = Auth::user()->mahasiswa->id;

        $request->validate([
            'cv' => 'nullable|file|mimes:pdf|max:51200',
            'pernyataan' => 'nullable|file|mimes:pdf|max:51200',
        ], [
            'cv.file' => 'File CV tidak valid.',
            'cv.mimes' => 'Format CV harus pdf atau',
            'cv.max' => 'Ukuran CV maksimal 50MB.',

            'pernyataan.file' => 'File pernyataan tidak valid.',
            'pernyataan.mimes' => 'Format pernyataan harus pdf',
            'pernyataan.max' => 'Ukuran pernyataan maksimal 50MB.',
        ]);

        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $oldCv = Attachment::where('mahasiswa_id', $id)->where('label', 'cv')->first();
            if ($oldCv) {
                if (Storage::disk('public')->exists($oldCv->file_path)) {
                    Storage::disk('public')->delete($oldCv->file_path);
                }
                $oldCv->delete();
            }

            [$mime, $type, $path] = $this->getTypeAndPath($cv);

            Attachment::create([
                'mahasiswa_id' => $id,
                'label'        => 'cv',
                'file_name'    => $cv->getClientOriginalName(),
                'file_path'    => $path,
                'file_type'    => $type,
                'mime_type'    => $mime,
                'file_size'    => $cv->getSize(),
            ]);
        }

        if ($request->hasFile('pernyataan')) {
            $pernyataan = $request->file('pernyataan');
            $oldPernyataan = Attachment::where('mahasiswa_id', $id)->where('label', 'pernyataan')->first();
            if ($oldPernyataan) {
                if (Storage::disk('public')->exists($oldPernyataan->file_path)) {
                    Storage::disk('public')->delete($oldPernyataan->file_path);
                }
                $oldPernyataan->delete();
            }

            [$mime, $type, $path] = $this->getTypeAndPath($pernyataan);

            Attachment::create([
                'mahasiswa_id' => $id,
                'label'        => 'pernyataan',
                'file_name'    => $pernyataan->getClientOriginalName(),
                'file_path'    => $path,
                'file_type'    => $type,
                'mime_type'    => $mime,
                'file_size'    => $pernyataan->getSize(),
            ]);
        }

        return back()->with('success', 'Semua berkas berhasil diunggah!');
    }
    public function store(Request $request)
    {

        // 1. Validasi Input
        $request->validate([
            'label'     => 'required|string',
            'files'     => 'required|array',
            'files.*'   => 'file|mimes:pdf|max:51200', // Maks 50MB
        ], [
            'required'      => ':attribute wajib diisi.',
            'array'         => ':attribute harus berupa file.',
            'files.*.file'  => 'Berkas yang diunggah tidak valid.',
            'files.*.mimes' => 'Format berkas harus berupa pdf',
            'files.*.max'   => 'Ukuran berkas maksimal adalah 50MB.',
        ], [
            'files'   => 'Berkas',
            'files.*' => 'File',
            'label'   => 'Kategori file'
        ]);



        $id = Auth::user()->mahasiswa->id;

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // 2. Deteksi Tipe File Otomatis
                [$mime, $type, $path] = $this->getTypeAndPath($file);

                $id = Auth::user()->mahasiswa->id;

                Attachment::create([
                    'mahasiswa_id' => $id,
                    'label'        => $request->label,
                    'file_name'    => $file->getClientOriginalName(),
                    'file_path'    => $path,
                    'file_type'    => $type,
                    'mime_type'    => $mime,
                    'file_size'    => $file->getSize(),
                ]);
            }
        }

        return back()->with('success', 'Semua berkas berhasil diunggah!');
    }

    private function getTypeAndPath($file)
    {
        $mime = $file->getMimeType();
        if (str_contains($mime, 'video')) {
            $type = 'video';
        } elseif (str_contains($mime, 'image')) {
            $type = 'image';
        } else {
            $type = 'document';
        }

        $path = $file->store('uploads', 'public');


        return [$mime, $type, $path];
    }

    public function delete($id)
    {
        // Delete dan unlink
        $item = Attachment::findOrFail($id);
        if (Storage::disk('public')->exists($item->file_path)) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return redirect()->back()->with('success', 'File dan data berhasil dihapus!');
    }
}
