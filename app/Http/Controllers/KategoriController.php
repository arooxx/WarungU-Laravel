<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends BaseController
{
    public function index(Request $request)
    {
        $error = '';

        if ($request->isMethod('POST')) {
            $nama = trim($request->input('nama_kategori', ''));
            if (!$nama) {
                $error = 'Nama kategori wajib diisi!';
            } else {
                Kategori::create(['nama_kategori' => $nama]);
                return redirect()->route('kategori.index')->with('msg', 'tambah');
            }
        }

        $kategori = Kategori::withCount('barang as jml_barang')
            ->orderBy('nama_kategori')->get();

        return view('kategori.index', compact('kategori', 'error'));
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $nama = trim($request->input('nama_kategori', ''));
        if (!$nama) {
            return back()->withErrors(['edit' => 'Nama kategori wajib diisi!'])->withInput();
        }
        Kategori::findOrFail($id)->update(['nama_kategori' => $nama]);
        return redirect()->route('kategori.index')->with('msg', 'edit');
    }

    public function destroy($id)
    {
        Kategori::findOrFail($id)->delete();
        return redirect()->route('kategori.index')->with('msg', 'hapus');
    }
}
