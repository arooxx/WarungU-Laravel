<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warung extends Model
{
    protected $table      = 'tb_warung';
    protected $primaryKey = 'id_warung';
    protected $fillable   = ['nama_warung', 'alamat', 'no_hp'];

    public function users()    { return $this->hasMany(User::class,     'id_warung', 'id_warung'); }
    public function barang()   { return $this->hasMany(Barang::class,   'id_warung', 'id_warung'); }
    public function transaksi(){ return $this->hasMany(Transaksi::class,'id_warung', 'id_warung'); }
}
