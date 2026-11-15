<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Rules\PasswordKuat;
use App\Support\AturanAkun;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $username = trim($request->input('username', ''));
        $password = $request->input('password', '');

        if (!$username || !$password) {
            return back()->withErrors(['login' => 'Username dan password wajib diisi!'])->withInput();
        }

        // Batasi tebak-tebakan password: maksimal 5 kali gagal per menit
        // untuk kombinasi username + alamat IP.
        $kunci = Str::lower($username) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            $detik = RateLimiter::availableIn($kunci);
            return back()
                ->withErrors(['login' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik."])
                ->withInput();
        }

        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($kunci, 60);
            return back()->withErrors(['login' => 'Username atau password salah!'])->withInput();
        }

        // Password benar, tapi akun belum boleh dipakai (pesan hanya diberikan setelah password benar,
        // supaya orang lain tidak bisa menebak status akun).
        if ($user->status !== 'aktif') {
            $pesan = $user->status === 'menunggu'
                ? 'Akun Anda masih menunggu persetujuan pemilik warung.'
                : 'Akun Anda dinonaktifkan. Hubungi pemilik warung.';
            return back()->withErrors(['login' => $pesan])->withInput();
        }

        RateLimiter::clear($kunci);

        // "Ingat saya" memakai mekanisme bawaan Laravel (cookie terenkripsi, token
        // dirotasi saat logout). Auth::login() sudah mengganti ID sesi otomatis.
        Auth::login($user, $request->boolean('remember_me'));

        // Catat log
        \DB::table('tb_log_aktivitas')->insert([
            'aktivitas' => 'Login',
            'id_user'   => $user->id_user,
            'tanggal'   => now(),
        ]);

        // Reset filter warung
        session()->forget(['filter_warung', 'kasir_warung']);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Pendaftaran publik. Pendaftar SELALU menjadi pegawai berstatus 'menunggu' dan belum punya warung;
     * role, status, dan warung tidak pernah diambil dari input. Pemilik yang menyetujui.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'     => ['required', 'string', 'max:100'],
            'username' => AturanAkun::username(),
            'password' => ['required', 'confirmed', new PasswordKuat($request->input('username'))],
        ], AturanAkun::pesan());

        if ($validator->fails()) {
            return back()
                ->withErrors(['register' => $validator->errors()->first()])
                ->withInput($request->only('nama', 'username'));
        }

        (new User())->forceFill([
            'nama'      => $request->input('nama'),
            'username'  => $request->input('username'),
            'password'  => Hash::make($request->input('password')),
            'role'      => 'pegawai',
            'status'    => 'menunggu',
            'id_warung' => null,
        ])->save();

        return redirect()->route('login')
            ->with('success', 'Pendaftaran berhasil. Akun Anda baru bisa dipakai setelah disetujui pemilik warung.');
    }
}
