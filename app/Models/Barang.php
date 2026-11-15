<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table      = 'tb_barang';
    protected $primaryKey = 'id_barang';
    protected $fillable   = [
        'barcode', 'nama_barang', 'harga_beli', 'harga_jual',
        'harga_satuan', 'isi_per_pack', 'bisa_satuan',
        'stok', 'satuan', 'foto', 'id_kategori', 'id_warung',
    ];

    public function kategori() { return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori'); }
    public function warung()   { return $this->belongsTo(Warung::class,   'id_warung',   'id_warung'); }
}
