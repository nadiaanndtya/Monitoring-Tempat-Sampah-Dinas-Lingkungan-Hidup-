<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrasi | DLH Parepare</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --green-900:#0b3d22;
      --green-800:#0b7a3b;
      --green-700:#0ea44f;

      --accent:#f59e0b;
      --accent-2:#fbbf24;

      --text:#0b1220;
      --muted:#51606f;

      --stroke: rgba(15, 23, 42, .10);
      --shadow: 0 18px 45px rgba(2, 20, 10, .16);

      --radius-xl: 26px;
      --radius-lg: 18px;
    }

    *{ box-sizing:border-box; margin:0; padding:0; }
    html,body{ height:100%; }

    body{
      font-family:'Poppins',sans-serif;
      color:var(--text);
      background:
        radial-gradient(900px 520px at 8% 18%, rgba(22,163,74,.40), transparent 55%),
        radial-gradient(900px 520px at 92% 12%, rgba(14,164,79,.32), transparent 55%),
        radial-gradient(900px 520px at 72% 88%, rgba(245,158,11,.18), transparent 55%),
        linear-gradient(135deg, #e2f6ea, #c8f1d6);
      overflow-x:hidden;
    }

    .blob{
      position:fixed;
      filter: blur(40px);
      opacity:.55;
      z-index:0;
      pointer-events:none;
      border-radius:999px;
    }
    .blob.a{ width:360px; height:360px; left:-120px; top:90px; background: rgba(22,163,74,.35); }
    .blob.b{ width:420px; height:420px; right:-140px; top:-120px; background: rgba(14,164,79,.28); }
    .blob.c{ width:380px; height:380px; right:160px; bottom:-160px; background: rgba(245,158,11,.25); }

    .page{
      position:relative;
      z-index:1;
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 18px;
    }

    .shell{
      width:min(1280px, 100%);
      display:flex;
      flex-direction:column;
      gap:14px;
    }

    .panel{
      background: rgba(255,255,255,.42);
      border: 1px solid rgba(255,255,255,.42);
      backdrop-filter: blur(12px);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow);
      overflow:hidden;
      display:grid;
      grid-template-columns: 1.2fr 1fr;
      min-height: 76vh;
    }

    .hero{
      position:relative;
      background: url("/img/dlh.jpeg") center/cover no-repeat;
      min-height: 520px;
    }
    .hero::after{
      content:"";
      position:absolute;
      inset:0;
      background:
        radial-gradient(900px 520px at 20% 20%, rgba(255,255,255,.06), transparent 60%),
        linear-gradient(180deg, rgba(11,61,34,.28), rgba(11,122,59,.62));
    }

    .hero-inner{
      position:relative;
      z-index:1;
      height:100%;
      display:flex;
      flex-direction:column;
      justify-content:space-between;
      padding: 34px;
      color:#fff;
    }

    .brand-mini{
      display:flex;
      align-items:center;
      gap:10px;
      width: fit-content;
      padding: 10px 12px;
      border-radius: 999px;
      background: rgba(255,255,255,.14);
      border: 1px solid rgba(255,255,255,.18);
    }
    .brand-mini img{ height:36px; width:auto; }
    .brand-mini .btxt{ line-height:1.15; }
    .brand-mini .btxt strong{ display:block; font-size:13px; letter-spacing:.2px; }
    .brand-mini .btxt span{ display:block; font-size:11px; opacity:.9; }

    .hero-copy{ padding-bottom: 6px; }
    .pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 10px 14px;
      border-radius:999px;
      background: rgba(255,255,255,.14);
      border: 1px solid rgba(255,255,255,.18);
      width: fit-content;
      margin-bottom: 12px;
      font-weight:700;
      font-size:13px;
    }
    .hero-copy h1{
      font-size: 42px;
      line-height:1.06;
      letter-spacing:-.5px;
      margin-bottom: 10px;
      text-shadow: 0 14px 40px rgba(0,0,0,.35);
    }
    .hero-copy p{
      max-width: 520px;
      color: rgba(255,255,255,.90);
      line-height:1.55;
      font-size:14px;
    }

    .form-wrap{
      padding: 34px;
      display:flex;
      align-items:center;
      justify-content:center;
      background:
        radial-gradient(700px 420px at 90% 10%, rgba(22,163,74,.16), transparent 55%),
        radial-gradient(700px 420px at 10% 90%, rgba(245,158,11,.12), transparent 55%),
        rgba(255,255,255,.22);
    }

    .card{
      width: min(460px, 100%);
      background: rgba(255,255,255,.92);
      border: 1px solid var(--stroke);
      border-radius: 22px;
      padding: 28px 26px;
      box-shadow: 0 18px 45px rgba(2, 20, 10, .12);
    }

    @media (min-width: 980px){
      .card{
        height: min(640px, 72vh);
        padding: 24px 24px;
        display:flex;
        flex-direction:column;
        min-height:0;
      }
    }

    .card-top{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:10px;
      margin-bottom: 10px;
    }

    .card-head h2{
      font-size: 26px;
      color: var(--green-800);
      letter-spacing:-.2px;
      margin-bottom: 6px;
    }
    .card-head p{
      font-size: 13px;
      color: var(--muted);
      line-height:1.5;
    }

    .back{
      display:inline-flex;
      align-items:center;
      gap:8px;
      text-decoration:none;
      font-weight:800;
      font-size:12px;
      color: var(--green-800);
      padding: 10px 12px;
      border-radius: 999px;
      background: rgba(14,164,79,.10);
      border: 1px solid rgba(14,164,79,.18);
      transition:.18s ease;
      white-space:nowrap;
    }
    .back:hover{
      transform: translateY(-1px);
      background: rgba(14,164,79,.14);
      color: var(--green-900);
    }

    .divider{
      display:flex;
      align-items:center;
      gap:12px;
      margin: 12px 0 14px;
      color: rgba(81,96,111,.85);
      font-size:12px;
    }
    .divider::before, .divider::after{
      content:"";
      height:1px;
      flex:1;
      background: rgba(15,23,42,.10);
    }

    .alert{
      padding: 12px 14px;
      border-radius: 14px;
      margin-bottom: 12px;
      font-size: 13px;
      border: 1px solid rgba(15,23,42,.08);
    }
    .alert-success{
      background: rgba(232,245,233,.75);
      color: #166534;
      border-left: 5px solid #22c55e;
    }
    .alert-danger{
      background: rgba(254,226,226,.75);
      color: #991b1b;
      border-left: 5px solid #ef4444;
    }

    .reg-form{
      display:flex;
      flex-direction:column;
      min-height:0; 
    }

    .card-scroll{
      flex:1;
      overflow:auto;
      min-height:0;
      padding-right: 6px; 
    }

    .field{ margin-bottom: 10px; }
    label{
      display:block;
      font-size:12px;
      font-weight:700;
      color: rgba(11,18,32,.78);
      margin: 0 0 5px 4px;
    }

    .control{ position:relative; }
    .control input{
      width:100%;
      padding: 12px 44px 12px 44px;
      border-radius: 16px;
      border: 1px solid rgba(15,23,42,.14);
      outline:none;
      font-size:14px;
      transition: .18s ease;
      background: rgba(255,255,255,.95);
    }
    .control input:focus{
      border-color: rgba(14,164,79,.55);
      box-shadow: 0 0 0 4px rgba(14,164,79,.16);
    }
    .control .icon{
      position:absolute;
      left:14px;
      top:50%;
      transform: translateY(-50%);
      color: rgba(81,96,111,.75);
      font-size:14px;
    }
    .control .toggle-eye{
      position:absolute;
      right:12px;
      top:50%;
      transform: translateY(-50%);
      color: rgba(81,96,111,.75);
      cursor:pointer;
      padding: 6px 8px;
      border-radius: 10px;
      transition: .18s ease;
    }
    .control .toggle-eye:hover{
      background: rgba(15,23,42,.06);
      color: rgba(11,18,32,.85);
    }

    .card-actions{
      margin-top: 10px;
    }

    .submit{
      width:100%;
      border:none;
      cursor:pointer;
      padding: 12px 14px;
      border-radius: 16px;
      font-weight:800;
      font-size:14px;
      color:#ffffff;
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      box-shadow: 0 18px 40px rgba(11, 245, 11, 0.22);
      transition:.18s ease;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
    }
    .submit:hover{ transform: translateY(-3px); filter:saturate(1.06); }

    .register{
      margin-top: 12px;
      font-size: 14px;
      color: rgba(81,96,111,.95);
      text-align:center;
    }
    .register a{
      color: var(--green-800);
      font-weight: 800;
      text-decoration:none;
    }
    .register a:hover{
      text-decoration: underline;
      color: var(--green-900);
    }

    .footer{
      display:flex;
      justify-content:space-between;
      gap:12px;
      color: rgba(81,96,111,.86);
      font-size:12px;
      padding: 0 6px;
    }

    .card-scroll::-webkit-scrollbar{ width: 8px; }
    .card-scroll::-webkit-scrollbar-thumb{
      background: rgba(15,23,42,.14);
      border-radius: 999px;
    }

    @media (max-width: 980px){
      .panel{ grid-template-columns: 1fr; }
      .hero{ min-height: 320px; }
      .hero-inner{ padding: 22px; }
      .hero-copy h1{ font-size: 30px; }
      .form-wrap{ padding: 22px; }

      .card{ height:auto; display:block; }
      .card-scroll{ overflow:visible; padding-right:0; }
    }

    @media (max-width: 520px){
      .card{ padding: 22px 18px; border-radius: 18px; }
      .hero-copy h1{ font-size: 26px; }
    }
  </style>
