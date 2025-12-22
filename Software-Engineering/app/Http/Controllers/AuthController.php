<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->database = $factory->createDatabase();
    }

    public function showLoginWarga()
    {
        return view('auth.login');
    }

    public function showLoginPetugasAdmin()
    {
        return view('auth.loginPetugasAdmin');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'alamat'   => 'required|string|max:255',
            'telepon'  => [
                'required',
                'regex:/^[0-9]{9,15}$/',   
            ],
            'email'    => [
                'required',
                'string',
                'max:255',
                'email',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            ],       
            'password' => 'required|min:6|confirmed',
        ], [

            'nama.required'      => 'Nama wajib diisi.',
            'alamat.required'    => 'Alamat wajib diisi.',
            'telepon.required'   => 'Nomor telepon wajib diisi.',
            'telepon.regex'      => 'Nomor telepon harus berupa angka dan minimal 9 digit.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.regex'        => 'Format email tidak valid. Contoh: nama@domain.com',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password harus lebih dari 6 karakter (minimal 7 karakter).',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $emailInput = trim(strtolower((string) $request->email));

        $usersRef = $this->database->getReference('users');
        $users    = $usersRef->getValue() ?? [];

        if (is_array($users)) {
            foreach ($users as $uid => $user) {
                if (!is_array($user)) continue;

                $existingEmail = trim(strtolower((string) ($user['email'] ?? '')));

                if ($existingEmail !== '' && $existingEmail === $emailInput) {
                    return back()
                        ->withErrors(['email' => 'Email sudah terdaftar. Silakan gunakan email lain atau lakukan login.'])
                        ->withInput();
                }
            }
        }

        $lastUID = 0;
        if (is_array($users)) {
            foreach ($users as $key => $value) {
                if (preg_match('/^UID(\d{3})$/', (string)$key, $matches)) {
                    $num = intval($matches[1]);
                    if ($num > $lastUID) $lastUID = $num;
                }
            }
        }

        $nextUID = 'UID' . str_pad($lastUID + 1, 3, '0', STR_PAD_LEFT);

        $this->database->getReference('users/' . $nextUID)->set([
            'nama'          => trim((string) $request->nama),
            'alamat'        => trim((string) $request->alamat),
            'telepon'       => trim((string) $request->telepon),
            'email'         => trim(strtolower((string) $request->email)),
            'password_hash' => Hash::make((string) $request->password),
            'role'          => 'warga',
        ]);

        return redirect()->route('login.warga')->with('success', 'Pendaftaran berhasil! Silakan login.');
    }

    public function loginWarga(Request $request)
    {
        return $this->attemptLogin($request, ['warga'], 'warga');
    }

    public function loginPetugasAdmin(Request $request)
    {
        return $this->attemptLogin($request, ['admin', 'petugas'], 'staff');
    }

    /**
     * Helper untuk login + pembatasan role sesuai halaman.
     *
     * @param array $allowedRoles role yang diizinkan untuk halaman login tsb
     * @param string $context 'warga' atau 'staff' (untuk pesan error yang jelas)
     */
    private function attemptLogin(Request $request, array $allowedRoles, string $context)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $emailInput = trim(strtolower((string) $request->email));
        $passwordInput = trim((string) $request->password);

        $users = $this->database->getReference('users')->getValue();

        if (!is_array($users) || empty($users)) {
            return back()->withErrors(['email' => 'Data pengguna tidak ditemukan.']);
        }

        foreach ($users as $uid => $user) {
            if (!is_array($user)) continue;

            $userEmail = trim(strtolower((string)($user['email'] ?? '')));
            if ($userEmail !== $emailInput) continue;

            $role = strtolower(trim((string)($user['role'] ?? '')));

            if (!in_array($role, $allowedRoles, true)) {
                $msg = ($context === 'warga')
                    ? 'Silakan login melalui halaman Petugas/Admin.'
                    : 'Silakan login melalui halaman Warga.';
                return back()->withErrors(['email' => $msg]);
            }

            $storedHash  = $user['password_hash'] ?? null; 
            $storedPlain = $user['password'] ?? null;      

            $ok = false;

            if (is_string($storedHash) && trim($storedHash) !== '') {
                $ok = Hash::check($passwordInput, $storedHash);
            }

            elseif ($storedPlain !== null) {
                $ok = hash_equals(trim((string) $storedPlain), $passwordInput);

                if ($ok) {
                    $this->database->getReference("users/{$uid}")->update([
                        'password_hash' => Hash::make($passwordInput),
                        'password'      => null, 
                    ]);
                }
            }

            if (!$ok) {
                return back()->withErrors(['email' => 'Email atau password salah']);
            }

            session([
                'uid'           => (string) $uid,
                'firebase_uid'   => (string) $uid,
                'nama'          => $user['nama'] ?? null,
                'role'          => $role,
                'role_pengguna' => $role, 
            ]);

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($role === 'petugas') {
                return redirect()->route('petugas.dashboard');
            }

            return redirect()->route('dashboard.warga');
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function logout()
    {
        $role = strtolower(trim((string) session('role', '')));

        session()->flush();

        if (in_array($role, ['admin', 'petugas'], true)) {
            return redirect()->route('login.staff');
        }

        return redirect()->route('login.warga');
    }
}