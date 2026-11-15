<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PemilikMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'pemilik') {
            return redirect()->route('dashboard')->with('error_akses', 'Akses ditolak. Halaman ini khusus pemilik.');
        }

        return $next($request);
    }
}
