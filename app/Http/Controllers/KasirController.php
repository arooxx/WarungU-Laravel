<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Warung;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Kategori;

class KasirController extends BaseController
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // FIX: Pemilik pakai kasir_warung — identik dengan fix di native kasir/index.php
        if ($user->isPemilik()) {
            if ($request->has('warung') && (int)$request->warung > 0) {
                session(['kasir_warung' => (int)$request->warung]);
            }
            if (!session()->has('kasir_warung')) {
                session(['kasir_warung' => $user->id_warung]);
            }
            $idWarung = (int)session('kasir_warung');
        } else {
            $idWarung = (int)$user->id_warung;
        }

        $daftarWarungList = $user->isPemilik() ? Warung::orderBy('nama_warung')->get() : collect();
        $infoWarung       = Warung::find($idWarung);

        $daftarBarang = Barang::with('kategori')
            ->where('id_warung', $idWarung)
            ->where('stok', '>', 0)
            ->orderBy('id_kategori')
            ->orderBy('nama_barang')
            ->get();

        $kategoriList = Kategori::whereIn(
            'id_kategori',
            $daftarBarang->pluck('id_kategori')->unique()
        )->orderBy('nama_kategori')->get();

        return view('kasir.index', compact(
            'daftarBarang', 'kategoriList', 'infoWarung',
            'daftarWarungList', 'idWarung'
        ));
    }

    /**
     * Proses transaksi via AJAX POST — identik dengan kasir/proses.php
     */
    public function proses(Request $request)
    {
        $user = auth()->user();

        if ($user->isPemilik()) {
            $idWarung = (int)(session('kasir_warung') ?? $user->id_warung);
        } else {
            $idWarung = (int)$user->id_warung;
        }

        $keranjang        = $request->input('keranjang', []);
        $total            = (int)$request->input('total', 0);
        $bayar            = (int)$request->input('bayar', 0);
        $kembalian        = (int)$request->input('kembalian', 0);
        $metodePembayaran = in_array($request->input('metode_pembayaran'), ['cash', 'qris'])
            ? $request->input('metode_pembayaran') : 'cash';

        if (empty($keranjang)) {
            return response()->json(['status' => 'error', 'pesan' => 'Data tidak valid']);
        }

        if ($metodePembayaran === 'cash' && $bayar < $total) {
            return response()->json(['status' => 'error', 'pesan' => 'Uang bayar kurang dari total']);
        }

        DB::beginTransaction();
        try {
            // =============================================
            // STEP 1: Akumulasi kebutuhan stok per barang
            // =============================================
            $kebutuhan = [];
            foreach ($keranjang as $item) {
                $idBarang   = (int)($item['id']         ?? 0);
                $qty        = (int)($item['qty']        ?? 0);
                $unitPerQty = (int)($item['unitPerQty'] ?? 1);
                if ($qty <= 0 || $unitPerQty <= 0) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'pesan' => 'Data item tidak valid']);
                }
                $kebutuhan[$idBarang] = ($kebutuhan[$idBarang] ?? 0) + ($qty * $unitPerQty);
            }

            // =============================================
            // STEP 2: Validasi stok semua barang (FOR UPDATE = lock row)
            // =============================================
            foreach ($kebutuhan as $idBarang => $dibutuhkan) {
                $barangCek = DB::table('tb_barang')
                    ->where('id_barang', $idBarang)
                    ->where('id_warung', $idWarung)
                    ->lockForUpdate()
                    ->first();

                if (!$barangCek) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'pesan' => 'Barang tidak ditemukan atau bukan milik warung ini']);
                }

                if ((int)$barangCek->stok < $dibutuhkan) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'pesan'  => 'Stok "' . $barangCek->nama_barang . '" tidak cukup! '
                                  . 'Dibutuhkan: ' . $dibutuhkan . ' ' . $barangCek->satuan
                                  . ', tersisa: ' . $barangCek->stok . ' ' . $barangCek->satuan,
                    ]);
                }
            }

            // =============================================
            // STEP 3: Semua lolos — simpan transaksi
            // =============================================
            $transaksi = Transaksi::create([
                'tanggal'           => now(),
                'total'             => $total,
                'bayar'             => $bayar,
                'kembalian'         => $kembalian,
                'metode_pembayaran' => $metodePembayaran,
                'id_user'           => $user->id_user,
                'id_warung'         => $idWarung,
            ]);

            // =============================================
            // STEP 4: Simpan detail & kurangi stok
            // =============================================
            foreach ($keranjang as $item) {
                $idBarang   = (int)($item['id']         ?? 0);
                $qty        = (int)($item['qty']        ?? 0);
                $harga      = (int)($item['harga']      ?? 0);
                $tipe       = in_array($item['tipe'] ?? '', ['pack', 'satuan']) ? $item['tipe'] : 'pack';
                $unitPerQty = (int)($item['unitPerQty'] ?? 1);

                DetailTransaksi::create([
                    'id_transaksi'   => $transaksi->id_transaksi,
                    'id_barang'      => $idBarang,
                    'tipe_penjualan' => $tipe,
                    'qty'            => $qty,
                    'harga'          => $harga,
                    'subtotal'       => $qty * $harga,
                ]);

                Barang::where('id_barang', $idBarang)
                      ->where('id_warung', $idWarung)
                      ->decrement('stok', $qty * $unitPerQty);
            }

            $label = strtoupper($metodePembayaran);
            $this->catatLog("Transaksi #{$transaksi->id_transaksi} total Rp " . number_format($total, 0, ',', '.') . " [$label]");

            DB::commit();

            return response()->json([
                'status'       => 'ok',
                'id_transaksi' => $transaksi->id_transaksi,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'pesan' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
}
