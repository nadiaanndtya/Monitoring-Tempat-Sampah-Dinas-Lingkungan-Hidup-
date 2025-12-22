<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengambilan Riwayat</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="/img/logo1.png" alt="Logo DLH">
            <h2>DLH Admin <small>Manajemen Data</small></h2>
        </div>
        <ul class="menu">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.laporanhistoris') }}" class="active"><i class="fas fa-history"></i> Laporan Pengambilan</a></li>
            <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header class="main-header">
            <h1>Laporan Pengambilan Sampah</h1>
            <p>Riwayat semua pengambilan sampah dari TPS</p>
        </header>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu Pelaporan</th>
                        <th>ID TPS</th>
                        <th>Lokasi TPS</th>
                        <th>Volume Sebelum Pengangkutan</th>
                        <th>Nama Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan_historis as $index => $lap)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td> {{ optional($lap['waktu'] && $lap['waktu'] != '-' ? \Carbon\Carbon::parse($lap['waktu']) : null)->format('d M Y, H:i') ?? '-' }}</td>
                            <td>{{ $lap['id'] ?? '-' }}</td>
                            <td>{{ $lap['lokasi_tps'] ?? '-' }}</td>
                            <td>{{ $lap['volume_sebelum'] ?? '-' }}</td>
                            <td>{{ $lap['nama_petugas'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="text-align:center;">Belum ada data laporan pengambilan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
