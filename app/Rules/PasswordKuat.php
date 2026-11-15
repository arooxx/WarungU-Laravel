<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Aturan password: 8-72 karakter, mengandung huruf dan angka,
 * tidak sama dengan username, dan bukan kata yang terlalu umum.
 */
class PasswordKuat implements ValidationRule
{
    // Kata-kata yang terlalu mudah ditebak (hanya bagian hurufnya yang dibandingkan).
    private const TERLALU_UMUM = [
        'password', 'passw', 'admin', 'qwerty', 'qwertyuiop', 'abcdef', 'abcdefgh',
        'welcome', 'login', 'rahasia', 'indonesia', 'warung', 'warungu', 'kasir',
        'pemilik', 'pegawai', 'letmein', 'iloveyou',
    ];

    public function __construct(private ?string $username = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $v = (string) $value;

        if (mb_strlen($v) < 8) {
            $fail('Password minimal 8 karakter.');
            return;
        }
        if (strlen($v) > 72) {
            $fail('Password maksimal 72 karakter.');
            return;
        }
        if (!preg_match('/[A-Za-z]/', $v) || !preg_match('/\d/', $v)) {
            $fail('Password harus mengandung huruf dan angka.');
            return;
        }
        if ($this->username !== null && mb_strtolower($v) === mb_strtolower($this->username)) {
            $fail('Password tidak boleh sama dengan username.');
            return;
        }
        $huruf = preg_replace('/[^a-z]/', '', mb_strtolower($v));
        if (in_array($huruf, self::TERLALU_UMUM, true)) {
            $fail('Password terlalu umum dan mudah ditebak.');
        }
    }
}
