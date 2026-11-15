<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends BaseController
{
    public function index()
    {
        $user = auth()->user()->load('warung');
        return view('profile.index', compact('user'));
    }

    public function updateNama(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ], [
            'nama.required' => 'Nama tidak boleh kosong.',
        ]);

        auth()->user()->update(['nama' => trim($request->nama)]);
        $this->catatLog('Ubah nama profil');

        return redirect()->route('profile.index')->with('success', 'Nama berhasil diperbarui.');
    }

    public function updateUsername(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username'           => ['required', 'regex:/^[a-zA-Z0-9_]{3,30}$/'],
            'password_konfirm_un'=> 'required',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.regex'    => 'Username hanya boleh huruf, angka, dan underscore (3–30 karakter).',
            'password_konfirm_un.required' => 'Password konfirmasi wajib diisi.',
        ]);

        if (!Hash::check($request->password_konfirm_un, $user->password)) {
            return redirect()->route('profile.index')
                ->with('error_msg', 'Password konfirmasi tidak sesuai.')
                ->withInput();
        }

        $username = trim($request->username);

        // Cek username sudah dipakai user lain
        $sudahAda = \DB::table('tb_users')
            ->where('username', $username)
            ->where('id_user', '!=', $user->id_user)
            ->exists();

        if ($sudahAda) {
            return redirect()->route('profile.index')
                ->with('error_msg', "Username \"$username\" sudah digunakan. Pilih username lain.")
                ->withInput();
        }

        $user->update(['username' => $username]);
        $this->catatLog("Ubah username menjadi: $username");

        return redirect()->route('profile.index')
            ->with('success', "Username berhasil diubah menjadi \"$username\".");
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password_lama'   => 'required',
            'password_baru'   => 'required|min:6',
            'password_konfirm'=> 'required|same:password_baru',
        ], [
            'password_lama.required'    => 'Password lama wajib diisi.',
            'password_baru.required'    => 'Password baru wajib diisi.',
            'password_baru.min'         => 'Password baru minimal 6 karakter.',
            'password_konfirm.required' => 'Konfirmasi password wajib diisi.',
            'password_konfirm.same'     => 'Password baru dan konfirmasi tidak sama.',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            return redirect()->route('profile.index')
                ->with('error_msg', 'Password lama tidak sesuai.');
        }

        $user->update(['password' => Hash::make($request->password_baru)]);
        $this->catatLog('Ganti password profil');

        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diubah.');
    }
}
