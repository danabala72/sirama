<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = User::whereHas('role', function ($query) {
            $query->where('role', '!=', 'Admin');
        })->with(['mahasiswa', 'asesor', 'role'])->get();

        return view('user.index', compact('user'));


        return view('user.index', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::whereIn('role', ['Mahasiswa', 'Asesor'])->get();

        return view('user.edit', compact('user', 'roles'));
    }

    public function create()
    {
        $roles = Role::whereIn('role', ['Mahasiswa', 'Asesor'])->get();
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|exists:roles,id'
        ]);


        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role ?? null,
        ]);

        return redirect()->route('user.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            // 'unique:table,column,except,idColumn'
            'username' => 'required|unique:users,username,' . $user->id,
            'password' => 'nullable|min:6',
            'role'     => 'required|exists:roles,id'
        ]);

        $user->username = $request->username;
        $user->role_id = $request->role;

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $loged = Auth::user();
        if ($loged->id == $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }


    public function templateDownload()
    {
        // Logika sederhana menghasilkan CSV template
        $headers = ["username", "password", "role"];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            // Contoh baris
            fputcsv($file, ['mhs_001', 'password123', 'Mahasiswa']);
            fputcsv($file, ['asesor_01', 'password123', 'Asesor']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=template_users.csv",
        ]);
    }

    public function userImport(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv,txt']);

        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data); // Buang baris judul

        foreach ($data as $row) {
            // Cari ID Role berdasarkan nama (Mahasiswa/Asesor)
            $role = Role::where('role', 'LIKE', '%' . $row[2] . '%')->first();

            User::updateOrCreate(
                ['username' => $row[0]],
                [
                    'password' => Hash::make($row[1]),
                    'role_id'  => $role ? $role->id : 2,
                ]
            );
        }

        return back()->with('success', 'Data user berhasil diimport!');
    }
}
