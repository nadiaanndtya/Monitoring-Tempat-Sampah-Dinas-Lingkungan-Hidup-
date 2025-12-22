<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Carbon\Carbon;

class AdminController extends Controller
{

    private const DLH_LAT = -3.9884196361122606;
    private const DLH_LON = 119.6521610943085;
    
    protected $database;

    public function __construct()
    {
        // koneksi Firebase
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->database = $factory->createDatabase();
    }

    private function validateCoords(Request $request)
    {
        $request->validate([
            'latitude' => [
                'required',
                'regex:/^-?\d+\.\d+$/',
                function ($attr, $value, $fail) {
                    if (!is_numeric($value)) return $fail('Latitude harus angka desimal.');
                    $v = (float) $value;
                    if ($v < -90 || $v > 90) $fail('Latitude tidak valid (rentang -90 sampai 90).');
                }
            ],
            'longitude' => [
                'required',
                'regex:/^-?\d+\.\d+$/',
                function ($attr, $value, $fail) {
                    if (!is_numeric($value)) return $fail('Longitude harus angka desimal.');
                    $v = (float) $value;
                    if ($v < -180 || $v > 180) $fail('Longitude tidak valid (rentang -180 sampai 180).');
                }
            ],
        ], [
            'latitude.regex'  => 'Latitude wajib memakai titik (.) contoh: -4.012345',
            'longitude.regex' => 'Longitude wajib memakai titik (.) contoh: 119.623456',
        ]);
    }

    public function index()
    {

        $users = $this->database->getReference('users')->getValue() ?? [];

        $petugas = [];
        $warga   = [];

        foreach ($users as $key => $user) {
            if (!is_array($user)) continue; 
            $user['id'] = $key;
            $role = $user['role'] ?? '';

            if ($role === 'petugas') {
                $petugas[] = $user;
            } elseif ($role === 'warga') {
                $warga[] = $user;
            }
        }

        $tempatSampah = $this->database->getReference('tempat_sampah')->getValue() ?? [];
        $tpsList = [];
        foreach ($tempatSampah as $key => $tps) {
            $tps['id'] = $key;
            $tpsList[] = $tps;
        }

        $mobil = $this->database->getReference('mobil')->getValue() ?? [];
        $mobilList = [];
        foreach ($mobil as $key => $m) {
            $coord = is_array($m['koordinat'] ?? null) ? $m['koordinat'] : [];
            $lat = $coord['latitude'] ?? self::DLH_LAT;
            $lon = $coord['longitude'] ?? self::DLH_LON;

            $mobilList[] = [
                'id' => $key,
                'plat' => $m['plat'] ?? '-', 
                'koordinat' => $m['koordinat'] ?? [],
            ];
        }

        return view('admin.dashboard', [
            'petugas' => $petugas,
            'warga'   => $warga,
            'tps'     => $tpsList,
            'mobil'   => $mobilList
        ]);
    }

