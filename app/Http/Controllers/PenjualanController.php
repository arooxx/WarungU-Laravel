<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;

class PenjualanController extends BaseController
{
    public function index(Request $request)
    {
        [$idWarungAktif, $semuaWarung, $daftarWarung] = $this->getFilterWarung($request);

        $dari         = $request->input('dari',   now()->startOfMonth()->format('Y-m-d'));
        $sampai       = $request->input('sampai', now()->format('Y-m-d'));
        $filterMetode = in_array($request->input('metode'), ['cash', 'qris']) ? $request->input('metode') : '';

        $transaksi = Transaksi::with(['user', 'warung'])
            ->withCount('detail as jml_item')
            ->when(!$semuaWarung, fn($q) => $q->where('id_warung', $idWarungAktif))
            ->whereBetween(DB::raw('DATE(tanggal)'), [$dari, $sampai])
            ->when($filterMetode, fn($q) => $q->where('metode_pembayaran', $filterMetode))
            ->latest('tanggal')
            ->get();

        $totalPeriode = $transaksi->sum('total');
        $totalCash    = $transaksi->where('metode_pembayaran', 'cash')->sum('total');
        $totalQris    = $transaksi->where('metode_pembayaran', 'qris')->sum('total');
        $jmlCash      = $transaksi->where('metode_pembayaran', 'cash')->count();
        $jmlQris      = $transaksi->where('metode_pembayaran', 'qris')->count();

        $warungParam = $semuaWarung ? '0' : $idWarungAktif;

        return view('penjualan.index', compact(
            'transaksi', 'totalPeriode', 'totalCash', 'totalQris', 'jmlCash', 'jmlQris',
            'dari', 'sampai', 'filterMetode',
            'daftarWarung', 'idWarungAktif', 'semuaWarung', 'warungParam'
        ));
    }

    public function detail(Request $request, $id)
    {
        $user = auth()->user();

        // Pemilik bisa lihat semua, pegawai hanya warungnhya sendiri
        $query = Transaksi::with(['user', 'warung', 'detail.barang']);

        if ($user->isPemilik()) {
            $transaksi = $query->findOrFail($id);
        } else {
            $transaksi = $query->where('id_warung', $user->id_warung)->findOrFail($id);
        }

        return view('penjualan.detail', compact('transaksi'));
    }
}
