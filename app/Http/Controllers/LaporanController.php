<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\Barang;

class LaporanController extends BaseController
{
    public function index(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $dari   = $request->input('dari',   now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->input('sampai', now()->format('Y-m-d'));
        $jenis  = $request->input('jenis',  'penjualan');

        $dataLaporan  = collect();
        $totalLaporan = 0;

        if ($jenis === 'penjualan') {
            $dataLaporan = Transaksi::with(['user', 'warung'])
                ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
                ->whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])
                ->latest('tanggal')->get();
            $totalLaporan = $dataLaporan->sum('total');

        } elseif ($jenis === 'stok') {
            $dataLaporan = Barang::with(['kategori', 'warung'])
                ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
                ->orderBy('stok')
                ->get();

        } elseif ($jenis === 'terlaris') {
            $dataLaporan = DB::table('tb_detail_transaksi as dt')
                ->join('tb_barang as b',     'dt.id_barang',    '=', 'b.id_barang')
                ->join('tb_transaksi as t',  'dt.id_transaksi', '=', 't.id_transaksi')
                ->join('tb_warung as w',     't.id_warung',     '=', 'w.id_warung')
                ->selectRaw('b.nama_barang, w.nama_warung, SUM(dt.qty) as total_qty, SUM(dt.subtotal) as total_pendapatan')
                ->when(!$semuaWarung, fn($q) => $q->where('t.id_warung', $idWarungAktif))
                ->whereBetween(DB::raw('DATE(t.tanggal)'), [$dari, $sampai])
                ->groupBy('dt.id_barang', 't.id_warung', 'b.nama_barang', 'w.nama_warung')
                ->orderByDesc('total_qty')
                ->limit(20)
                ->get();

        } elseif ($jenis === 'hampir_habis') {
            $dataLaporan = Barang::with(['kategori', 'warung'])
                ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
                ->where('stok', '<', self::STOK_MIN)
                ->orderBy('stok')
                ->get();
        }

        $warungParam = $semuaWarung ? '0' : $idWarungAktif;

        $judulMap = [
            'penjualan'    => 'Laporan Penjualan',
            'stok'         => 'Laporan Stok Barang',
            'terlaris'     => 'Barang Terlaris',
            'hampir_habis' => 'Barang Hampir Habis',
        ];

        return view('laporan.index', compact(
            'dataLaporan', 'totalLaporan', 'jenis', 'judulMap',
            'dari', 'sampai',
            'daftarWarung', 'idWarungAktif', 'semuaWarung', 'warungParam'
        ));
    }

    public function exportExcel(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $dari   = $request->input('dari',   now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->input('sampai', now()->format('Y-m-d'));
        $jenis  = $request->input('jenis',  'penjualan');

        $filename = "laporan_{$jenis}_{$dari}_sd_{$sampai}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($jenis, $idWarungAktif, $semuaWarung, $dari, $sampai) {
            $handle = fopen('php://output', 'w');
            // BOM untuk Excel agar utf-8 terbaca
            fputs($handle, "\xEF\xBB\xBF");

            if ($jenis === 'penjualan') {
                fputcsv($handle, ['No', 'Tanggal', 'Warung', 'Kasir', 'Metode', 'Total', 'Bayar', 'Kembalian']);
                $rows = Transaksi::with(['user', 'warung'])
                    ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
                    ->whereBetween(\DB::raw('DATE(tanggal)'), [$dari, $sampai])
                    ->latest('tanggal')->get();
                foreach ($rows as $i => $r) {
                    fputcsv($handle, [
                        $i + 1,
                        $r->tanggal->format('d/m/Y H:i'),
                        $r->warung->nama_warung ?? '-',
                        $r->user->nama         ?? '-',
                        strtoupper($r->metode_pembayaran),
                        $r->total, $r->bayar, $r->kembalian,
                    ]);
                }
            } elseif ($jenis === 'stok') {
                fputcsv($handle, ['No', 'Barcode', 'Nama Barang', 'Kategori', 'Warung', 'Stok', 'Satuan', 'Harga Jual']);
                $rows = Barang::with(['kategori','warung'])
                    ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
                    ->orderBy('stok')->get();
                foreach ($rows as $i => $r) {
                    fputcsv($handle, [
                        $i + 1, $r->barcode, $r->nama_barang,
                        $r->kategori->nama_kategori ?? '-',
                        $r->warung->nama_warung     ?? '-',
                        $r->stok, $r->satuan, $r->harga_jual,
                    ]);
                }
            } elseif ($jenis === 'terlaris') {
                fputcsv($handle, ['No', 'Nama Barang', 'Warung', 'Qty Terjual', 'Total Pendapatan']);
                $rows = \DB::table('tb_detail_transaksi as dt')
                    ->join('tb_barang as b', 'dt.id_barang', '=', 'b.id_barang')
                    ->join('tb_transaksi as t', 'dt.id_transaksi', '=', 't.id_transaksi')
                    ->join('tb_warung as w', 't.id_warung', '=', 'w.id_warung')
                    ->selectRaw('b.nama_barang, w.nama_warung, SUM(dt.qty) as total_qty, SUM(dt.subtotal) as total_pendapatan')
                    ->when(!$semuaWarung, fn($q) => $q->where('t.id_warung', $idWarungAktif))
                    ->whereBetween(\DB::raw('DATE(t.tanggal)'), [$dari, $sampai])
                    ->groupBy('dt.id_barang', 't.id_warung', 'b.nama_barang', 'w.nama_warung')
                    ->orderByDesc('total_qty')->limit(20)->get();
                foreach ($rows as $i => $r) {
                    fputcsv($handle, [$i + 1, $r->nama_barang, $r->nama_warung, $r->total_qty, $r->total_pendapatan]);
                }
            } elseif ($jenis === 'hampir_habis') {
                fputcsv($handle, ['No', 'Barcode', 'Nama Barang', 'Kategori', 'Warung', 'Stok', 'Satuan']);
                $rows = Barang::with(['kategori','warung'])
                    ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
                    ->where('stok', '<', self::STOK_MIN)->orderBy('stok')->get();
                foreach ($rows as $i => $r) {
                    fputcsv($handle, [
                        $i + 1, $r->barcode, $r->nama_barang,
                        $r->kategori->nama_kategori ?? '-',
                        $r->warung->nama_warung     ?? '-',
                        $r->stok, $r->satuan,
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
