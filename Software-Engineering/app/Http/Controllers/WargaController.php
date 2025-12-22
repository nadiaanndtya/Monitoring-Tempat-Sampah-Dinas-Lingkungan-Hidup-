<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WargaController extends Controller
{
    public function dashboard()
    {
        if (!session('uid')) {
            return redirect('/auth/login')->with('error', 'silahkan login terlebih dahulu');
        }
        if (session('role') !== 'warga') {
            return redirect('auth/login')-> with ('error', 'akses tidak diizinkan');
        }
        return view('warga.dashboard');
    }

    public function edukasiSampah()
{
    return view('warga.edukasi_sampah');
}

}
