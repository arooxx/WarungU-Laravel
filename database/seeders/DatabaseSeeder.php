<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Data awal WarungU (sama dengan dbwarungu.sql).
 *
 * Password akun demo TIDAK ditulis di kode. Isi SEED_PASSWORD_PEMILIK dan
 * SEED_PASSWORD_PEGAWAI di .env; kalau kosong, dibuat acak dan ditampilkan
 * sekali di terminal.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pwPemilik = env('SEED_PASSWORD_PEMILIK') ?: Str::random(16);
        $pwPegawai = env('SEED_PASSWORD_PEGAWAI') ?: Str::random(16);
        $hPemilik  = Hash::make($pwPemilik);
        $hPegawai  = Hash::make($pwPegawai);

        $t0 = '2026-11-01 07:00:00';

        DB::table('tb_warung')->insert([
            ['id_warung' => 1, 'nama_warung' => 'Warung Berkah', 'alamat' => 'Jl. Merdeka No. 1, Bandung',  'no_hp' => '081234567890', 'created_at' => $t0, 'updated_at' => $t0],
            ['id_warung' => 2, 'nama_warung' => 'Warung Makmur', 'alamat' => 'Jl. Sudirman No. 5, Jakarta', 'no_hp' => '081298765432', 'created_at' => $t0, 'updated_at' => $t0],
            ['id_warung' => 3, 'nama_warung' => 'Warung Bogor',  'alamat' => 'Jl. Pajajaran No. 10, Bogor', 'no_hp' => '081299991234', 'created_at' => $t0, 'updated_at' => $t0],
        ]);

        DB::table('tb_users')->insert([
            ['id_user' => 1, 'nama' => 'Budi Santoso',   'username' => 'pemilik',      'password' => $hPemilik, 'role' => 'pemilik', 'status' => 'aktif',    'id_warung' => 1,    'created_at' => $t0, 'updated_at' => $t0],
            ['id_user' => 2, 'nama' => 'Siti Rahayu',    'username' => 'pegawai1',     'password' => $hPegawai, 'role' => 'pegawai', 'status' => 'aktif',    'id_warung' => 1,    'created_at' => $t0, 'updated_at' => $t0],
            ['id_user' => 3, 'nama' => 'Agus Wahyudi',   'username' => 'pegawai2',     'password' => $hPegawai, 'role' => 'pegawai', 'status' => 'aktif',    'id_warung' => 2,    'created_at' => $t0, 'updated_at' => $t0],
            ['id_user' => 4, 'nama' => 'Rina Wulandari', 'username' => 'pegawai3',     'password' => $hPegawai, 'role' => 'pegawai', 'status' => 'aktif',    'id_warung' => 3,    'created_at' => $t0, 'updated_at' => $t0],
            // Contoh akun yang belum disetujui pemilik (belum bisa login)
            ['id_user' => 5, 'nama' => 'Dewi Lestari',   'username' => 'pegawai_baru', 'password' => $hPegawai, 'role' => 'pegawai', 'status' => 'menunggu', 'id_warung' => null, 'created_at' => '2026-11-10 21:00:00', 'updated_at' => '2026-11-10 21:00:00'],
        ]);

        $kategori = ['Minuman', 'Makanan', 'Snack', 'Sembako', 'Rokok', 'Obat'];
        DB::table('tb_kategori')->insert(array_map(
            fn ($i, $nama) => ['id_kategori' => $i + 1, 'nama_kategori' => $nama, 'created_at' => $t0, 'updated_at' => $t0],
            array_keys($kategori), $kategori
        ));

        // [barcode, nama, beli, jual, satuan_harga, isi_per_pack, bisa_satuan, stok, satuan, kategori, warung]
        $barang = [
            ['8992388251103', 'Aqua 600ml',         2500,  3500,  0,    1,  'tidak', 50,  'botol',   1, 1],
            ['8998009010038', 'Indomie Goreng',     2800,  3500,  0,    1,  'tidak', 100, 'bungkus', 2, 1],
            ['8992388251202', 'Teh Botol 350ml',    3000,  4000,  0,    1,  'tidak', 30,  'botol',   1, 1],
            ['8996001201215', 'Chitato 75gr',       8000,  10000, 0,    1,  'tidak', 20,  'bungkus', 3, 1],
            ['8992388888881', 'Gula Pasir 1kg',     13000, 15000, 0,    1,  'tidak', 8,   'kg',      4, 1],
            ['8992388888882', 'Beras 5kg',          55000, 65000, 0,    1,  'tidak', 10,  'karung',  4, 1],
            ['8992388888883', 'Rokok Surya 12',     18000, 22000, 2000, 12, 'ya',    120, 'batang',  5, 1],
            ['8992388888884', 'Rokok GG Mild',      20000, 25000, 2200, 12, 'ya',    96,  'batang',  5, 1],
            ['8992388251300', 'Pocari Sweat 500ml', 4500,  6000,  0,    1,  'tidak', 25,  'botol',   1, 2],
            ['8998009010100', 'Indomie Rebus',      2800,  3500,  0,    1,  'tidak', 80,  'bungkus', 2, 2],
            ['8992388251401', 'Mizone 500ml',       3500,  5000,  0,    1,  'tidak', 15,  'botol',   1, 3],
        ];
        DB::table('tb_barang')->insert(array_map(fn ($i, $b) => [
            'id_barang' => $i + 1, 'barcode' => $b[0], 'nama_barang' => $b[1], 'harga_beli' => $b[2],
            'harga_jual' => $b[3], 'harga_satuan' => $b[4], 'isi_per_pack' => $b[5], 'bisa_satuan' => $b[6],
            'stok' => $b[7], 'satuan' => $b[8], 'foto' => null, 'id_kategori' => $b[9], 'id_warung' => $b[10],
            'created_at' => $t0, 'updated_at' => $t0,
        ], array_keys($barang), $barang));

        // [barang, jumlah, tanggal, keterangan, user]
        $stok = [
            [7, 60, '2026-11-01 08:00:00', 'Restok awal bulan', 1],
            [5, 10, '2026-11-01 08:05:00', 'Restok awal bulan', 1],
            [9, 24, '2026-11-04 09:30:00', 'Kiriman supplier',  1],
        ];
        DB::table('tb_stok_masuk')->insert(array_map(fn ($i, $s) => [
            'id_stok' => $i + 1, 'id_barang' => $s[0], 'jumlah' => $s[1], 'tanggal' => $s[2],
            'keterangan' => $s[3], 'id_user' => $s[4], 'created_at' => $s[2], 'updated_at' => $s[2],
        ], array_keys($stok), $stok));

        // [tanggal, bayar, metode, user, warung, [[barang, tipe, qty, harga], ...]]
        $transaksi = [
            ['2026-11-02 08:15:00', 20000,  'cash', 2, 1, [[1, 'pack', 2, 3500], [2, 'pack', 3, 3500]]],
            ['2026-11-03 10:40:00', 18000,  'qris', 2, 1, [[4, 'pack', 1, 10000], [3, 'pack', 2, 4000]]],
            ['2026-11-04 14:05:00', 100000, 'cash', 2, 1, [[6, 'pack', 1, 65000], [5, 'pack', 2, 15000]]],
            ['2026-11-05 19:30:00', 30000,  'cash', 1, 1, [[7, 'pack', 1, 22000], [8, 'satuan', 3, 2200]]],
            ['2026-11-06 09:10:00', 30000,  'cash', 3, 2, [[9, 'pack', 2, 6000], [10, 'pack', 4, 3500]]],
            ['2026-11-08 16:20:00', 15000,  'qris', 4, 3, [[11, 'pack', 3, 5000]]],
            ['2026-11-09 11:00:00', 21000,  'qris', 2, 1, [[1, 'pack', 4, 3500], [2, 'pack', 2, 3500]]],
            ['2026-11-10 20:15:00', 15000,  'cash', 3, 2, [[9, 'pack', 1, 6000], [10, 'pack', 2, 3500]]],
        ];
        $log = [
            ['Login', 1, '2026-11-01 07:55:00'],
            ['Tambah stok: Rokok Surya 12 (+60)', 1, '2026-11-01 08:00:00'],
            ['Tambah stok: Gula Pasir 1kg (+10)', 1, '2026-11-01 08:05:00'],
            ['Login', 2, '2026-11-02 08:00:00'],
            ['Tambah stok: Pocari Sweat 500ml (+24)', 1, '2026-11-04 09:30:00'],
        ];
        foreach ($transaksi as $i => [$tgl, $bayar, $metode, $user, $warung, $items]) {
            $id    = $i + 1;
            $total = array_sum(array_map(fn ($it) => $it[2] * $it[3], $items));
            DB::table('tb_transaksi')->insert([
                'id_transaksi' => $id, 'tanggal' => $tgl, 'total' => $total, 'bayar' => $bayar,
                'kembalian' => $bayar - $total, 'metode_pembayaran' => $metode,
                'id_user' => $user, 'id_warung' => $warung, 'created_at' => $tgl, 'updated_at' => $tgl,
            ]);
            foreach ($items as [$barangId, $tipe, $qty, $harga]) {
                DB::table('tb_detail_transaksi')->insert([
                    'id_transaksi' => $id, 'id_barang' => $barangId, 'tipe_penjualan' => $tipe,
                    'qty' => $qty, 'harga' => $harga, 'subtotal' => $qty * $harga,
                    'created_at' => $tgl, 'updated_at' => $tgl,
                ]);
            }
            $log[] = ['Transaksi #' . $id . ' total Rp ' . number_format($total, 0, ',', '.') . ' [' . strtoupper($metode) . ']', $user, $tgl];
        }
        usort($log, fn ($a, $b) => strcmp($a[2], $b[2]));
        DB::table('tb_log_aktivitas')->insert(array_map(fn ($l) => [
            'aktivitas' => $l[0], 'id_user' => $l[1], 'tanggal' => $l[2], 'created_at' => $l[2], 'updated_at' => $l[2],
        ], $log));

        if (! env('SEED_PASSWORD_PEMILIK') || ! env('SEED_PASSWORD_PEGAWAI')) {
            $this->command?->warn('Password acak (catat sekarang, tidak ditampilkan lagi):');
            $this->command?->line("  pemilik : {$pwPemilik}");
            $this->command?->line("  pegawai : {$pwPegawai}");
        }
    }
}
