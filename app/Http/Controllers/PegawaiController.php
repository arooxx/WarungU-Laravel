<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Warung;

class PegawaiController extends BaseController
{
    public function index(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $warungList = Warung::orderBy('nama_warung')->get();
        $error      = '';

        // Tambah pegawai — dihandle di halaman yang sama (persis seperti native)
        if ($request->isMethod('POST') && $request->input('action') === 'tambah') {
            $nama      = trim($request->input('nama', ''));
            $username  = trim($request->input('username', ''));
            $password  = $request->input('password', '');
            $id_warung = (int)$request->input('id_warung', 0);

            if (!$nama || !$username || !$password) {
                $error = 'Semua field wajib diisi!';
            } elseif (strlen($password) < 6) {
                $error = 'Password minimal 6 karakter!';
            } elseif (User::where('username', $username)->exists()) {
                $error = 'Username sudah digunakan!';
            } else {
                User::create([
                    'nama'      => $nama,
                    'username'  => $username,
                    'password'  => Hash::make($password),
                    'role'      => 'pegawai',
                    'id_warung' => $id_warung,
                ]);
                $this->catatLog("Tambah pegawai: $nama");
                $warungParam = $semuaWarung ? '0' : $idWarungAktif;
                return redirect()->route('pegawai.index', ['warung' => $warungParam])->with('msg', 'tambah');
            }
        }

        // Ambil daftar pegawai sesuai filter warung
        $pegawai = User::with('warung')
            ->where('role', 'pegawai')
            ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->orderBy('nama')
            ->get();

        $warungParam = $semuaWarung ? '0' : $idWarungAktif;

        return view('pegawai.index', compact(
            'pegawai', 'warungList', 'error',
            'daftarWarung', 'idWarungAktif', 'semuaWarung', 'warungParam'
        ));
    }

    public function edit($id)
    {
        $pegawai    = User::where('role', 'pegawai')->findOrFail($id);
        $warungList = Warung::orderBy('nama_warung')->get();
        return view('pegawai.edit', compact('pegawai', 'warungList'));
    }

    public function update(Request $request, $id)
    {
        $pegawai = User::where('role', 'pegawai')->findOrFail($id);

        $nama      = trim($request->input('nama', ''));
        $username  = trim($request->input('username', ''));
        $password  = $request->input('password', '');
        $id_warung = (int)$request->input('id_warung', 0);

        if (!$nama || !$username) {
            return back()->withErrors(['edit' => 'Nama dan username wajib diisi!'])->withInput();
        }

        if (User::where('username', $username)->where('id_user', '!=', $id)->exists()) {
            return back()->withErrors(['edit' => 'Username sudah digunakan!'])->withInput();
        }

        $data = ['nama' => $nama, 'username' => $username, 'id_warung' => $id_warung];
        if ($password) {
            $data['password'] = Hash::make($password);
        }

        $pegawai->update($data);
        $this->catatLog("Edit pegawai: $nama");

        return redirect()->route('pegawai.index')->with('msg', 'edit');
    }

    public function destroy($id)
    {
        $pegawai = User::where('role', 'pegawai')->findOrFail($id);
        $nama    = $pegawai->nama;
        $pegawai->delete();
        $this->catatLog("Hapus pegawai: $nama");

        return redirect()->route('pegawai.index')->with('msg', 'hapus');
    }
}
