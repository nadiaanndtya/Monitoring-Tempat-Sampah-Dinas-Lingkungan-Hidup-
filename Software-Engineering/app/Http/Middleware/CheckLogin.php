<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Value\Uid;
use Symfony\Component\HttpFoundation\Response;

use function Laravel\Prompts\error;

class CheckLogin
{
   
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('uid')) {
            logger()->info('Middleware: Session kosong, redirect ke login');
            return redirect()->route('login.staff')
                    ->with('error', 'Silakan login terlebih dahulu (Petugas/Admin).');
        }
        
        return $next($request);
    }
}