    public function storeByRole(Request $request, $role)
    {
        if (!in_array($role, ['petugas', 'warga'], true)) {
            return redirect()->back()->with('error', 'Role tidak valid!');
        }

        $request->validate(
            [
                'nama'     => 'required|string|max:255',
                'email'    => [
                    'required',
                    'string',
                    'max:255',
                    'email',
                    'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
                ],
                'password' => 'required|min:6',
                'telepon'  => 'nullable|regex:/^[0-9]{8,15}$/',
                'alamat'   => 'nullable|string|max:255',
            ],
            [
                'nama.required'      => 'Nama wajib diisi.',
                'nama.string'        => 'Nama harus berupa teks.',
                'nama.max'           => 'Nama maksimal :max karakter.',

                'email.required'     => 'Email wajib diisi.',
                'email.string'       => 'Email harus berupa teks.',
                'email.max'          => 'Email maksimal :max karakter.',
                'email.email'        => 'Format email tidak valid. Gunakan format seperti nama@example.com.',
                'email.regex'        => 'Format email tidak valid. Email harus mengandung "@" dan titik (.)',

                'password.required'  => 'Password wajib diisi.',
                'password.min'       => 'Password minimal :min karakter.',

                'telepon.regex'      => 'Nomor telepon hanya boleh berisi angka dengan panjang 8–15 digit.',

                'alamat.string'      => 'Alamat harus berupa teks.',
                'alamat.max'         => 'Alamat maksimal :max karakter.',
            ]
        );

        $users = $this->database->getReference('users')->getValue() ?? [];

        foreach ($users as $uid => $user) {
        if (!is_array($user)) continue;

        $existingEmail = $user['email'] ?? '';
        if ($existingEmail !== '' && strcasecmp($existingEmail, $request->email) === 0) {
            return back()
                ->with('error', 'Email sudah digunakan oleh pengguna lain.')
                ->withInput();
            }   
        }

        $lastNumber = 0;
        foreach (array_keys($users) as $key) {
            if (preg_match('/^UID(\d+)$/', $key, $matches)) {
                $num = intval($matches[1]);
                $lastNumber = max($lastNumber, $num);
            }
        }

        $newId = 'UID' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        $this->database->getReference('users/' . $newId)->set([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => strval($request->password), 
            'telepon'  => $request->telepon,
            'alamat'   => $request->alamat,
            'role'     => $role, 
        ]);

        return redirect()->back()->with('success', ucfirst($role) . ' berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'nama'     => 'required|string|max:255',
                'email'    => [
                    'required',
                    'string',
                    'max:255',
                    'email',
                    'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
                ],
                'telepon'  => 'nullable|regex:/^[0-9]{8,15}$/',
                'alamat'   => 'nullable|string|max:255',
                'password' => 'nullable|string|min:6',
            ],
            [
                'nama.required'      => 'Nama wajib diisi.',
                'nama.string'        => 'Nama harus berupa teks.',
                'nama.max'           => 'Nama maksimal :max karakter.',

                'email.required'     => 'Email wajib diisi.',
                'email.string'       => 'Email harus berupa teks.',
                'email.max'          => 'Email maksimal :max karakter.',
                'email.email'        => 'Format email tidak valid. Gunakan format seperti nama@example.com.',
                'email.regex'        => 'Format email tidak valid. Email harus mengandung "@" dan titik (.)',

                'telepon.regex'      => 'Nomor telepon hanya boleh berisi angka dengan panjang 8–15 digit.',

                'alamat.string'      => 'Alamat harus berupa teks.',
                'alamat.max'         => 'Alamat maksimal :max karakter.',

                'password.min'       => 'Password minimal :min karakter.',
            ]
        );

        $users = $this->database->getReference('users')->getValue() ?? [];

        foreach ($users as $uid => $user) {
            if (!is_array($user)) {
                continue;
            }

            if ($uid == $id) {
                continue;
            }

            $existingEmail = $user['email'] ?? '';

            if ($existingEmail !== '' && strcasecmp($existingEmail, $request->email) === 0) {
                return back()
                    ->with('error', 'Email sudah digunakan oleh pengguna lain.')
                    ->withInput();
            }
        }

        $payload = [
            'nama'    => $request->nama,
            'email'   => $request->email,
            'telepon' => $request->telepon,
            'alamat'  => $request->alamat,
        ];

        if ($request->filled('password')) {
            $payload['password'] = strval($request->password);
        }

