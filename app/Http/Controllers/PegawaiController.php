<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Warung;
use App\Rules\PasswordKuat;
use App\Support\AturanAkun;

class PegawaiController extends BaseController
{
    public function index(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $warungList = Warung::orderBy('nama_warung')->get();
        $error      = '';

        // Tambah pegawai oleh pemilik — dihandle di halaman yang sama
        if ($request->isMethod('POST') && $request->input('action') === 'tambah') {
            $validator = Validator::make($request->all(), [
                'nama'      => ['required', 'string', 'max:100'],
                'username'  => AturanAkun::username(),
                'password'  => ['required', new PasswordKuat($request->input('username'))],
                'id_warung' => ['required', 'exists:tb_warung,id_warung'],
            ], AturanAkun::pesan());

            if ($validator->fails()) {
                $error = $validator->errors()->first();
            } else {
                // Dibuat langsung aktif karena pemilik sendiri yang menambahkan
                (new User())->forceFill([
                    'nama'      => $request->input('nama'),
                    'username'  => $request->input('username'),
                    'password'  => Hash::make($request->input('password')),
                    'role'      => 'pegawai',
                    'status'    => 'aktif',
                    'id_warung' => (int)$request->input('id_warung'),
                ])->save();

                $this->catatLog("Tambah pegawai: " . $request->input('nama'));
                $warungParam = $semuaWarung ? '0' : $idWarungAktif;
                return redirect()->route('pegawai.index', ['warung' => $warungParam])->with('msg', 'tambah');
            }
        }

        // Pendaftar yang menunggu persetujuan (tidak tergantung filter warung)
        $menunggu = User::where('role', 'pegawai')
            ->where('status', 'menunggu')
            ->orderBy('created_at')
            ->get();

        // Ambil daftar pegawai sesuai filter warung
        $pegawai = User::with('warung')
            ->where('role', 'pegawai')
            ->where('status', '!=', 'menunggu')
            ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->orderBy('nama')
            ->get();

        $warungParam = $semuaWarung ? '0' : $idWarungAktif;

        return view('pegawai.index', compact(
            'pegawai', 'menunggu', 'warungList', 'error',
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

        $validator = Validator::make($request->all(), [
            'nama'      => ['required', 'string', 'max:100'],
            'username'  => AturanAkun::username($id),
            'id_warung' => ['required', 'exists:tb_warung,id_warung'],
            'password'  => ['nullable', new PasswordKuat($request->input('username'))],
        ], AturanAkun::pesan());

        if ($validator->fails()) {
            return back()->withErrors(['edit' => $validator->errors()->first()])->withInput();
        }

        $data = [
            'nama'      => $request->input('nama'),
            'username'  => $request->input('username'),
            'id_warung' => (int)$request->input('id_warung'),
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $pegawai->update($data);
        $this->catatLog("Edit pegawai: " . $request->input('nama'));

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

    /** Setujui pendaftar: aktifkan akun dan tentukan warungnya. */
    public function setujui(Request $request, $id)
    {
        $pegawai = User::where('role', 'pegawai')->where('status', 'menunggu')->findOrFail($id);
        $warung  = Warung::find((int)$request->input('id_warung'));

        if (!$warung) {
            return redirect()->route('pegawai.index')->with('error_msg', 'Pilih warung yang valid sebelum menyetujui.');
        }

        $pegawai->forceFill(['status' => 'aktif', 'id_warung' => $warung->id_warung])->save();
        $this->catatLog("Setujui pegawai: {$pegawai->nama} (warung {$warung->nama_warung})");

        return redirect()->route('pegawai.index')->with('msg', 'setujui');
    }

    /** Tolak pendaftar: hanya akun berstatus 'menunggu' yang bisa dihapus lewat rute ini. */
    public function tolak($id)
    {
        $pegawai = User::where('role', 'pegawai')->where('status', 'menunggu')->findOrFail($id);
        $nama    = $pegawai->nama;
        $pegawai->delete();
        $this->catatLog("Tolak pendaftar: $nama");

        return redirect()->route('pegawai.index')->with('msg', 'tolak');
    }

    /** Aktifkan atau nonaktifkan pegawai (nonaktif langsung kehilangan akses). */
    public function ubahStatus($id)
    {
        $pegawai = User::where('role', 'pegawai')->whereIn('status', ['aktif', 'nonaktif'])->findOrFail($id);
        $baru    = $pegawai->status === 'aktif' ? 'nonaktif' : 'aktif';
        $pegawai->forceFill(['status' => $baru])->save();
        $this->catatLog(($baru === 'aktif' ? 'Aktifkan' : 'Nonaktifkan') . " pegawai: {$pegawai->nama}");

        return redirect()->route('pegawai.index')->with('msg', 'status');
    }
}
