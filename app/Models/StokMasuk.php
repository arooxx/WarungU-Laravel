<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    protected $table      = 'tb_stok_masuk';
    protected $primaryKey = 'id_stok';
    protected $fillable   = ['id_barang', 'jumlah', 'tanggal', 'keterangan', 'id_user'];
    protected $casts      = ['tanggal' => 'datetime'];

    public function barang() { return $this->belongsTo(Barang::class, 'id_barang', 'id_barang'); }
    public function user()   { return $this->belongsTo(User::class,   'id_user',   'id_user'); }
}