        $this->database->getReference('users/'.$id)->update($payload);

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->database->getReference('users/'.$id)->remove();
        return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
    }

    public function storeTps(Request $request)
    {
        $request->validate([
        'lokasi'    => 'required|string|max:255',
        'latitude'  => 'required|numeric',
        'longitude' => 'required|numeric',
        ]);

        $this->validateCoords($request);

        $tpsData = $this->database->getReference('tempat_sampah')->getValue() ?? [];
        $lastNumber = 0;

        foreach (array_keys($tpsData) as $key) {
            if (preg_match('/TPSID(\d+)/', $key, $matches)) {
                $num = intval($matches[1]);
                $lastNumber = max($lastNumber, $num);
            }
        }

        $newId = 'TPSID' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        $this->database->getReference('tempat_sampah/'. $newId)->set([
        'lokasi' => $request->lokasi,
        'koordinat' => [
            'latitude' => floatval($request->latitude),
            'longitude' => floatval($request->longitude),
        ],
        'status' => 'kosong',  
        'volume' => 0,        
        'last_update' => now()->toISOString()
        ]);

        return redirect()->back()->with('success', 'Data TPS berhasil ditambahkan!');
    }

    public function updateTps(Request $request, $id)
    {
        $request->validate([
        'lokasi'    => 'required|string|max:255',
        'latitude'  => 'required|numeric',
        'longitude' => 'required|numeric',
        ]);

        $this->validateCoords($request);

        $this->database->getReference('tempat_sampah/'.$id)->update([
            'lokasi' => $request->lokasi,
            'koordinat' => [
                'latitude' => floatval($request->latitude),
                'longitude' => floatval($request->longitude),
            ],
            'last_update' => now()->toISOString()
        ]);

        return redirect()->back()->with('success', 'Data TPS berhasil diperbarui!');
    }

    public function destroyTps($id)
    {
        $this->database->getReference('tempat_sampah/'.$id)->remove();
        return redirect()->back()->with('success', 'Data TPS berhasil dihapus!');
    }

    public function storeMobil(Request $request)
    {
        $request->validate(
            [
                'plat'      => ['required','string','max:20','regex:/^[A-Za-z0-9\s\-]{3,20}$/'],
            ],
            [
                'plat.required' => 'Plat mobil wajib diisi.',
                'plat.regex'    => 'Plat hanya boleh huruf, angka, spasi, atau strip (-). Contoh: B 1234 CD',
                'plat.max'      => 'Plat maksimal :max karakter.',
            ]
        );

        $mobilData = $this->database->getReference('mobil')->getValue() ?? [];

        $lastNumber = 0;
        foreach (array_keys($mobilData) as $key) {
            if (preg_match('/mobilID(\d{3})$/i', $key, $matches)) {
                $num = intval($matches[1]);
                $lastNumber = max($lastNumber, $num);
            }
        }

        $newId = 'mobilID' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        $plat = strtoupper(preg_replace('/\s+/', ' ', trim($request->plat)));

        $this->database->getReference('mobil/'. $newId)->set([
            'plat' => $plat,
            'koordinat' => [
                'latitude' => self::DLH_LAT,
                'longitude' => self::DLH_LON,
            ],
        ]);

        return redirect()->back()->with('success', 'Data mobil berhasil ditambahkan!');
    }

    public function updateMobil(Request $request, $id)
    {
        $request->validate(
            [
                'plat'      => ['required','string','max:20','regex:/^[A-Za-z0-9\s\-]{3,20}$/'],
            ],
            [
                'plat.required' => 'Plat mobil wajib diisi.',
                'plat.regex'    => 'Plat hanya boleh huruf, angka, spasi, atau strip (-). Contoh: B 1234 CD',
                'plat.max'      => 'Plat maksimal :max karakter.',
            ]
        );

        $plat = strtoupper(preg_replace('/\s+/', ' ', trim($request->plat)));

        $this->database->getReference('mobil/'.$id)->update([
            'plat' => $plat,
        ]);

        return redirect()->back()->with('success', 'Data mobil berhasil diperbarui!');
    }

    public function destroyMobil($id)
    {
        $this->database->getReference('mobil/'.$id)->remove();
        return redirect()->back()->with('success', 'Data mobil berhasil dihapus!');
    }

    public function laporan()
    {

        $tz = 'Asia/Makassar';
        $laporanRef  = $this->database->getReference('laporan_pengambilan_riwayat')->getValue() ?? [];
        $laporanList = [];

        foreach ($laporanRef as $tpsId => $riwayatList) {
            if (!is_array($riwayatList)) continue;

            foreach ($riwayatList as $key => $item) {

                if (!is_array($item)) continue;

                $waktuRaw      = trim((string)($item['waktu'] ?? ''));
                $lokasiTps   = trim((string)($item['lokasi_tps'] ?? ($item['lokasi'] ?? '')));
                $petugasNama = trim((string)($item['nama_petugas'] ?? ($item['petugas'] ?? '')));
                $status      = trim((string)($item['status'] ?? ''));
                $volume      = $item['volume_sebelum'] ?? null;

                $waktuTs = strtotime($waktuRaw) ?: 0;
                $volumeInt = is_numeric($volume) ? (int)$volume : null;

                if ($waktuRaw === '' || $waktuRaw === '-') continue;
                if ($lokasiTps === '' || $lokasiTps === '-') continue;
                if ($petugasNama === '' || $petugasNama === '-') continue;

                if (($volumeInt === null || $volumeInt === 0) && ($status === '' || $status === '-')) {
                    continue;
                }

                try {

                    $dtWita = Carbon::parse($waktuRaw)->setTimezone($tz);
                } catch (\Throwable $e) {
                    continue; 
                }

                $laporanList[] = [
                    'id'            => $tpsId,
                    'laporan_ke'    => $key,
                    'lokasi_tps'    => $lokasiTps,
                    'nama_petugas'  => $petugasNama,
                    'petugas'       => $petugasNama,     
                    'volume_sebelum'=> $volumeInt ?? 0,
                    'status'        => $status !== '' ? $status : '-',
                    'waktu'          => $dtWita->format('d M Y, H:i'),

                    '_ts'            => $dtWita->timestamp,
                    ];
            }
        }

        usort($laporanList, function ($a, $b) {
            return ($b['_ts'] ?? 0) <=> ($a['_ts'] ?? 0);
        });

        foreach ($laporanList as &$row) unset($row['_ts']);

        return view('admin.laporanhistoris', [
            'laporan_historis' => $laporanList
        ]);
    }
}