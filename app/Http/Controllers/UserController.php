<?php

namespace App\Http\Controllers;

use App\Exports\TemplateAsesorExport;
use App\Exports\TemplateMahasiswaExport;
use App\Imports\AsesorImport;
use App\Imports\MahasiswaImport;
use App\Models\Asesor;
use App\Models\Role;
use App\Models\ROLES;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->role;
        $jurusanId = $user->jurusan_id;

        $mahasiswa = User::whereHas('role', function ($query) {
            $query->where('role', '=', ROLES::MAHASISWA);
        })
            ->whereNotNull('jurusan_id')

            ->when($role === 'AdminJurusan', function ($query) use ($jurusanId) {
                return $query->where('jurusan_id', $jurusanId);
            })
            ->with(['mahasiswa', 'asesor', 'role', 'jurusan'])
            ->get();

        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function edit(User $user)
    {
        $asesors = Asesor::with('user')->get();
        $selectedAsesors = $user->mahasiswa ? $user->mahasiswa->asesors->pluck('id')->toArray() : [];
        return view('mahasiswa.edit', compact('user', 'asesors', 'selectedAsesors'));
    }

    public function asesorEdit(User $user)
    {
        $user->load('asesor');

        return view('asesor.edit', compact('user'));
    }

    public function create()
    {
        return view('mahasiswa.create');
    }

    public function asesorCreate()
    {
        return view('asesor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $roleMhs = Role::where('role', ROLES::MAHASISWA)->first();
        $jurusanId = Auth::user()->jurusan->id;

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $roleMhs->id ?? null,
            'jurusan_id' => $jurusanId
        ]);

        return redirect()->route('mahasiswa.index')->with('success', 'User baru berhasil dibuat.');
    }


    public function asesorStore(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',

            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255|unique:asesor,email',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        $role = Role::where('role', ROLES::ASESOR)->first();

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $role->id ?? null,
        ]);

        Asesor::create([
            'user_id'       => $user->id,
            'name'          => $request->name,
            'email'         => $request->email,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp'         => $request->no_hp,
        ]);

        return redirect()->route('asesor.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            // 'unique:table,column,except,idColumn'
            'username' => 'required|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'required_with:password|same:password',
            'asesor_ids' => 'nullable|array',
            'asesor_ids.*' => 'exists:asesor,id',

        ]);

        $roleMhs = Role::where('role', ROLES::MAHASISWA)->first();
        $user->username = $request->username;
        $user->role_id = $roleMhs->id;

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($user->mahasiswa) {
            $user->mahasiswa->asesors()->sync($request->asesor_ids ?? []);
        }

        return redirect()->route('mahasiswa.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function asesorUpdate(Request $request, User $user)
    {
        $request->validate([
            'username'      => 'required|string|max:255|unique:users,username,' . $user->id,
            'password'      => 'nullable|string|min:6|confirmed',
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255|unique:asesor,email,' . ($user->asesor->id ?? 'NULL'),
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($request, $user) {
            // 1. Update Data User
            $user->username = $request->username;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // 2. Update atau Buat Data Asesor
            // Menggunakan updateOrCreate agar jika data asesor belum ada, otomatis dibuatkan
            $user->asesor()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'no_hp'         => $request->no_hp,
                ]
            );
        });

        return redirect()->route('asesor.index')->with('success', 'Data user dan profil asesor berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $loged = Auth::user();
        if ($loged->id == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $user = User::find($id);

        $user->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'User berhasil dihapus.');
    }

    public function asesordestroy($id)
    {
        $loged = Auth::user();
        if ($loged->id == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::find($id);

        $user->delete();

        return redirect()->route('asesor.index')->with('success', 'User berhasil dihapus.');
    }

    public function templateDownload()
    {
        $jurusanId = Auth::user()->jurusan?->id;

        $fileName = 'template_import_mahasiswa_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new TemplateMahasiswaExport($jurusanId), $fileName);
    }

    public function asesorTemplateDownload()
    {
        $fileName = 'template_import_asesor_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new TemplateAsesorExport, $fileName);
    }



    public function userImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Silakan pilih file terlebih dahulu.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal adalah 2MB.'
        ]);

        try {
            // 2. Jalankan proses import
            Excel::import(new MahasiswaImport, $request->file('file'));

            // 3. Beri feedback sukses
            return back()->with('success', 'Data Mahasiswa berhasil diimport dan disinkronkan!');
        } catch (\Exception $e) {
            // 4. Tangani jika ada error (format salah, data duplikat, dll)
            return back()->withErrors(['file' => 'Gagal mengimport data: ' . $e->getMessage()]);
        }
    }

    public function asesorImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new AsesorImport, $request->file('file'));
            return back()->with('success', 'Data user dan asesor berhasil diimport!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal import: ' . $e->getMessage()]);
        }
    }


    public function asesorIndex()
    {
        $asesor = User::whereHas('role', function ($query) {
            $query->where('role', '=', ROLES::ASESOR);
        })->with(['mahasiswa', 'asesor', 'role'])->get();

        return view('asesor.index', compact('asesor'));
    }
}
