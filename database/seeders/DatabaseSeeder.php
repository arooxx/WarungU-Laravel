<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_warung')->insert([
            ['nama_warung' => 'Warung Berkah',  'alamat' => 'Jl. Merdeka No. 1, Bandung',  'no_hp' => '081234567890', 'created_at' => now(), 'updated_at' => now()],
            ['nama_warung' => 'Warung Makmur',  'alamat' => 'Jl. Sudirman No. 5, Jakarta', 'no_hp' => '081298765432', 'created_at' => now(), 'updated_at' => now()],
            ['nama_warung' => 'Warung Bogor',   'alamat' => 'Jl. Pajajaran No. 10, Bogor', 'no_hp' => '081299991234', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // password: password
        DB::table('tb_users')->insert([
            ['nama' => 'Budi Santoso',  'username' => 'pemilik',  'password' => Hash::make('password'), 'role' => 'pemilik', 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Siti Rahayu',   'username' => 'pegawai1', 'password' => Hash::make('password'), 'role' => 'pegawai', 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Agus Wahyudi',  'username' => 'pegawai2', 'password' => Hash::make('password'), 'role' => 'pegawai', 'id_warung' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Rina Wulandari','username' => 'pegawai3', 'password' => Hash::make('password'), 'role' => 'pegawai', 'id_warung' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('tb_kategori')->insert([
            ['nama_kategori' => 'Minuman',  'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Makanan',  'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Snack',    'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Sembako',  'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Rokok',    'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Obat',     'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('tb_barang')->insert([
            ['barcode' => '8992388251103', 'nama_barang' => 'Aqua 600ml',         'harga_beli' => 2500,  'harga_jual' => 3500,  'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 50,  'satuan' => 'botol',   'foto' => null, 'id_kategori' => 1, 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8998009010038', 'nama_barang' => 'Indomie Goreng',      'harga_beli' => 2800,  'harga_jual' => 3500,  'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 100, 'satuan' => 'bungkus', 'foto' => null, 'id_kategori' => 2, 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8992388251202', 'nama_barang' => 'Teh Botol 350ml',    'harga_beli' => 3000,  'harga_jual' => 4000,  'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 30,  'satuan' => 'botol',   'foto' => null, 'id_kategori' => 1, 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8996001201215', 'nama_barang' => 'Chitato 75gr',       'harga_beli' => 8000,  'harga_jual' => 10000, 'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 20,  'satuan' => 'bungkus', 'foto' => null, 'id_kategori' => 3, 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8992388888881', 'nama_barang' => 'Gula Pasir 1kg',     'harga_beli' => 13000, 'harga_jual' => 15000, 'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 8,   'satuan' => 'kg',      'foto' => null, 'id_kategori' => 4, 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8992388888882', 'nama_barang' => 'Beras 5kg',          'harga_beli' => 55000, 'harga_jual' => 65000, 'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 10,  'satuan' => 'karung',  'foto' => null, 'id_kategori' => 4, 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8992388888883', 'nama_barang' => 'Rokok Surya 12',     'harga_beli' => 18000, 'harga_jual' => 22000, 'harga_satuan' => 2000, 'isi_per_pack' => 12, 'bisa_satuan' => 'ya',    'stok' => 120, 'satuan' => 'batang',  'foto' => null, 'id_kategori' => 5, 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8992388888884', 'nama_barang' => 'Rokok GG Mild',      'harga_beli' => 20000, 'harga_jual' => 25000, 'harga_satuan' => 2200, 'isi_per_pack' => 12, 'bisa_satuan' => 'ya',    'stok' => 96,  'satuan' => 'batang',  'foto' => null, 'id_kategori' => 5, 'id_warung' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8992388251300', 'nama_barang' => 'Pocari Sweat 500ml', 'harga_beli' => 4500,  'harga_jual' => 6000,  'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 25,  'satuan' => 'botol',   'foto' => null, 'id_kategori' => 1, 'id_warung' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8998009010100', 'nama_barang' => 'Indomie Rebus',      'harga_beli' => 2800,  'harga_jual' => 3500,  'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 80,  'satuan' => 'bungkus', 'foto' => null, 'id_kategori' => 2, 'id_warung' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['barcode' => '8992388251401', 'nama_barang' => 'Mizone 500ml',       'harga_beli' => 3500,  'harga_jual' => 5000,  'harga_satuan' => 0,    'isi_per_pack' => 1,  'bisa_satuan' => 'tidak', 'stok' => 15,  'satuan' => 'botol',   'foto' => null, 'id_kategori' => 1, 'id_warung' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
