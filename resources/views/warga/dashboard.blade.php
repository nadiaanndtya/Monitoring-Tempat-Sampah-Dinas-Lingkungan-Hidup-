<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="firebase-uid" content="{{ session('firebase_uid') }}">
  <meta name="csrf-token" content="{{csrf_token()}}">
  <title>Dashboard Warga</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #f3f9f6, #e9f5ec);
      font-family: 'Poppins', sans-serif;
    }

    .navbar {
      background: linear-gradient(90deg, #16a34a, #15803d);
      padding: 12px 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .navbar-brand {
      color: #fff !important;
      font-weight: 600;
      font-size: 19px;
      display: flex;
      align-items: center;
      letter-spacing: 0.5px;
      gap: 12px;
    }
    .navbar-logo {
      background:#ffffff;
      border-radius: 14px;
      padding:6px;
      width:48px; height:48px;
      object-fit:contain;
      display:block;
      box-shadow: 0 10px 18px rgba(0,0,0,.18);
    }

    .logout-btn {
      background: rgba(255,255,255,0.15);
      border: none;
      padding: 6px 14px;
      border-radius: 20px;
      color: #fff;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .logout-btn:hover {
      background: #ffc107;
      color: #000;
    }

    .card {
      border: none;
      border-radius: 16px;
      background: rgba(255,255,255,0.8);
      backdrop-filter: blur(10px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }
    .card h4 {
      font-weight: 600;
      color: #15803d;
    }

    #map {
      height: 550px;
      width: 100%;
      border-radius: 14px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
      margin-top: 12px;
    }

    .footer {
      background: linear-gradient(90deg, #16a34a, #15803d);
      color: #ffffff;
      text-align: center;
      padding: 14px;
      margin-top: 30px;
      border-radius: 10px;
      font-size: 14px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      
    }
  </style>
</head>
<body>

  <div id="notification" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055; width: 350px;"></div>

  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img class="navbar-logo" src="/img/logoku.png" alt="Logo DLH">
        Dinas Lingkungan Hidup - Warga
      </a>
      <div class="d-flex">
        <button id="btnProfil" class="logout-btn me-2" style="position: relative; index: 2050;">
          <i class="fas fa-user-circle"></i> Profil
        </button>

        {{-- Tombol baru: Edukasi Sampah --}}
        <a href="{{ route('warga.edukasi') }}">
          <button class="logout-btn me-2">
            <i class="bi bi-book-half"></i> Edukasi Sampah
          </button>
        </a>

        <a href="{{ route('logout') }}">
          <button class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
          </button>
        </a>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <div class="card p-3">
      <h4 class="mb-3">Peta Lokasi TPS & Mobil Pengangkut</h4>
      <div id="map"></div>
    </div>
  </div>

  <div id="overlayBackdrop" style="
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1990;
      transition: opacity 0.3s ease;
  "></div>

  <div id="profilOverlay" style="
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.8);
      width: 360px;
      background: rgba(255,255,255,0.97);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 12px 30px rgba(0,0,0,0.3);
      z-index: 2000;
      opacity: 0;
      transition: all 0.3s ease;
  ">
      <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Profil Warga</h5>
          <button id="closeProfil" class="btn btn-sm btn-outline-secondary">&times;</button>
      </div>
      <div class="text-center mb-3">
          <img src="/img/user.jpg" alt="Profil" style="width:80px; height:80px; border-radius:50%; object-fit:cover;">
      </div>
      <p><strong>Nama:</strong> <span id="petugasNama">Nama Petugas</span></p>
      <p><strong>Email:</strong> <span id="petugasEmail">email@example.com</span></p>
      <p><strong>Role:</strong> <span id="petugasRole">Petugas</span></p>
      <p><strong>Alamat:</strong> <span id="petugasStatus">Jl Agussalim</span></p>
  </div>

  <div class="container">
    <footer class="footer">
      <p>&copy; 2025 Monitoring Tempat Sampah - DLH Kota Parepare</p>
    </footer>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="{{ asset('js/dashboard_warga.js') }}"></script>
  <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
</body>
</html>