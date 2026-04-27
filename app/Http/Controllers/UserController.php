<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Models\Role;
use App\Models\ROLES;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            // Filter tambahan untuk Admin Jurusan
            ->when($role === 'AdminJurusan', function ($query) use ($jurusanId) {
                return $query->whereHas('mahasiswa', function ($q) use ($jurusanId) {
                    $q->where('jurusan_id', $jurusanId);
                });
            })
            ->with(['mahasiswa', 'asesor', 'role'])
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

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $roleMhs->id ?? null,
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
        // Logika sederhana menghasilkan CSV template
        $headers = ["username", "password"];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            // Contoh baris
            fputcsv($file, ['mhs_001', 'password123']);
            fputcsv($file, ['mhs_002', 'password123']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=template_mahasiswa.csv",
        ]);
    }

    public function asesorTemplateDownload()
    {
        // Logika sederhana menghasilkan CSV template
        $headers = ["username", "password", "nama_lengkap", "email", "jenis_kelamin", "no_hp"];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Contoh baris instruksi/dummy
            fputcsv($file, ['asesor_001', 'password123', 'Budi Santoso', 'budi@example.com', 'L', "'08123456789"]);
            fputcsv($file, ['asesor_002', 'password123', 'Siti Aminah', 'siti@example.com', 'P', "'08123456789"]);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=template_asesor.csv",
        ]);
    }


    public function userImport(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);

        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data); // Buang baris judul

        foreach ($data as $row) {

            $role = Role::where('role', ROLES::MAHASISWA)->first();

            User::updateOrCreate(
                ['username' => $row[0]],
                [
                    'password' => Hash::make($row[1]),
                    'role_id'  => $role->id,
                ]
            );
        }

        return back()->with('success', 'Data user berhasil diimport!');
    }

    public function asesorImport(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);

        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data);

        // Ambil role sekali saja di luar loop untuk efisiensi
        $role = Role::where('role', ROLES::ASESOR)->first();

        DB::transaction(function () use ($data, $role) {
            foreach ($data as $row) {
                // Mapping kolom (sesuai template sebelumnya)
                // 0:username, 1:password, 2:nama_lengkap, 3:email, 4:jenis_kelamin, 5:no_hp

                // 1. Update atau Buat User
                $user = User::updateOrCreate(
                    ['username' => $row[0]],
                    [
                        'password' => Hash::make($row[1]),
                        'role_id'  => $role->id,
                    ]
                );

                // 2. Bersihkan no_hp dari tanda petik (') hasil manipulasi Excel
                $no_hp = isset($row[5]) ? str_replace("'", "", $row[5]) : null;

                // 3. Update atau Buat Asesor yang nge-link ke user_id
                Asesor::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name'          => $row[2],
                        'email'         => $row[3],
                        'jenis_kelamin' => $row[4],
                        'no_hp'         => $no_hp,
                    ]
                );
            }
        });

        return back()->with('success', 'Data user dan asesor berhasil diimport!');
    }


    public function asesorIndex()
    {
        $asesor = User::whereHas('role', function ($query) {
            $query->where('role', '=', ROLES::ASESOR);
        })->with(['mahasiswa', 'asesor', 'role'])->get();

        return view('asesor.index', compact('asesor'));
    }
}
