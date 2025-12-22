<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="firebase-uid" content="{{ session('firebase_uid') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edukasi Sampah - Warga</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      background: rgba(255,255,255,0.9);
      backdrop-filter: blur(10px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }
    .section-title {
      font-weight: 600;
      color: #15803d;
      margin-bottom: 10px;
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

    .img-edukasi {
      border-radius: 14px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.12);
      object-fit: cover;
      width: 100%;
      height: 180px;
    }

    .badge-jenis {
      font-size: 11px;
      border-radius: 999px;
      padding: 4px 10px;
    }

    .card-clickable {
      cursor: pointer;
      transition: transform 0.22s ease, box-shadow 0.22s ease;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .card-clickable:hover {
      transform: translateY(-6px);
      box-shadow: 0 18px 50px rgba(0, 0, 0, 0.22);
    }

    .modal-edukasi {
      border-radius: 18px;
      border: none;
      box-shadow: 0 18px 35px rgba(15, 23, 42, 0.25);
      overflow: hidden;
    }

    .modal-edukasi-header {
      border-bottom: none;
      background: linear-gradient(135deg, #16a34a, #22c55e);
      color: #ffffff;
      padding: 14px 20px;
    }

    .modal-edukasi-header .modal-title {
      font-weight: 600;
      font-size: 18px;
    }

    .modal-edukasi-body {
      background: #f9fafb;
      padding: 18px 22px 22px 22px;
    }

    .modal-edukasi-body p,
    .modal-edukasi-body ul {
      font-size: 14px;
      line-height: 1.5;
    }

    .modal-edukasi-body ul {
      padding-left: 1.2rem;
    }

    .modal-edukasi-body img {
      border-radius: 14px;
      box-shadow: 0 6px 18px rgba(15, 23, 42, 0.18);
    }

    .btn-close-edukasi {
      width: 32px;
      height: 32px;
      border-radius: 999px;
      border: 2px solid rgba(255, 255, 255, 0.85);
      background: rgba(15, 23, 42, 0.18);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      padding: 0;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.35);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-close-edukasi:hover {
      background: #facc15;        
      color: #166534;            
      border-color: transparent;
      transform: translateY(-1px) scale(1.04);
      box-shadow: 0 6px 18px rgba(245, 158, 11, 0.55);
    }

    .btn-close-edukasi:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.65);
    }

    .modal.fade .modal-dialog {
      transform: translateY(10px);
      opacity: 0;
      transition: all 0.22s ease;
    }

    .modal.fade.show .modal-dialog {
      transform: translateY(0);
      opacity: 1;
    }

    .btn-back-map {
      border-radius: 999px;
      border: 2px solid #16a34a;
      padding: 8px 18px;
      font-weight: 600;
      font-size: 14px;
      color: #166534;
      background: #ecfdf3; 
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 4px 10px rgba(22, 101, 52, 0.18);
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .btn-back-map i {
      font-size: 16px;
    }

    .btn-back-map:hover {
      background: #facc15;       
      color: #1f2937;            
      border-color: #f59e0b;
      box-shadow: 0 6px 18px rgba(245, 158, 11, 0.4);
      transform: translateY(-1px);
    }

    html, body {
      height: 100%;
    }

    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;   
    }

    .content-scroll {
      flex: 1 1 auto;
      min-height: 0;           
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 100px;    
    }

    .footer{
      margin-top: 0;
      border-radius: 0;
      width: 100%;
    }

    .modal-edukasi{
      display: flex;
      flex-direction: column;
      max-height: 90vh;     
    }

    .modal-edukasi-header{
      flex: 0 0 auto;       
      position: sticky;     
      top: 0;
      z-index: 2;
    }

    .modal-edukasi-body{
      flex: 1 1 auto;        
      overflow-y: auto;    
    }

  </style>
