<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Warga | DLH Parepare</title>

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

    .hero-copy{
      padding-bottom: 6px;
    }
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
      padding: 30px 28px;
      box-shadow: 0 18px 45px rgba(2, 20, 10, .12);
    }

    .card-top{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:12px;
    }

    .card-head{
      flex:1;              
      min-width: 240px;    
    }

    .card-actions{
      display:flex;
      flex-direction:column;  
      gap:10px;
      align-items:flex-end;   
    }

    @media (max-width: 520px){
      .card-top{ flex-direction:column; }
      .card-actions{
        width:100%;
        align-items:flex-start;
        flex-direction:row;
        flex-wrap:wrap;
      }
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
      margin: 14px 0 18px;
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
      margin-bottom: 14px;
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

    .field{ margin-bottom: 12px; }
    label{
      display:block;
      font-size:12px;
      font-weight:700;
      color: rgba(11,18,32,.78);
      margin: 0 0 6px 4px;
    }

    .control{ position:relative; }
    .control input{
      width:100%;
      padding: 14px 46px 14px 46px;
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
      right:14px;
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

    .row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      margin: 10px 0 12px;
      font-size: 12px;
      color: rgba(81,96,111,.92);
    }
    .row a{
      color: var(--green-800);
      text-decoration:none;
      font-weight:700;
    }
    .row a:hover{ text-decoration:underline; color: var(--green-900); }

    .submit{
      width:100%;
      border:none;
      cursor:pointer;
      padding: 13px 14px;
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

    .footer{
      display:flex;
      justify-content:space-between;
      gap:12px;
      color: rgba(81,96,111,.86);
      font-size:12px;
      padding: 0 6px;
    }

    @media (max-width: 980px){
      .panel{ grid-template-columns: 1fr; }
      .hero{ min-height: 320px; }
      .hero-inner{ padding: 22px; }
      .hero-copy h1{ font-size: 30px; }
      .form-wrap{ padding: 22px; }
    }

    @media (max-width: 520px){
      .card{ padding: 24px 18px; border-radius: 18px; }
      .hero-copy h1{ font-size: 26px; }
    }

    .register{
      margin-top: 18px;
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

    .submit:disabled{
      cursor: wait;
      opacity: .85;
    }

    .submit .spinner{
      width: 18px;
      height: 18px;
      border-radius: 999px;
      border: 2px solid rgba(255,255,255,.45);
      border-top-color: #ffffff;
      display: none;
      animation: spin .65s linear infinite;
    }

    .submit.is-loading .btn-text{
      opacity: 0;
      pointer-events: none;
    }

    .submit.is-loading .spinner{
      display: inline-block;
    }

    /* animasi putar */
    @keyframes spin{
      to{ transform: rotate(360deg); }
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
              <h1>Selamat Datang</h1>
              <p>Dukung lingkungan yang lebih bersih dan sehat.</p>
            </div>
          </div>
        </section>

        <section class="form-wrap">
          <div class="card">

            <div class="card-top">
              <div class="card-head">
                <h2>Silahkan Login</h2>
                <p>Gunakan email dan password yang terdaftar untuk mengakses sistem.</p>
              </div>

              <div class="card-actions">
                <a class="back" href="{{ route('login.staff') }}">
                  <i class="fa-solid fa-user-gear"></i> Login Petugas
                </a>

                <a class="back" href="{{ url('/utama/home') }}">
                  <i class="fa-solid fa-arrow-left"></i> Beranda
                </a>
              </div>
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

            <div class="divider">Form Login</div>

            <form id="loginFormWarga" action="{{ route('login.warga.submit') }}" method="POST" autocomplete="on">
              @csrf

              <div class="field">
                <label for="email">Email</label>
                <div class="control">
                  <i class="fa-solid fa-envelope icon"></i>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan username"
                    required
                  >
                </div>
              </div>

              <div class="field">
                <label for="password">Password</label>
                <div class="control">
                  <i class="fa-solid fa-lock icon"></i>
                  <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                  >
                  <i class="fa-solid fa-eye toggle-eye" id="eye" onclick="togglePassword()"></i>
                </div>
              </div>

              <button class="submit" type="submit" id="btnLogin">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
                <span class="spinner" aria-hidden="true"></span>
              </button>

              <div class="register">
                Belum punya akun? <a href="/auth/register">Daftar di sini</a>
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

    
    function togglePassword(){
      const input = document.getElementById('password');
      const eye   = document.getElementById('eye');
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      eye.classList.toggle('fa-eye', !isPwd);
      eye.classList.toggle('fa-eye-slash', isPwd);
    }

    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('loginFormWarga');
      const btn  = document.getElementById('btnLogin');

      if (form && btn) {
        form.addEventListener('submit', function () {
          btn.disabled = true;

          btn.classList.add('is-loading');
        });
      }
    });
  </script>
</body>
</html>
