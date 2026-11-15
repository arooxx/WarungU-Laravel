<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table      = 'tb_detail_transaksi';
    protected $primaryKey = 'id_detail';
    protected $fillable   = ['id_transaksi', 'id_barang', 'tipe_penjualan', 'qty', 'harga', 'subtotal'];

    public function barang()    { return $this->belongsTo(Barang::class,    'id_barang',    'id_barang'); }
    public function transaksi() { return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi'); }
}
