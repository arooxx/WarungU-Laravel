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
     * Proses transaksi via AJAX POST.
     *
     * Dari browser HANYA dipakai: id barang, tipe (pack/satuan), qty, bayar, dan metode.
     * Harga, isi per pack, total, dan kembalian SELALU dihitung server dari database,
     * karena data dari browser bisa diubah siapa saja (DevTools, curl).
     */
    public function proses(Request $request)
    {
        $user = auth()->user();

        if ($user->isPemilik()) {
            $idWarung = (int)(session('kasir_warung') ?? $user->id_warung);
        } else {
            $idWarung = (int)$user->id_warung;
        }

        $keranjang        = $request->input('keranjang');
        $bayar            = (int)$request->input('bayar', 0);
        $metodePembayaran = in_array($request->input('metode_pembayaran'), ['cash', 'qris'])
            ? $request->input('metode_pembayaran') : 'cash';

        if (!is_array($keranjang) || empty($keranjang)) {
            return response()->json(['status' => 'error', 'pesan' => 'Data tidak valid']);
        }

        // STEP 1: validasi bentuk data keranjang
        $items = [];
        foreach ($keranjang as $item) {
            $ok       = is_array($item);
            $idBarang = $ok ? (int)($item['id']  ?? 0) : 0;
            $qty      = $ok ? (int)($item['qty'] ?? 0) : 0;
            $tipe     = ($ok && ($item['tipe'] ?? '') === 'satuan') ? 'satuan' : 'pack';
            if ($idBarang <= 0 || $qty <= 0 || $qty > 9999) {
                return response()->json(['status' => 'error', 'pesan' => 'Data item tidak valid']);
            }
            $items[] = ['id' => $idBarang, 'qty' => $qty, 'tipe' => $tipe];
        }

        DB::beginTransaction();
        try {
            // STEP 2: kunci baris barang (urut id agar tidak deadlock) dan ambil data asli dari database
            $ids = array_values(array_unique(array_column($items, 'id')));
            sort($ids);
            $barangMap = [];
            foreach ($ids as $idBarang) {
                $b = DB::table('tb_barang')
                    ->where('id_barang', $idBarang)
                    ->where('id_warung', $idWarung)
                    ->lockForUpdate()
                    ->first();
                if (!$b) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'pesan' => 'Barang tidak ditemukan atau bukan milik warung ini']);
                }
                $barangMap[$idBarang] = $b;
            }

            // STEP 3: harga dan kebutuhan stok dihitung dari database, bukan dari browser
            $kebutuhan = [];
            $baris     = [];
            $total     = 0;
            foreach ($items as $it) {
                $b = $barangMap[$it['id']];
                if ($it['tipe'] === 'satuan') {
                    if ($b->bisa_satuan !== 'ya' || (int)$b->harga_satuan <= 0) {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'pesan' => 'Barang "' . $b->nama_barang . '" tidak bisa dijual satuan']);
                    }
                    $harga = (int)$b->harga_satuan;
                    $unit  = 1;
                } else {
                    $harga = (int)$b->harga_jual;
                    $unit  = max(1, (int)$b->isi_per_pack);
                }
                $kebutuhan[$it['id']] = ($kebutuhan[$it['id']] ?? 0) + ($it['qty'] * $unit);
                $baris[] = ['id' => $it['id'], 'qty' => $it['qty'], 'tipe' => $it['tipe'], 'harga' => $harga, 'unit' => $unit];
                $total  += $it['qty'] * $harga;
            }
            if ($total > 2000000000) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'pesan' => 'Total transaksi terlalu besar']);
            }

            // STEP 4: validasi stok (baris sudah terkunci di step 2)
            foreach ($kebutuhan as $idBarang => $dibutuhkan) {
                $b = $barangMap[$idBarang];
                if ((int)$b->stok < $dibutuhkan) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'pesan'  => 'Stok "' . $b->nama_barang . '" tidak cukup! '
                                  . 'Dibutuhkan: ' . $dibutuhkan . ' ' . $b->satuan
                                  . ', tersisa: ' . $b->stok . ' ' . $b->satuan,
                    ]);
                }
            }

            // STEP 5: pembayaran dihitung dari total versi server
            if ($metodePembayaran === 'qris') {
                $bayar     = $total;
                $kembalian = 0;
            } else {
                if ($bayar < $total) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'pesan' => 'Uang bayar kurang dari total']);
                }
                if ($bayar > 2000000000) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'pesan' => 'Nominal bayar tidak valid']);
                }
                $kembalian = $bayar - $total;
            }

            // STEP 6: simpan transaksi
            $transaksi = Transaksi::create([
                'tanggal'           => now(),
                'total'             => $total,
                'bayar'             => $bayar,
                'kembalian'         => $kembalian,
                'metode_pembayaran' => $metodePembayaran,
                'id_user'           => $user->id_user,
                'id_warung'         => $idWarung,
            ]);

            // STEP 7: simpan detail & kurangi stok (memakai angka dari database)
            foreach ($baris as $row) {
                DetailTransaksi::create([
                    'id_transaksi'   => $transaksi->id_transaksi,
                    'id_barang'      => $row['id'],
                    'tipe_penjualan' => $row['tipe'],
                    'qty'            => $row['qty'],
                    'harga'          => $row['harga'],
                    'subtotal'       => $row['qty'] * $row['harga'],
                ]);

                Barang::where('id_barang', $row['id'])
                      ->where('id_warung', $idWarung)
                      ->decrement('stok', $row['qty'] * $row['unit']);
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
            // Detail error asli hanya masuk ke storage/logs/laravel.log, tidak ditampilkan ke pengguna.
            report($e);
            return response()->json(['status' => 'error', 'pesan' => 'Transaksi gagal disimpan. Silakan coba lagi.']);
        }
    }
}
