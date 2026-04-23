<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\MataKuliahPilihan;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    public function show(Request $request)
    {
        $step = $request->query('step', 1); // default step = 1

        $mahasiswa = Auth::user()->mahasiswa;

        $titles = [
            1 => 'Formulir 1 - Rincian Data Peserta / Calon Peserta',
            2 => 'Formulir 2 - Upload File Pendukung',
            3 => 'Formulir 3 - Pengisian Mata Kuliah',
            4 => 'Formulir 4 - Formulir Daftar Riwayat Hidup',
            5 => 'Formulir 5 - Formulir Asesmen Mandiri',
            6 => 'Formulir 6 - Formulir Transfer SKS dari Pendidikan Formal'
        ];

        abort_if(!isset($titles[$step]), 404);

        view()->share('step', $step);

        if ($step == 2) {
            $attachments = Attachment::where('mahasiswa_id', $mahasiswa->id)
                ->latest()
                ->get();
        } elseif ($step == 4) {
            $attachments = Attachment::where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('label', ['cv', 'pernyataan'])
                ->latest()
                ->get();
        } else {
            $attachments = collect();
        }

        $jurusan = ($step == 1)
            ? Jurusan::all()
            : collect();

        $semester = ($step == 3)
            ? Semester::orderBy('id', 'desc')->get()
            : collect();

        $mataKuliahPilihan = ($step == 5 || $step == 6)
            ? MataKuliahPilihan::with('mataKuliah.cps', 'cpLevel', 'transferSks.cpmkItems')->where('mahasiswa_id', $mahasiswa->id)->get()
            : collect();

        return view('form.index', [
            'step' => $step,
            'title' => $titles[$step],
            'mahasiswa' => $mahasiswa,
            'attachments' => $attachments,
            'jurusan' => $jurusan,
            'semester' => $semester,
            'mataKuliahPilihan' => $mataKuliahPilihan
        ]);
    }

    public function storeStep1(Request $request)
    {
        $user = Auth::user();
        $existingMahasiswa = $user->mahasiswa;

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'tempat_lahir' => ['required', 'string', 'max:255'],
                'tgl_lahir' => ['required', 'date'],
                'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
                'status_perkawinan' => ['required', Rule::in(['Belum Kawin', 'Kawin'])],
                'kebangsaan' => ['required', 'string', 'max:255'],
                'alamat_rumah' => ['required', 'string', 'max:255'],
                'kode_pos' => ['required', 'string', 'max:255'],
                'no_hp' => ['required', 'string', 'max:255', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/'],
                'alamat_kantor' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('mahasiswa', 'email')->ignore($existingMahasiswa?->id),
                ],
                'jurusan_id' => ['required', 'exists:jurusan,id'],
            ],
            [
                'required' => ':attribute wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar.',
                'max' => ':attribute maksimal :max karakter.',
                'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
                'status_perkawinan.in' => 'Status perkawinan tidak valid.',
                'no_hp.regex' => 'Format :attribute tidak valid',
                'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.'
            ],
            [
                'name' => 'Nama lengkap',
                'tempat_lahir' => 'Tempat lahir',
                'tgl_lahir' => 'Tanggal lahir',
                'jenis_kelamin' => 'Jenis kelamin',
                'status_perkawinan' => 'Status perkawinan',
                'kebangsaan' => 'Kebangsaan',
                'alamat_rumah' => 'Alamat rumah',
                'kode_pos' => 'Kode pos',
                'no_hp' => 'No HP',
                'alamat_kantor' => 'Alamat kantor',
                'email' => 'Email',
                'jurusan_id' => 'Jurusan',
            ]
        );

        $validated['user_id'] = $user->id;

        Mahasiswa::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()
            ->route('form.step', 'step=1')
            ->with('success', 'Data Formulir 1 berhasil disimpan.');
    }

    public function getMataKuliah(Request $request)
    {
        $mahasiswaId = Auth::user()->mahasiswa->id;

        $semester = Semester::orderBy('id', 'desc')->get();
        $jurusanId = Auth::user()->mahasiswa->jurusan_id;
        $semesterId = $request->semester_id;

        // 2. Query Mata Kuliah berdasarkan Relasi Pivot yang kita buat
        $mataKuliah = MataKuliah::where('jurusan_id', $jurusanId)
            ->whereHas('semester', function ($q) use ($semesterId) {
                $q->where('semester_id', $semesterId);
            })
            ->get();

        // 3. Kirim data ke view berikutnya

        $mataKuliahPilihan = Auth::user()
            ->mahasiswa
            ->mataKuliahPilihan()
            ->with('attachment')
            ->get();
        return view('form.index', [
            'step'       => 3,
            'title'      => 'Formulir 3 - Pengisian Mata Kuliah',
            'mataKuliah' => $mataKuliah,
            'attachment' => Attachment::where('mahasiswa_id', $mahasiswaId)->get(),
            'semester'   => $semester,
            'jurusan'    => Jurusan::all(),
            'mataKuliahPilihan' => $mataKuliahPilihan
        ]);
    }
}
