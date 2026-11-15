<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use App\Models\Warung;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    const STOK_MIN = 10;

    /**
     * Identik dengan helper.php getFilterWarung() di native.
     * Return: [id_warung_aktif, semua_warung, daftar_warung]
     */
    protected function getFilterWarung($request = null): array
    {
        $daftarWarung = Warung::orderBy('nama_warung')->get();
        $user         = auth()->user();

        // Pegawai: selalu warungnya sendiri
        if (!$user->isPemilik()) {
            return [(int)$user->id_warung, false, $daftarWarung];
        }

        // Pemilik: baca dari request/session
        if ($request && $request->has('warung')) {
            $val = $request->input('warung');
            session(['filter_warung' => ($val == '0' || $val == 'semua') ? 0 : (int)$val]);
        }

        if (!session()->has('filter_warung')) {
            session(['filter_warung' => 0]); // default semua warung
        }

        $filter  = (int)session('filter_warung');
        $semua   = ($filter === 0);
        $idAktif = $semua ? 0 : $filter;

        return [$idAktif, $semua, $daftarWarung];
    }

    /**
     * Rupiah format — identik dengan helper.php rupiah()
     */
    protected function rupiah(int $angka): string
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    /**
     * Catat log aktivitas — identik dengan helper.php catatLog()
     */
    protected function catatLog(string $aktivitas): void
    {
        \DB::table('tb_log_aktivitas')->insert([
            'aktivitas' => $aktivitas,
            'id_user'   => auth()->id(),
            'tanggal'   => now(),
        ]);
    }
}
