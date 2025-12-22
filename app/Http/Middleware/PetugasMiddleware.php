<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PetugasMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $role = strtolower(trim((string) session('role', '')));

        if ($role !== 'petugas') {
            return redirect()->route('login.staff')->with('error', 'Silakan login sebagai petugas terlebih dahulu.');
        }

        return $next($request);
    }
}
