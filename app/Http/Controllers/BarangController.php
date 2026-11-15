<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Warung;
use App\Models\StokMasuk;

class BarangController extends BaseController
{
    public function index(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $filter = $request->input('filter', '');

        $barang = Barang::with(['kategori', 'warung'])
            ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->when($filter === 'hampir_habis', fn($q) => $q->where('stok', '<', self::STOK_MIN))
            ->orderBy('nama_barang')
            ->get();

        $warungParam = $semuaWarung ? '0' : $idWarungAktif;

        return view('barang.index', compact(
            'barang', 'daftarWarung', 'idWarungAktif', 'semuaWarung', 'warungParam', 'filter'
        ));
    }

    public function create(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $user         = auth()->user();
        $kategoriList = Kategori::orderBy('nama_kategori')->get();

        // Pemilik bisa pilih warung mana, pegawai hanya warungnya
        $warungList = $user->isPemilik()
            ? Warung::orderBy('nama_warung')->get()
            : collect([$user->warung]);

        // Pre-select warung
        $idWarungSimpan = $semuaWarung ? 0 : $idWarungAktif;
        if ($user->role === 'pegawai') {
            $idWarungSimpan = $user->id_warung;
        }

        return view('barang.tambah', compact('kategoriList', 'warungList', 'idWarungSimpan', 'semuaWarung'));
    }

    public function store(Request $request)
    {
        $bisaSatuan = $request->input('bisa_satuan', 'tidak');

        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'harga_beli'  => 'required|integer|min:0',
            'harga_jual'  => 'required|integer|min:0',
            'stok'        => 'required|integer|min:0',
            'id_warung'   => 'required|exists:tb_warung,id_warung',
        ]);

        if ($bisaSatuan === 'ya') {
            // Pakai field dari boxSatuan
            $satuan      = $request->input('satuan_kecil', 'batang');
            $isiPerPack  = (int)$request->input('isi_per_pack', 1);
            $hargaSatuan = (int)$request->input('harga_satuan', 0);
        } else {
            // Pakai field dari boxSatuanBiasa
            $satuan      = $request->input('satuan_biasa', 'pcs');
            $isiPerPack  = 1;
            $hargaSatuan = 0;
        }

        Barang::create([
            'barcode'      => $request->input('barcode'),
            'nama_barang'  => $request->input('nama_barang'),
            'harga_beli'   => (int)$request->input('harga_beli', 0),
            'harga_jual'   => (int)$request->input('harga_jual', 0),
            'harga_satuan' => $hargaSatuan,
            'isi_per_pack' => $isiPerPack,
            'bisa_satuan'  => $bisaSatuan,
            'stok'         => (int)$request->input('stok', 0),
            'satuan'       => $satuan,
            'id_kategori'  => $request->input('id_kategori'),
            'id_warung'    => $request->input('id_warung'),
        ]);

        $this->catatLog("Tambah barang: " . $request->input('nama_barang'));

        return redirect()->route('barang.index')->with('msg', 'tambah');
    }

    public function edit(Request $request, $id)
    {
        $barang       = Barang::findOrFail($id);
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $warungList   = auth()->user()->isPemilik()
            ? Warung::orderBy('nama_warung')->get()
            : collect([auth()->user()->warung]);

        return view('barang.edit', compact('barang', 'kategoriList', 'warungList'));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'harga_beli'  => 'required|integer|min:0',
            'harga_jual'  => 'required|integer|min:0',
            'stok'        => 'required|integer|min:0',
            'satuan'      => 'required|string|max:20',
            'id_kategori' => 'required|exists:tb_kategori,id_kategori',
        ]);

        $data = $request->only([
            'barcode', 'nama_barang', 'harga_beli', 'harga_jual',
            'harga_satuan', 'isi_per_pack', 'stok', 'satuan', 'id_kategori',
        ]);
        $data['bisa_satuan'] = $request->has('bisa_satuan') ? 'ya' : 'tidak';

        $barang->update($data);
        $this->catatLog("Edit barang: " . $barang->nama_barang);

        return redirect()->route('barang.index')->with('msg', 'edit');
    }

    public function destroy($id)
    {
        // FIX: Cek apakah sudah ada di transaksi (sama dengan hapus.php native)
        $barang = Barang::findOrFail($id);

        $sudahDipakai = \DB::table('tb_detail_transaksi')
            ->where('id_barang', $id)->count();

        if ($sudahDipakai > 0) {
            return redirect()->route('barang.index')->with('msg', 'tidak_bisa_hapus');
        }

        $barang->delete();
        $this->catatLog("Hapus barang: " . $barang->nama_barang);

        return redirect()->route('barang.index')->with('msg', 'hapus');
    }

    public function showTambahStok(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $user       = auth()->user();
        $barangList = Barang::with('warung')
            ->when(!$user->isPemilik(), fn($q) => $q->where('id_warung', $user->id_warung))
            ->when($user->isPemilik() && !$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->orderBy('id_warung')
            ->orderBy('nama_barang')
            ->get();

        $warungParam = $semuaWarung ? '0' : $idWarungAktif;

        return view('barang.tambah_stok', compact(
            'barangList', 'daftarWarung', 'idWarungAktif', 'semuaWarung', 'warungParam'
        ));
    }

    public function prosesStok(Request $request)
    {
        $request->validate([
            'id_barang'  => 'required|exists:tb_barang,id_barang',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        StokMasuk::create([
            'id_barang'  => $request->id_barang,
            'jumlah'     => $request->jumlah,
            'tanggal'    => now(),
            'keterangan' => $request->keterangan,
            'id_user'    => auth()->user()->id_user,
        ]);

        Barang::where('id_barang', $request->id_barang)->increment('stok', $request->jumlah);

        $b = Barang::find($request->id_barang);
        $this->catatLog("Tambah stok {$b->nama_barang}: +{$request->jumlah} {$b->satuan}");

        return redirect()->route('barang.tambah-stok')->with('msg', 'stok_ok');
    }
}
