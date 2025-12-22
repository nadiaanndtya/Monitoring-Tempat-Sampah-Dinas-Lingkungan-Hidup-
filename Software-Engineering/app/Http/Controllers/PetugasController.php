<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PetugasController extends Controller
{

    public function dashboard()
    {
        if (!session('uid')) {
            return redirect('/auth/login')->with('error', 'silahkan login terlebih dahulu');
        }
        if (session('role') !== 'petugas') {
            return redirect('auth/login')-> with ('error', 'akses tidak diizinkan');
        }
        return view('petugas.dashboard');
    
    }
}