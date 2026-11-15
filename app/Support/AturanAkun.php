<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class AturanAkun
{
    /** Aturan username: 3-30 karakter, hanya huruf/angka/garis bawah, unik. */
    public static function username($ignoreId = null): array
    {
        $unique = Rule::unique('tb_users', 'username');
        if ($ignoreId) {
            $unique = $unique->ignore($ignoreId, 'id_user');
        }
        return ['required', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9_]+$/', $unique];
    }

    public static function pesan(): array
    {
        return [
            'nama.required'       => 'Nama wajib diisi.',
            'nama.max'            => 'Nama maksimal 100 karakter.',
            'username.required'   => 'Username wajib diisi.',
            'username.min'        => 'Username minimal 3 karakter.',
            'username.max'        => 'Username maksimal 30 karakter.',
            'username.regex'      => 'Username hanya boleh huruf, angka, dan garis bawah (_).',
            'username.unique'     => 'Username sudah digunakan!',
            'password.required'   => 'Password wajib diisi.',
            'password.confirmed'  => 'Konfirmasi password tidak sama.',
            'id_warung.required'  => 'Pilih warung.',
            'id_warung.exists'    => 'Warung tidak valid.',
        ];
    }
}
