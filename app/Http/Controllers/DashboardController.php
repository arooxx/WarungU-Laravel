<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\Barang;

class DashboardController extends BaseController
{
    public function index(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $namaWarungFilter = 'Semua Warung';
        if (!$semuaWarung) {
            $found = $daftarWarung->firstWhere('id_warung', $idWarungAktif);
            if ($found) $namaWarungFilter = $found->nama_warung;
        }

        // Query helper — sama dengan buildWhereWarung di native
        $warungWhere = fn($q, $alias = null) => $semuaWarung ? $q : $q->where(($alias ? $alias . '.' : '') . 'id_warung', $idWarungAktif);

        // Pendapatan hari ini
        $pendapatanHari = Transaksi::when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->whereDate('tanggal', today())->sum('total');

        // Pendapatan bulan ini
        $pendapatanBulan = Transaksi::when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total');

        // Total transaksi hari ini
        $totalTransaksi = Transaksi::when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->whereDate('tanggal', today())->count();

        // Stok hampir habis
        $hampirHabis = Barang::when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->where('stok', '<', self::STOK_MIN)->count();

        // Grafik 7 hari — identik dengan native
        $grafikRaw = DB::table('tb_transaksi as t')
            ->selectRaw('DATE(t.tanggal) as tgl, COALESCE(SUM(t.total),0) as total')
            ->when(!$semuaWarung, fn($q) => $q->where('t.id_warung', $idWarungAktif))
            ->where('t.tanggal', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(t.tanggal)'))
            ->orderBy('tgl')
            ->get();

        $labelGrafik = $grafikRaw->pluck('tgl')->map(fn($d) => date('d/m', strtotime($d)))->toArray();
        $valueGrafik = $grafikRaw->pluck('total')->map(fn($v) => (int)$v)->toArray();

        // Pie metode pembayaran hari ini
        $pieData = DB::table('tb_transaksi')
            ->selectRaw('metode_pembayaran, COUNT(*) as jml')
            ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->whereDate('tanggal', today())
            ->groupBy('metode_pembayaran')
            ->pluck('jml', 'metode_pembayaran');

        $pieCash = (int)($pieData['cash'] ?? 0);
        $pieQris = (int)($pieData['qris'] ?? 0);

        // List stok kritis
        $listHampir = Barang::with('kategori')
            ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->where('stok', '<', self::STOK_MIN)
            ->orderBy('stok')->limit(8)->get();

        // Transaksi terbaru
        $transaksiBaru = Transaksi::with(['user'])
            ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->latest('tanggal')->limit(7)->get();

        $warungParam = $semuaWarung ? '0' : $idWarungAktif;

        return view('dashboard.index', compact(
            'pendapatanHari', 'pendapatanBulan', 'totalTransaksi', 'hampirHabis',
            'labelGrafik', 'valueGrafik', 'pieCash', 'pieQris',
            'listHampir', 'transaksiBaru',
            'daftarWarung', 'idWarungAktif', 'semuaWarung', 'namaWarungFilter', 'warungParam'
        ));
    }
}
