<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table      = 'tb_transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $fillable   = ['tanggal', 'total', 'bayar', 'kembalian', 'metode_pembayaran', 'id_user', 'id_warung'];
    protected $casts      = ['tanggal' => 'datetime'];

    public function user()   { return $this->belongsTo(User::class,   'id_user',   'id_user'); }
    public function warung() { return $this->belongsTo(Warung::class, 'id_warung', 'id_warung'); }
    public function detail() { return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi'); }
}
