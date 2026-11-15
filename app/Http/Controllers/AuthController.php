<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
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
        $username    = trim($request->input('username', ''));
        $password    = $request->input('password', '');
        $remember_me = $request->has('remember_me');

        if (!$username || !$password) {
            return back()->withErrors(['login' => 'Username dan password wajib diisi!'])->withInput();
        }

        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withErrors(['login' => 'Username atau password salah!'])->withInput();
        }

        Auth::login($user, false);

        // Remember me — simpan token ke cookie (sama persis dengan native)
        if ($remember_me) {
            $token = bin2hex(random_bytes(32));
            $user->update(['remember_token' => $token]);
            Cookie::queue('remember_token', $token, 60 * 24 * 30); // 30 hari
        }

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

    public function logout()
    {
        // Hapus remember_token dari DB
        if (Auth::check()) {
            Auth::user()->update(['remember_token' => null]);
        }
        Auth::logout();
        session()->flush();
        Cookie::queue(Cookie::forget('remember_token'));
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