</head>

<body>
  <div class="blob a"></div>
  <div class="blob b"></div>
  <div class="blob c"></div>

  <div class="page">
    <div class="shell">

      <div class="panel">
        <section class="hero">
          <div class="hero-inner">
            <div class="brand-mini">
              <img src="{{ asset('img/logo1.png') }}" alt="Logo DLH">
              <div class="btxt">
                <strong>Parepare</strong>
                <span>Sistem Monitoring Tempat Sampah IoT</span>
              </div>
            </div>

            <div class="hero-copy">
              <div class="pill">
                <i class="fa-solid fa-location-dot"></i>
                Dinas Lingkungan Hidup Kota Parepare
              </div>
              <h1>Registrasi Akun</h1>
              <p>Buat akun baru untuk mengakses sistem monitoring dan layanan DLH.</p>
            </div>
          </div>
        </section>

        <section class="form-wrap">
          <div class="card">

            <div class="card-top">
              <div class="card-head">
                <h2>Buat Akun Baru</h2>
                <p>Isi data Anda dengan benar</p>
              </div>

              <a class="back" href="/auth/login">
                <i class="fa-solid fa-right-to-bracket"></i> Login
              </a>
            </div>

            @if (session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
              <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                  <div>{{ $error }}</div>
                @endforeach
              </div>
            @endif

            @if (session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="divider">Form Registrasi</div>

            <form id="registerForm" class="reg-form" action="{{ route('register') }}" method="POST" autocomplete="on">
              @csrf

              <div class="card-scroll">

                <div class="field">
                  <label for="nama">Nama Lengkap</label>
                  <div class="control">
                    <i class="fa-solid fa-user icon"></i>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                  </div>
                </div>

                <div class="field">
                  <label for="email">Email</label>
                  <div class="control">
                    <i class="fa-solid fa-envelope icon"></i>
                    <input type="email" id="email" name="email" placeholder="Masukkan email aktif" required>
                  </div>
                </div>

                <div class="field">
                  <label for="telepon">Nomor Telepon</label>
                  <div class="control">
                    <i class="fa-solid fa-phone icon"></i>
                    <input type="text" id="telepon" name="telepon" placeholder="Masukkan nomor telepon" required>
                  </div>
                </div>

                <div class="field">
                  <label for="alamat">Alamat</label>
                  <div class="control">
                    <i class="fa-solid fa-location-crosshairs icon"></i>
                    <input type="text" id="alamat" name="alamat" placeholder="Masukkan alamat" required>
                  </div>
                </div>

                <div class="field">
                  <label for="password">Password</label>
                  <div class="control">
                    <i class="fa-solid fa-lock icon"></i>
                    <input type="password" id="password" name="password" placeholder="Password minimal 6 digit" required>
                    <i class="fa-solid fa-eye toggle-eye" id="eyePass" onclick="togglePassword('password','eyePass')"></i>
                  </div>
                </div>

                <div class="field" style="margin-bottom:0;">
                  <label for="confirm_password">Konfirmasi Password</label>
                  <div class="control">
                    <i class="fa-solid fa-lock icon"></i>
                    <input type="password" id="confirm_password" name="password_confirmation" placeholder="Ulangi password" required>
                    <i class="fa-solid fa-eye toggle-eye" id="eyeConf" onclick="togglePassword('confirm_password','eyeConf')"></i>
                  </div>
                </div>

              </div>

              <div class="card-actions">
                <button class="submit" type="submit">
                  <i class="fa-solid fa-user-plus"></i> Daftar
                </button>

                <div class="register">
                  Sudah punya akun? <a href="/auth/login">Masuk di sini</a>
                </div>
              </div>
            </form>

          </div>
        </section>
      </div>

      <div class="footer">
        <span>© {{ date('Y') }} DLH Kota Parepare</span>
      </div>

    </div>
  </div>

  <script>
    function togglePassword(inputId, eyeId){
      const input = document.getElementById(inputId);
      const eye   = document.getElementById(eyeId);
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      eye.classList.toggle('fa-eye', !isPwd);
      eye.classList.toggle('fa-eye-slash', isPwd);
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
      const pass = document.getElementById('password').value;
      const confirm = document.getElementById('confirm_password').value;
      if (pass !== confirm) {
        e.preventDefault();
        alert('Konfirmasi password tidak sesuai!');
      }
    });
  </script>
</body>
</html>