</head>
<body>

  <div id="notification" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055; width: 350px;"></div>

  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('dashboard.warga') }}">
        <img class="navbar-logo" src="/img/logoku.png" alt="Logo DLH">
        Dinas Lingkungan Hidup - Warga
      </a>
      <div class="d-flex">
        <button id="btnProfil" class="logout-btn me-2">
          <i class="fas fa-user-circle"></i> Profil
        </button>

        <a href="{{ route('warga.edukasi') }}">
          <button class="logout-btn me-2" style="background:#ffc107; color:#000;">
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

<div class="content-scroll">
  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0" style="color:#15803d;">
        <i class="bi bi-book-half me-1"></i> Edukasi Pengelolaan Sampah
      </h3>
      <a href="{{ route('dashboard.warga') }}" class="btn btn-sm btn-back-map">
        <i class="bi bi-arrow-left"></i> Kembali ke Peta
      </a>
    </div>

    <div class="card p-3 mb-4">
      <p class="mb-0">
        Halaman ini berisi informasi singkat mengenai jenis-jenis sampah, contoh, serta aturan
        apa yang boleh dan tidak boleh dibuang ke TPS. Warga diharapkan dapat memilah sampah
        dari rumah untuk mendukung lingkungan yang bersih dan sehat.
      </p>
    </div>

