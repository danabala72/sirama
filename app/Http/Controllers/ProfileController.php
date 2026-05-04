<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Asesor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function signature(Request $request)
{
    $request->validate([
        'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    // Mengambil relasi asesor dari User yang sedang login
    $user = Auth::user();
    $asesor = Asesor::where('user_id', $user->id)->first();

    if (!$asesor) {
        return back()->with('error', 'Data profil asesor tidak ditemukan.');
    }

    // Hapus file lama jika ada untuk menghemat storage
    if ($asesor->signature && Storage::disk('public')->exists($asesor->signature)) {
        Storage::disk('public')->delete($asesor->signature);
    }

    // Simpan file baru ke folder public/signatures
    $path = $request->file('signature')->store('signatures', 'public');

    // Update path ke tabel asesor
    $asesor->update([
        'signature' => $path
    ]);

    return back()->with('status', 'signature-updated');
}

}
