<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WargaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('role') || session('role') !== 'warga') {
            return redirect('/auth/login')->with('error', 'Silakan login sebagai warga terlebih dahulu.');
        }

        return $next($request);
    }
}
