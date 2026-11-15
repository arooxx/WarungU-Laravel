<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dicek di SETIAP permintaan: akun yang dinonaktifkan pemilik langsung
 * kehilangan akses, termasuk yang masih punya sesi atau cookie "Ingat saya".
 */
class EnsureUserAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['login' => 'Akun Anda tidak aktif. Hubungi pemilik warung.']);
        }

        return $next($request);
    }
}
