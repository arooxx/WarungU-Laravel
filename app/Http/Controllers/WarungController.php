<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Warung;

class WarungController extends BaseController
{
    public function index(Request $request)
    {
        $error = '';

        // Tambah warung dari halaman yang sama (persis seperti native)
        if ($request->isMethod('POST')) {
            $nama_warung = trim($request->input('nama_warung', ''));
            $alamat      = trim($request->input('alamat', ''));
            $no_hp       = trim($request->input('no_hp', ''));

            if (!$nama_warung) {
                $error = 'Nama warung wajib diisi!';
            } else {
                Warung::create(['nama_warung' => $nama_warung, 'alamat' => $alamat, 'no_hp' => $no_hp]);
                return redirect()->route('warung.index')->with('msg', 'tambah');
            }
        }

        $warung = DB::table('tb_warung as w')
            ->selectRaw("w.*,
                (SELECT COUNT(*) FROM tb_users WHERE id_warung=w.id_warung AND role='pegawai') as jml_pegawai,
                (SELECT COUNT(*) FROM tb_barang  WHERE id_warung=w.id_warung) as jml_barang")
            ->orderBy('w.nama_warung')
            ->get();

        return view('warung.index', compact('warung', 'error'));
    }

    public function edit($id)
    {
        $warung = Warung::findOrFail($id);
        return view('warung.edit', compact('warung'));
    }

    public function update(Request $request, $id)
    {
        $nama_warung = trim($request->input('nama_warung', ''));
        $alamat      = trim($request->input('alamat', ''));
        $no_hp       = trim($request->input('no_hp', ''));

        if (!$nama_warung) {
            return back()->withErrors(['edit' => 'Nama warung wajib diisi!'])->withInput();
        }

        Warung::findOrFail($id)->update(['nama_warung' => $nama_warung, 'alamat' => $alamat, 'no_hp' => $no_hp]);
        return redirect()->route('warung.index')->with('msg', 'edit');
    }

    public function destroy($id)
    {
        Warung::findOrFail($id)->delete();
        return redirect()->route('warung.index')->with('msg', 'hapus');
    }
}