<div class="card p-3 mb-4">
  <h5 class="section-title">
    <i class="bi bi-list-ul me-1"></i>
    Jenis Sampah & Contoh
  </h5>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card card-clickable h-100"
           data-bs-toggle="modal" data-bs-target="#modalOrganik">
        <img src="{{ asset('img/edukasi/organik.jpg') }}" alt="Sampah Organik" class="img-edukasi">
        <div class="card-body">
          <span class="badge bg-success badge-jenis mb-2">Organik</span>
          <p class="mb-1"><strong>Contoh:</strong> sisa makanan, sayur, buah busuk, daun kering.</p>
          <p class="mb-0" style="font-size: 13px;">
            Sampah organik dapat diolah menjadi kompos. Jika memungkinkan, pisahkan di rumah
            sebelum dibawa ke TPS atau bank kompos.
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-clickable h-100"
           data-bs-toggle="modal" data-bs-target="#modalAnorganik">
        <img src="{{ asset('img/edukasi/anorganik.jpg') }}" alt="Sampah Anorganik" class="img-edukasi">
        <div class="card-body">
          <span class="badge bg-primary badge-jenis mb-2">Anorganik Daur Ulang</span>
          <p class="mb-1"><strong>Contoh:</strong> botol plastik, kardus, kertas, kaleng, kaca.</p>
          <p class="mb-0" style="font-size: 13px;">
            Bersihkan dari sisa makanan/minuman agar tidak menimbulkan bau.
            Sampah jenis ini bisa dijual ke bank sampah atau pengepul.
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-clickable h-100"
           data-bs-toggle="modal" data-bs-target="#modalResidu">
        <img src="{{ asset('img/edukasi/residu.jpg') }}" alt="Sampah Residu" class="img-edukasi">
        <div class="card-body">
          <span class="badge bg-secondary badge-jenis mb-2">Residu</span>
          <p class="mb-1"><strong>Contoh:</strong> popok sekali pakai, pembalut, tisu kotor.</p>
          <p class="mb-0" style="font-size: 13px;">
            Residu adalah sampah yang sulit didaur ulang. Tetap dipisahkan dari organik
            dan anorganik daur ulang untuk memudahkan pengelolaan di TPS.
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card card-clickable h-100 mt-3 mt-md-2"
           data-bs-toggle="modal" data-bs-target="#modalB3">
        <img src="{{ asset('img/edukasi/b3.jpg') }}" alt="Sampah B3" class="img-edukasi">
        <div class="card-body">
          <span class="badge bg-warning text-dark badge-jenis mb-2">B3 Rumah Tangga</span>
          <p class="mb-1"><strong>Contoh:</strong> baterai bekas, lampu neon, obat kadaluarsa.</p>
          <p class="mb-0" style="font-size: 13px;">
            Jenis ini berbahaya bagi kesehatan dan lingkungan.
            <strong>Tidak boleh</strong> dibuang ke TPS biasa. Ikuti arahan titik kumpul khusus
            dari DLH atau fasilitas pengelola B3.
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card card-clickable h-100 mt-3 mt-md-2"
           data-bs-toggle="modal" data-bs-target="#modalMedis">
        <img src="{{ asset('img/edukasi/medis.jpg') }}" alt="Sampah Medis" class="img-edukasi">
        <div class="card-body">
          <span class="badge bg-danger badge-jenis mb-2">Sampah Medis</span>
          <p class="mb-1"><strong>Contoh:</strong> jarum suntik, perban bekas, masker dari pasien sakit.</p>
          <p class="mb-0" style="font-size: 13px;">
            <strong>Dilarang</strong> dibuang ke TPS umum. Harus dikembalikan ke fasilitas
            kesehatan atau layanan pengelola sampah medis.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

    <div class="card p-3 mb-4">
      <h5 class="section-title mb-3">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>
        Aturan Buang Sampah ke TPS
      </h5>
      <div class="row g-3">
        <div class="col-md-6">
          <h6 class="text-success mt-2">
            <i class="bi bi-check-circle-fill me-1"></i> Yang Boleh Dibuang ke TPS
          </h6>
          <ul class="mb-0" style="font-size: 14px;">
            <li>Sampah rumah tangga harian yang sudah dipilah (organik, anorganik, residu).</li>
            <li>Sampah dalam kantong yang terikat rapi agar tidak berceceran.</li>
            <li>Volume wajar dari rumah tangga, bukan sampah usaha skala besar.</li>
            <li>Buang sampah sesuai jam yang ditentukan RT/RW atau DLH.</li>
          </ul>
        </div>
        <div class="col-md-6">
          <h6 class="text-danger">
            <i class="bi bi-x-circle-fill me-1"></i> Yang Tidak Boleh Dibuang ke TPS
          </h6>
          <ul class="mb-0" style="font-size: 14px;">
            <li>Sampah B3 dan medis (baterai, oli, jarum suntik, obat, pestisida).</li>
            <li>Puing bangunan, bongkaran rumah, dan sampah proyek dalam jumlah besar.</li>
            <li>Bangkai hewan tanpa pengelolaan khusus.</li>
            <li>Membakar sampah di area TPS (dilarang karena berbahaya bagi kesehatan).</li>
          </ul>
        </div>
      </div>
      <div class="alert alert-success mt-3 mb-0" style="font-size: 13px;">
        <i class="bi bi-info-circle me-1"></i>
        Jika ragu, warga bisa menanyakan ke ketua RT/RW atau petugas DLH sebelum membuang sampah
        yang sifatnya berbahaya atau tidak biasa.
      </div>
    </div>

    <div class="card p-3 mb-4">
      <h5 class="section-title mb-3">
        <i class="bi bi-recycle me-1"></i>
        Tips 3R: Reduce, Reuse, Recycle
      </h5>
      <div class="row g-3">
        <div class="col-md-4">
          <h6 class="text-success mt-2">Reduce (Mengurangi)</h6>
          <ul style="font-size: 14px;">
            <li>Membawa tas belanja sendiri untuk mengurangi kantong plastik.</li>
            <li>Mengurangi penggunaan alat makan sekali pakai.</li>
            <li>Membeli seperlunya untuk menghindari banyak sisa makanan.</li>
          </ul>
        </div>
        <div class="col-md-4">
          <h6 class="text-primary">Reuse (Menggunakan Kembali)</h6>
          <ul style="font-size: 14px;">
            <li>Memakai kembali botol atau toples sebagai wadah.</li>
            <li>Menyumbangkan pakaian layak pakai daripada membuang.</li>
            <li>Memanfaatkan kertas bekas sisi kosong untuk catatan.</li>
          </ul>
        </div>
        <div class="col-md-4">
          <h6 class="text-warning">Recycle (Mendaur Ulang)</h6>
          <ul style="font-size: 14px;">
            <li>Memilah plastik, kertas, dan logam untuk didaur ulang.</li>
            <li>Membuat kompos sederhana dari sisa sayur dan buah.</li>
            <li>Ikut program bank sampah di lingkungan tempat tinggal.</li>
          </ul>
        </div>
      </div>
      <p class="mb-0 text-muted" style="font-size: 13px;">
        Kebiasaan kecil dari rumah, seperti membawa botol minum sendiri dan memilah sampah,
        punya dampak besar untuk mengurangi beban TPS dan TPA.
      </p>
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
      <p><strong>Nama:</strong> <span id="petugasNama">Nama Warga</span></p>
      <p><strong>Email:</strong> <span id="petugasEmail">email@example.com</span></p>
      <p><strong>Role:</strong> <span id="petugasRole">Warga</span></p>
      <p><strong>Alamat:</strong> <span id="petugasStatus">Jl. Contoh Alamat</span></p>
  </div>
