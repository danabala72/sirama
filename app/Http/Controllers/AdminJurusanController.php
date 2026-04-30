<?php

namespace App\Http\Controllers;

use App\Models\AdminJurusan;
use App\Models\Jurusan;
use App\Models\Role;
use App\Models\ROLES;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminJurusanController extends Controller
{
    public function index()
    {
        $adminJurusans = AdminJurusan::with(['user', 'user.jurusan'])->get();
        $jurusans = Jurusan::all();
        return view('admin-jurusan.index', compact('adminJurusans', 'jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'jurusan_id'    => 'required|exists:jurusan,id',
            'email'         => 'nullable|email|unique:admin_jurusan,email',
            'no_hp'         => 'nullable|string|max:20',
            'username'      => 'required|string|unique:users,username',
            'password'      => 'required|string|min:6|confirmed',
        ], [
            'required' => ':attribute wajib diisi.',
            'unique'   => ':attribute sudah terdaftar.',
            'min'      => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'email'    => 'Format email tidak valid.',
            'exists'   => ':attribute yang dipilih tidak tersedia.',
            'in'       => ':attribute harus pilih L atau P.',
        ], [
            'nama'          => 'Nama Lengkap',
            'jenis_kelamin' => 'Jenis Kelamin',
            'jurusan_id'    => 'Jurusan',
            'no_hp'         => 'Nomor HP',
            'username'      => 'Username',
            'password'      => 'Password',
        ]);


        try {
            // 2. Gunakan Transaction agar data konsisten di 2 tabel
            DB::transaction(function () use ($request) {
                $role = Role::where('role', ROLES::ADMINJURUSAN)->first();
                // Simpan ke tabel users
                $user = User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'role_id'  => $role->id,
                    'jurusan_id'    => $request->jurusan_id,
                ]);

                // Simpan ke tabel admin_jurusan
                AdminJurusan::create([
                    'user_id'       => $user->id,                    
                    'nama'          => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'email'         => $request->email,
                    'no_hp'         => $request->no_hp,
                ]);
            });

            return redirect()->route('admin_jurusan.index')->with('success', 'Admin Jurusan berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Jika gagal, kembali ke form dengan pesan error
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $admin = AdminJurusan::with('user.jurusan')->findOrFail($id);

        return response()->json([
            'admin'    => $admin,
            'username' => $admin->user->username
        ]);
    }

    public function update(Request $request, $id)
    {
        // 1. Cari data admin dan user terkait
        $admin = AdminJurusan::findOrFail($id);
        $user = User::findOrFail($admin->user_id);

        // 2. Flash action URL ke session agar modal edit terbuka kembali jika validasi gagal
        session()->flash('edit_action', route('admin_jurusan.update', $id));

        // 3. Validasi Data
        $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'jurusan_id'    => 'required|exists:jurusan,id',
            // 'email,'.$admin->id memastikan email ini boleh tetap sama untuk data admin ini sendiri
            'email'         => 'nullable|email|unique:admin_jurusan,email,' . $admin->id,
            'no_hp'         => 'nullable|string|max:20',
            // 'username,'.$user->id memastikan username ini boleh tetap sama untuk user ini sendiri
            'username'      => 'required|string|unique:users,username,' . $user->id,
            'password'      => 'nullable|string|min:6|confirmed',
        ], [
            'required'  => ':attribute wajib diisi.',
            'unique'    => ':attribute sudah terdaftar.',
            'min'       => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi password tidak cocok.',
        ], [
            'nama'          => 'Nama Lengkap',
            'jurusan_id'    => 'Jurusan',
            'username'      => 'Username',
            'password'      => 'Password',
        ]);

        try {
            // 4. Proses Update dengan Transaction
            DB::transaction(function () use ($request, $admin, $user) {
                // Update data User Login
                $user->username = $request->username;
                $user->jurusan_id = $request->jurusan_id;
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }
                $user->save();

                // Update data Profil Admin Jurusan
                $admin->update([                   
                    'nama'          => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'email'         => $request->email,
                    'no_hp'         => $request->no_hp,
                ]);
            });

            return redirect()->route('admin_jurusan.index')
                ->with('success', 'Data Admin Jurusan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $admin = AdminJurusan::findOrFail($id);

        try {
            DB::transaction(function () use ($admin) {
                $user = User::findOrFail($admin->user_id);
                $user->delete();
            });

            return redirect()->route('admin_jurusan.index')
                ->with('success', 'Admin Jurusan beserta akun loginnya berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
