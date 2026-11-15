<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Warung;

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
        $warungList = Warung::orderBy('nama_warung')->get();
        return view('auth.register', compact('warungList'));
    }

    public function register(Request $request)
    {
        $nama      = trim($request->input('nama', ''));
        $username  = trim($request->input('username', ''));
        $password  = $request->input('password', '');
        $id_warung = (int)$request->input('id_warung', 0);

        if (!$nama || !$username || !$password) {
            return back()->withErrors(['register' => 'Semua field wajib diisi!'])->withInput();
        }
        if (strlen($password) < 6) {
            return back()->withErrors(['register' => 'Password minimal 6 karakter!'])->withInput();
        }
        if (User::where('username', $username)->exists()) {
            return back()->withErrors(['register' => 'Username sudah digunakan!'])->withInput();
        }

        User::create([
            'nama'      => $nama,
            'username'  => $username,
            'password'  => Hash::make($password),
            'role'      => 'pegawai',
            'id_warung' => $id_warung,
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }
}