</div>

<footer class="footer footer-full">
  <div class="container">
    <p class="mb-0">&copy; 2025 Monitoring Tempat Sampah - DLH Kota Parepare</p>
  </div>
</footer>

  <div class="modal fade" id="modalOrganik" tabindex="-1" aria-labelledby="modalOrganikLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content modal-edukasi">
        <div class="modal-header modal-edukasi-header">
          <h5 class="modal-title" id="modalOrganikLabel">Sampah Organik</h5>
          <button type="button"
                  class="btn-close-edukasi"
                  data-bs-dismiss="modal"
                  aria-label="Tutup">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body modal-edukasi-body">
          <img src="{{ asset('img/edukasi/organik.jpg') }}" alt="Sampah Organik" class="img-fluid mb-3 rounded">
          <p><strong>Pengertian:</strong> Sampah organik adalah sampah yang berasal dari makhluk hidup, seperti tumbuhan, hewan,
            dan sisa makanan. Jenis sampah ini dapat terurai secara alami oleh mikroorganisme
            sehingga bila dikelola dengan baik bisa kembali menjadi tanah yang subur. </p>
          <p><strong>Contoh:</strong> sisa nasi, sayur, buah busuk, daun kering, rumput, ampas kopi/teh.</p>
          <p><strong>Cara pengelolaan di rumah:</strong></p>
          <ul>
            <li>Siapkan wadah khusus sampah organik terpisah dari plastik dan logam.</li>
            <li>Jika memungkinkan, olah menjadi kompos menggunakan lubang biopori atau komposter sederhana.</li>
            <li>Jangan mencampur organik basah dengan popok, pembalut, atau plastik kotor dalam satu kantong.</li>
            <li>Jika belum bisa mengompos, pastikan kantong organik diikat rapat sebelum diangkut petugas.</li>
          </ul>
          <p><strong>Dampak jika tidak dikelola:</strong></p>
          <ul>
            <li>Menimbulkan bau tidak sedap jika bercampur dengan plastik di dalam kantong tertutup.</li>
            <li>Menarik lalat, tikus, dan serangga lain yang berpotensi membawa penyakit.</li>
            <li>Menambah beban TPS dan TPA karena volumenya cukup besar setiap hari.</li>
          </ul>
          <p class="mb-0 text-muted">
            Dengan memisahkan sampah organik, warga membantu mengurangi beban TPA dan mendukung
            program pemanfaatan kompos untuk tanaman di lingkungan sekitar.
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalAnorganik" tabindex="-1" aria-labelledby="modalAnorganikLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content modal-edukasi">
        <div class="modal-header modal-edukasi-header">
          <h5 class="modal-title" id="modalAnorganikLabel">Sampah Anorganik Daur Ulang</h5>
          <button type="button"
                  class="btn-close-edukasi"
                  data-bs-dismiss="modal"
                  aria-label="Tutup">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body modal-edukasi-body">
          <img src="{{ asset('img/edukasi/anorganik.jpg') }}" alt="Sampah Anorganik" class="img-fluid mb-3 rounded">
          <p><strong>Pengertian:</strong> Sampah anorganik adalah sampah yang berasal dari bahan non hayati (plastik, logam, kaca,
            dan hasil olahan pabrik) yang sulit terurai secara alami. Banyak jenis anorganik yang
            sebenarnya masih bisa didaur ulang dan memiliki nilai ekonomi.</p>
          <p><strong>Contoh sampah anorganik yang dapat didaur ulang:</strong></p>
          <ul>
            <li>Botol dan gelas plastik minuman, galon kecil, dan kemasan plastik tebal.</li>
            <li>Kardus, kertas koran, kertas HVS bekas, dan dus makanan kering.</li>
            <li>Kaleng minuman, kaleng susu, dan logam ringan lainnya.</li>
            <li>Botol kaca minuman atau kecap yang masih utuh.</li>
          </ul>
          <p><strong>Dampak jika tidak dikelola:</strong></p>
          <ul>
            <li>Menumpuk di TPA karena butuh puluhan hingga ratusan tahun untuk terurai.</li>
            <li>Plastik yang terbawa ke sungai dan laut dapat membahayakan biota air.</li>
            <li>Membuat lingkungan kotor dan mengurangi keindahan kota.</li>
          </ul>
          <p><strong>Cara pengelolaan di rumah:</strong></p>
          <ul>
            <li>Bersihkan botol dan kaleng dari sisa makanan/minuman sebelum disimpan.</li>
            <li>Kelompokkan berdasarkan jenis (plastik, kertas, logam, kaca) jika memungkinkan.</li>
            <li>Kumpulkan dalam karung atau wadah khusus untuk disetor ke bank sampah atau pengepul.</li>
            <li>Usahakan mengurangi penggunaan plastik sekali pakai dan memilih produk yang bisa diisi ulang.</li>
          </ul>
          <p class="mb-0 text-muted">
            Semakin banyak anorganik yang dipilah dan dijual kembali, semakin sedikit sampah yang berakhir di TPA
            dan warga juga mendapat tambahan penghasilan.
          </p>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modalResidu" tabindex="-1" aria-labelledby="modalResiduLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content modal-edukasi">
        <div class="modal-header modal-edukasi-header">
          <h5 class="modal-title" id="modalResiduLabel">Sampah Residu</h5>
          <button type="button"
                  class="btn-close-edukasi"
                  data-bs-dismiss="modal"
                  aria-label="Tutup">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body modal-edukasi-body">
          <img src="{{ asset('img/edukasi/residu.jpg') }}" alt="Sampah Residu" class="img-fluid mb-3 rounded">
          <p><strong>Pengertian:</strong> Sampah residu adalah sampah sisa yang sulit atau tidak dapat didaur ulang dengan teknologi sederhana.
            Biasanya residu langsung dibawa ke TPA dan hanya bisa ditimbun atau diolah dengan proses khusus.</p>
          <p><strong>Contoh sampah residu rumah tangga:</strong></p>
          <ul>
            <li>Popok sekali pakai dan pembalut bekas.</li>
            <li>Tisu bekas pakai, kapas make up, dan kain pel kotor.</li>
            <li>Bungkus makanan multilayer (keripik, snack, kopi saset, dll.).</li>
            <li>Sisa sapuan lantai dan debu rumah.</li>
          </ul>
          <p><strong>Dampak jika jumlahnya besar:</strong></p>
          <ul>
            <li>Mempercepat penuhnya TPA karena sulit dikurangi dan didaur ulang.</li>
            <li>Berpotensi menimbulkan bau jika tercampur dengan sampah basah.</li>
            <li>Beberapa jenis residu mengandung bahan kimia yang tidak ramah lingkungan.</li>
          </ul>
          <p><strong>Cara pengelolaan di rumah:</strong></p>
          <ul>
            <li>Selalu pisahkan residu dari organik dan anorganik yang bisa didaur ulang.</li>
            <li>Kemas rapi dalam kantong terikat sebelum dibuang ke TPS agar tidak berceceran.</li>
            <li>Mengurangi penggunaan produk sekali pakai (popok, tisu, bungkus snack kecil-kecil).</li>
            <li>Memilih produk isi ulang atau kemasan besar untuk mengurangi jumlah sampah residu.</li>
          </ul>
          <p class="mb-0 text-muted">
            Prinsipnya, semakin sedikit residu yang dihasilkan, semakin mudah kota mengelola sampah dan
            umur TPA bisa lebih panjang.
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalB3" tabindex="-1" aria-labelledby="modalB3Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content modal-edukasi">
        <div class="modal-header modal-edukasi-header">
          <h5 class="modal-title" id="modalB3Label">Sampah B3 Rumah Tangga</h5>
          <button type="button"
                  class="btn-close-edukasi"
                  data-bs-dismiss="modal"
                  aria-label="Tutup">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body modal-edukasi-body">
          <img src="{{ asset('img/edukasi/b3.jpg') }}" alt="Sampah B3" class="img-fluid mb-3 rounded">
          <p><strong>Pengertian:</strong></p>
          <p>
            B3 (Bahan Berbahaya dan Beracun) rumah tangga adalah limbah dari kegiatan di rumah
            yang mengandung zat kimia berbahaya. Jika dibuang sembarangan, B3 dapat mencemari
            tanah, air, dan membahayakan kesehatan manusia maupun hewan.
          </p>
          <p><strong>Contoh B3 rumah tangga:</strong></p>
          <ul>
            <li>Baterai bekas, powerbank rusak, dan aki kecil.</li>
            <li>Lampu neon, lampu hemat energi yang pecah atau tidak terpakai.</li>
            <li>Obat-obatan kadaluarsa, vitamin, dan jamu berbentuk tablet/kapsul.</li>
            <li>Kaleng atau botol pestisida, cat, tiner, dan bahan kimia rumah tangga lainnya.</li>
          </ul>
          <p><strong>Bahaya jika dibuang ke TPS biasa:</strong></p>
          <ul>
            <li>Bocoran cairan baterai dapat mencemari tanah dan air tanah di sekitar TPA.</li>
            <li>Obat kadaluarsa yang tercecer dapat disalahgunakan atau dikonsumsi orang lain.</li>
            <li>Gas dari kaleng aerosol dan pestisida berpotensi merusak kualitas udara dan kesehatan.</li>
          </ul>
          <p><strong>Cara pengelolaan yang disarankan:</strong></p>
          <ul>
            <li>Simpan sementara B3 di rumah pada wadah tertutup dan aman dari jangkauan anak-anak.</li>
            <li>Jangan membakar, membuang ke saluran air, atau mencampur dengan sampah rumah tangga lainnya.</li>
            <li>Ikuti pengumuman dari DLH atau kelurahan terkait titik pengumpulan khusus B3 rumah tangga.</li>
            <li>Untuk obat kadaluarsa, konsultasikan dengan apotek atau fasilitas kesehatan terdekat
                apakah mereka menerima pengembalian limbah obat.</li>
          </ul>
          <p class="mb-0 text-muted">
            Penanganan B3 membutuhkan perhatian khusus. Sedikit ketelitian dari warga akan sangat
            membantu melindungi lingkungan dan kesehatan bersama.
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalMedis" tabindex="-1" aria-labelledby="modalMedisLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content modal-edukasi">
        <div class="modal-header modal-edukasi-header">
          <h5 class="modal-title" id="modalMedisLabel">Sampah Medis Rumah Tangga</h5>
          <button type="button"
                  class="btn-close-edukasi"
                  data-bs-dismiss="modal"
                  aria-label="Tutup">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body modal-edukasi-body">
          <img src="{{ asset('img/edukasi/medis.jpg') }}" alt="Sampah Medis" class="img-fluid mb-3 rounded">
          <p><strong>Pengertian:</strong></p>
          <p>
            Sampah medis rumah tangga adalah limbah yang berasal dari kegiatan perawatan kesehatan di rumah
            (misalnya perawatan pasien sakit, lansia, atau penggunaan alat medis mandiri) yang berpotensi
            mengandung kuman, darah, atau bahan kimia berbahaya.
          </p>
          <p><strong>Contoh sampah medis rumah tangga:</strong></p>
          <ul>
            <li>Jarum suntik dan spuit bekas injeksi insulin atau obat lain.</li>
            <li>Perban, kapas, dan kasa yang terkena darah atau cairan tubuh.</li>
            <li>Masker, sarung tangan medis, dan tisu yang digunakan pasien sakit.</li>
            <li>Alat tes kesehatan sekali pakai (alat cek gula darah, testpack, dll.).</li>
          </ul>
          <p><strong>Risiko jika dibuang ke TPS umum:</strong></p>
          <ul>
            <li>Pemulung atau petugas kebersihan dapat tertusuk jarum bekas yang masih terkontaminasi.</li>
            <li>Penyebaran penyakit melalui darah atau cairan tubuh yang menempel pada perban dan kapas.</li>
            <li>Masker dan sarung tangan bekas dapat menjadi sumber penularan penyakit infeksi saluran
                pernapasan.</li>
          </ul>
          <p><strong>Cara pengelolaan yang lebih aman:</strong></p>
          <ul>
            <li>Kumpulkan jarum dan benda tajam dalam botol plastik tebal atau wadah khusus yang tidak mudah tembus.</li>
            <li>Perban dan kapas bekas dapat dimasukkan ke kantong khusus sebelum diikat rapat.</li>
            <li>Sebisa mungkin, kembalikan limbah medis ke puskesmas, rumah sakit, atau apotek yang memiliki
                layanan penanganan limbah medis.</li>
            <li>Jangan pernah membuka, mencuci, atau menggunakan kembali alat kesehatan sekali pakai.</li>
          </ul>
          <p class="mb-0 text-muted">
            Bila di rumah ada pasien yang menjalani perawatan jangka panjang, warga dapat berkonsultasi
            dengan petugas kesehatan mengenai jadwal dan cara penyerahan limbah medis agar lebih aman.
          </p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="{{ asset('js/dashboard_warga.js') }}"></script>

  <script>
    const btnProfil = document.getElementById('btnProfil');
    const overlay = document.getElementById('overlayBackdrop');
    const profilOverlay = document.getElementById('profilOverlay');
    const closeProfil = document.getElementById('closeProfil');

    if (btnProfil && overlay && profilOverlay && closeProfil) {
      btnProfil.addEventListener('click', () => {
        overlay.style.display = 'block';
        profilOverlay.style.display = 'block';
        setTimeout(() => {
          overlay.style.opacity = '1';
          profilOverlay.style.opacity = '1';
          profilOverlay.style.transform = 'translate(-50%, -50%) scale(1)';
        }, 10);
      });

      const closeFn = () => {
        overlay.style.opacity = '0';
        profilOverlay.style.opacity = '0';
        profilOverlay.style.transform = 'translate(-50%, -50%) scale(0.9)';
        setTimeout(() => {
          overlay.style.display = 'none';
          profilOverlay.style.display = 'none';
        }, 200);
      };

      closeProfil.addEventListener('click', closeFn);
      overlay.addEventListener('click', closeFn);
    }
  </script>
</body>
</html>
