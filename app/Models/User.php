<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table      = 'tb_users';
    protected $primaryKey = 'id_user';

    protected $fillable = ['nama', 'username', 'password', 'role', 'id_warung', 'remember_token'];
    protected $hidden   = ['password', 'remember_token'];

    public function warung()   { return $this->belongsTo(Warung::class,  'id_warung', 'id_warung'); }
    public function transaksi(){ return $this->hasMany(Transaksi::class,  'id_user',   'id_user'); }
    public function isPemilik(): bool { return $this->role === 'pemilik'; }

    // Gunakan username bukan email
    public function getAuthIdentifierName() { return 'id_user'; }
}
