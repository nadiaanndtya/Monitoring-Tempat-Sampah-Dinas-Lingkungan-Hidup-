<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Home DLH Parepare</title>

  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root{
      --green-950:#04301a;
      --green-900:#064e2a;
      --green-800:#0b7a3b;
      --green-700:#0ea44f;
      --green-600:#16a34a;

      --amber:#f59e0b;

      --text:#0b1220;
      --muted:#526170;

      --card:#ffffff;
      --bg:#ffffff;
      --line: rgba(11,122,59,.14);

      --shadow: 0 16px 40px rgba(2, 20, 10, .10);
      --shadow-strong: 0 26px 70px rgba(2, 20, 10, .16);

      --radius: 18px;
      --radius-2: 22px;

      --ring: 0 0 0 4px rgba(245,158,11,.25);
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    html{scroll-behavior:smooth}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Helvetica Neue", sans-serif;
      color: var(--text);
      background:
        radial-gradient(1050px 650px at 10% 12%, rgba(14,164,79,.22), transparent 60%),
        radial-gradient(920px 620px at 92% 18%, rgba(11,122,59,.18), transparent 62%),
        radial-gradient(980px 720px at 40% 95%, rgba(22,163,74,.14), transparent 66%),
        linear-gradient(180deg, #ffffff 0%, #eaf8ef 42%, #ffffff 100%);
    }

    a{color:inherit; text-decoration:none}
    .container{width:min(1140px, 92%); margin-inline:auto}
    .sr-only{
      position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden;
      clip:rect(0,0,0,0); border:0;
    }

    .nav{
      position: sticky; top:0; z-index: 50;
      background: linear-gradient(90deg, rgba(6,78,42,.92), rgba(14,164,79,.92));
      border-bottom: 1px solid rgba(255,255,255,.18);
      box-shadow: 0 10px 24px rgba(2,20,10,.14);
      backdrop-filter: blur(10px);
    }
    .nav-inner{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      padding:12px 0;
    }

    .brand{
      display:flex; align-items:center; gap:12px;
      font-weight:900; letter-spacing:.2px; color:#fff;
      min-width: 260px;
    }
    .brand-logo{
      background:#ffffff;
      border-radius: 14px;
      padding:6px;
      width:48px; height:48px;
      object-fit:contain;
      display:block;
      box-shadow: 0 10px 18px rgba(0,0,0,.18);
    }
    .brand small{
      display:block;
      color: rgba(255,255,255,.88);
      font-weight:700;
      margin-top:2px;
      letter-spacing: .1px;
      font-size: 12.5px;
    }

    .nav-links{
      display:flex; align-items:center; gap:8px;
      padding:6px;
      border-radius: 999px;
      background: rgba(255,255,255,.10);
      border: 1px solid rgba(255,255,255,.18);
    }
    .nav-links a{
      padding:10px 12px;
      border-radius: 999px;
      color:#fff;
      opacity:.96;
      font-weight:800;
      transition: .2s ease;
      outline: none;
    }
    .nav-links a:hover{ background: rgba(255,255,255,.14); transform: translateY(-1px); }
    .nav-links a:focus-visible{ box-shadow: var(--ring); }

    .nav-cta{
      display:flex; gap:10px; align-items:center;
    }

    .btn{
      display:inline-flex; align-items:center; gap:8px;
      padding:10px 14px;
      border-radius: 999px;
      font-weight:900;
      border: 1px solid rgba(255,255,255,.28);
      background: rgba(255,255,255,.16);
      color:#fff;
      white-space:nowrap;
      transition: .2s ease;
      box-shadow: 0 10px 18px rgba(2,20,10,.12);
      outline:none;
    }
    .btn i{ font-size: 1.05em; line-height: 1; }
    .btn:hover{
      background: rgba(255,255,255,.24);
      transform: translateY(-2px);
    }
    .btn:active{ transform: translateY(0px); }
    .btn:focus-visible{ box-shadow: var(--ring); }

    .btn.primary{
      background: var(--amber);
      border-color: rgba(245,158,11,.75);
      color: #0b1220;
    }
    .btn.primary:hover{
      filter: brightness(0.98);
      transform: translateY(-2px);
    }

    .menu-btn{
      display:none;
      width:44px; height:44px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.25);
      background: rgba(255,255,255,.16);
      color:#fff;
      align-items:center; justify-content:center;
      box-shadow: 0 10px 18px rgba(2,20,10,.12);
      cursor:pointer;
      outline:none;
    }
    .menu-btn:focus-visible{ box-shadow: var(--ring); }

    .drawer-backdrop{
      position: fixed; inset: 0;
      background: rgba(2, 20, 10, .35);
      opacity: 0; pointer-events: none;
      transition: .2s ease;
      z-index: 60;
    }
    .drawer{
      position: fixed;
      right: 14px; top: 70px;
      width: min(360px, calc(100% - 28px));
      background: rgba(255,255,255,.92);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 18px;
      box-shadow: var(--shadow-strong);
      transform: translateY(-10px);
      opacity: 0;
      pointer-events: none;
      transition: .2s ease;
      z-index: 70;
      overflow:hidden;
      backdrop-filter: blur(10px);
    }
    .drawer .drawer-inner{ padding: 14px; }
    .drawer a.link{
      display:flex; align-items:center; justify-content:space-between;
      padding: 12px 12px;
      border-radius: 14px;
      font-weight:900;
      color: var(--text);
      transition:.15s ease;
    }
    .drawer a.link:hover{ background: rgba(14,164,79,.10); }
    .drawer .divider{ height:1px; background: rgba(0,0,0,.06); margin:10px 0; }
    .drawer .drawer-cta{ display:grid; gap:10px; margin-top: 10px; }
    .drawer .btn{
      justify-content:center;
      border-color: rgba(11,122,59,.18);
      background: rgba(14,164,79,.10);
      color: var(--text);
      box-shadow: none;
    }
    .drawer .btn.primary{ background: var(--amber); border-color: rgba(245,158,11,.65); }

    body.menu-open .drawer-backdrop{ opacity: 1; pointer-events: auto; }
    body.menu-open .drawer{
      opacity: 1;
      transform: translateY(0px);
      pointer-events: auto;
    }

    .hero{
      padding: 84px 0 36px;
      min-height: calc(100vh - 76px);
      display:flex; align-items:center;
      position: relative;
    }
    .hero-grid{
      display:grid;
      grid-template-columns: 1.18fr .82fr;
      gap: 38px;
      align-items:center;
    }

    .kicker{
      display:inline-flex;
      align-items:center;
      gap:10px;
      padding:8px 12px;
      border-radius: 999px;
      background: rgba(14,164,79,.10);
      border: 1px solid rgba(11,122,59,.18);
      color: #0b1220;
      font-weight:900;
      font-size: 12.5px;
      margin-bottom: 14px;
    }
    .kicker i{ color: var(--green-800); }

    .hero h1{
      margin:0 0 12px;
      font-size: clamp(36px, 3.8vw, 56px);
      line-height:1.06;
      letter-spacing:-.8px;
      color:#0b1220;
    }
    .hero p{
      margin:0;
      max-width: 64ch;
      color: var(--muted);
      line-height:1.75;
      font-size: 16.5px;
    }

    .hero-actions{
      display:flex; gap:12px; flex-wrap:wrap;
      margin-top: 18px;
    }
    .hero-actions .btn{
      border-color: rgba(11,122,59,.18);
      background: rgba(255,255,255,.65);
      color: var(--text);
      box-shadow: var(--shadow);
    }
    .hero-actions .btn:hover{ background: rgba(255,255,255,.90); }
    .hero-actions .btn.primary{ background: var(--amber); border-color: rgba(245,158,11,.70); }

    .illus{
      display:flex;
      align-items:center;
      justify-content:flex-end;
      position: relative;
      padding: 0;
    }
    .illus::before{
      content:"";
      position:absolute;
      inset: -10% -8%;
      background: radial-gradient(circle at 60% 40%, rgba(14,164,79,.20), transparent 55%);
      filter: blur(2px);
      z-index:0;
    }
    .hero-img{
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 560px;
      height: auto;
      display:block;
      object-fit: contain;
      filter: drop-shadow(0 24px 34px rgba(2,20,10,.16));
      border-radius: var(--radius-2);
    }

    .section{ padding: 92px 0 72px; }
    .section-head{
      display:flex; align-items:flex-end; justify-content:space-between;
      gap: 16px;
      margin-bottom: 18px;
    }
    .section-title{
      font-size: 30px;
      letter-spacing: .2px;
      margin: 0;
      color:#0b1220;
    }
    .section-sub{
      margin: 0;
      color: var(--muted);
      max-width: 70ch;
      line-height: 1.6;
    }
    .section-title::after{
      content:"";
      display:block;
      width:64px;
      height:4px;
      margin-top:10px;
      border-radius:999px;
      background: linear-gradient(90deg, var(--green-700), var(--amber));
      opacity:.95;
    }

    .features{
      display:grid;
      grid-template-columns: repeat(3, minmax(0,1fr));
      gap: 14px;
      margin-top: 18px;
    }
    .feature{
      background: rgba(255,255,255,.82);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 16px 16px;
      transition: .2s ease;
    }
    .feature:hover{ transform: translateY(-3px); box-shadow: var(--shadow-strong); }
    .feature .f-ico{
      width:44px; height:44px;
      border-radius: 14px;
      display:grid; place-items:center;
      background: rgba(14,164,79,.12);
      border:1px solid rgba(11,122,59,.18);
      box-shadow: 0 10px 16px rgba(2,20,10,.08);
      margin-bottom: 10px;
      color: var(--green-800);
      font-size: 20px;
    }
    .feature h3{ margin: 0 0 8px; font-size: 16.5px; letter-spacing: -.2px; }
    .feature p{ margin: 0; color: var(--muted); line-height: 1.65; font-size: 14px; }

    .contact-wrap{
      display:grid;
      grid-template-columns: .92fr 1.08fr;
      gap: 18px;
      margin-top: 18px;
    }
    .card{
      background: rgba(255,255,255,.90);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      box-shadow: var(--shadow-strong);
      overflow:hidden;
    }
    .card-inner{ padding: 18px; }

    .badge{
      display:inline-flex;
      gap:10px;
      align-items:center;
      padding: 10px 12px;
      border-radius: 999px;
      background: rgba(14,164,79,.10);
      border: 1px solid rgba(11,122,59,.18);
      font-weight:900;
      margin-bottom: 12px;
      color: #0b1220;
    }

    .contact-item{
      display:flex;
      gap:12px;
      align-items:flex-start;
      padding:14px 0;
      border-top: 1px solid rgba(0,0,0,.06);
    }
    .contact-item:first-of-type{ border-top:0; }
    .icon{
      width:42px; height:42px;
      border-radius: 14px;
      display:grid; place-items:center;
      background: rgba(14,164,79,.12);
      border:1px solid rgba(11,122,59,.18);
      flex: 0 0 auto;
      box-shadow: 0 10px 16px rgba(2,20,10,.08);
      color: var(--green-800);
      font-size: 18px;
    }
    .contact-item b{ display:block; color:#0b1220; }
    .contact-item small{ display:block; color: var(--muted); line-height:1.45; margin-top: 2px; }

    .map{
      width:100%;
      min-height: 420px;
      border: 0;
      border-radius: var(--radius);
      box-shadow: var(--shadow-strong);
      background:#fff;
    }

    .footer{
      padding: 18px 0 26px;
      border-top: 1px solid rgba(0,0,0,.06);
      background: rgba(255,255,255,.88);
      color: var(--muted);
      font-size: 13px;
    }
    .footer-row{
      display:flex; align-items:center; justify-content:space-between;
      gap: 10px; flex-wrap: wrap;
    }
    .footer a{ color: var(--muted); font-weight: 800; }
    .footer a:hover{ color: var(--text); }

    #kontak{ scroll-margin-top: 94px; }
    #beranda{ scroll-margin-top: 94px; }

    @media (prefers-reduced-motion: reduce){
      *{ transition:none !important; scroll-behavior:auto !important; }
      .feature:hover{ transform:none !important; }
    }

    @media (max-width: 980px){
      .hero{ min-height:auto; padding: 62px 0 28px; }
      .hero-grid{ grid-template-columns: 1fr; }
      .illus{ justify-content:center; margin-top: 10px; }
      .hero-img{ max-width: 460px; }
      .mini-stats{ grid-template-columns: 1fr; }
      .features{ grid-template-columns: 1fr; }
      .contact-wrap{ grid-template-columns: 1fr; }
      .nav-links{ display:none; }
      .menu-btn{ display:inline-flex; }
      .brand{ min-width: auto; }
    }

    @media (max-width: 480px){
      .nav-cta .btn{ display:none; }
    }
  </style>
</head>

<body>

  <header class="nav">
    <div class="container nav-inner">
      <a class="brand" href="{{ url('/') }}">
        <img class="brand-logo" src="{{ asset('img/logoku.png') }}" alt="Logo DLH Parepare">
        <div>
          Parepare
          <small>Sistem Monitoring Tempat Sampah IoT</small>
        </div>
      </a>

      <nav class="nav-links" aria-label="Navigasi">
        <a href="#beranda">Beranda</a>
        <a href="#fitur">Fitur</a>
        <a href="#kontak">Kontak</a>
      </nav>

      <div class="nav-cta">
        <a class="btn" href="{{ route('login.staff') }}">
          <i class="bi bi-shield-lock"></i>
          Login Petugas & Admin
        </a>

        <a class="btn primary" href="{{ url('/auth/login') }}">
          <i class="bi bi-person-circle"></i>
          Login Warga
        </a>

        <button class="menu-btn" id="menuBtn" type="button" aria-expanded="false" aria-controls="drawer" aria-label="Buka menu">
          <i class="bi bi-list" style="font-size:22px;"></i>
        </button>
      </div>
    </div>
  </header>

  <div class="drawer-backdrop" id="backdrop" tabindex="-1" aria-hidden="true"></div>
  <aside class="drawer" id="drawer" role="dialog" aria-modal="true" aria-label="Menu">
    <div class="drawer-inner">
      <a class="link" href="#beranda">
        Beranda <i class="bi bi-chevron-right"></i>
      </a>
      <a class="link" href="#fitur">
        Fitur <i class="bi bi-chevron-right"></i>
      </a>
      <a class="link" href="#kontak">
        Kontak <i class="bi bi-chevron-right"></i>
      </a>

      <div class="divider"></div>

      <div class="drawer-cta">
        <a class="btn" href="{{ route('login.staff') }}">
          <i class="bi bi-shield-lock"></i>
          Login Petugas & Admin
        </a>
        <a class="btn primary" href="{{ url('/auth/login') }}">
          <i class="bi bi-person-circle"></i>
          Login Warga
        </a>
      </div>
    </div>
  </aside>

  <main id="beranda" class="hero">
    <div class="container hero-grid">
      <section>
        <div class="kicker">
          <i class="bi bi-geo-alt-fill"></i>
          Dinas Lingkungan Hidup Kota Parepare
        </div>

        <h1>Monitoring Tempat Sampah & Rute Mobil Sampah Secara Real-Time</h1>
        <p>
          Portal DLH Kota Parepare untuk memantau status tempat sampah berbasis IoT, membantu petugas dalam operasional,
          serta memudahkan warga mengakses informasi layanan kebersihan.
        </p>

        <div class="hero-actions">
          <a class="btn primary" href="{{ url('/auth/login') }}">
            <i class="bi bi-box-arrow-in-right"></i>
            Masuk sebagai Warga
          </a>
          <a class="btn" href="{{ route('login.staff') }}">
            <i class="bi bi-shield-check"></i>
            Masuk Petugas/Admin
          </a>
          <a class="btn" href="#kontak">
            <i class="bi bi-telephone"></i>
            Hubungi DLH
          </a>
        </div>
      </section>

      <aside class="illus" aria-label="Ilustrasi">
        <img
          class="hero-img"
          src="{{ asset('img/hero-home.png') }}"
          alt="Ilustrasi Monitoring Tempat Sampah"
        >
      </aside>
    </div>
  </main>

  <section id="fitur" class="section">
    <div class="container">
      <div class="section-head">
        <div>
          <h2 class="section-title">Fitur Utama</h2>
          <p class="section-sub">
            Fokus pada monitoring
          </p>
        </div>
      </div>

      <div class="features">
        <article class="feature">
          <div class="f-ico"><i class="bi bi-broadcast"></i></div>
          <h3>Status Tempat Sampah & Notifikasi Dashboard</h3>
          <p>Pantau indikator kapasitas/ kondisi secara berkala untuk menentukan prioritas pengangkutan bagi petugas</p>
        </article>

        <article class="feature">
          <div class="f-ico"><i class="bi bi-truck"></i></div>
          <h3>Monitoring Rute Mobil</h3>
          <p>Visualisasi pergerakan armada dan dukung perencanaan rute yang lebih efektif.</p>
        </article>

        <article class="feature">
          <div class="f-ico"><i class="bi bi-journal-text"></i></div>
          <h3>Edukasi Sampah</h3>
          <p>Materi edukasi untuk warga terkait pemilahan, pengelolaan, dan kebiasaan ramah lingkungan.</p>
        </article>
      </div>
    </div>
  </section>

  <section id="kontak" class="section">
    <div class="container">
      <div class="section-head">
        <div>
          <h2 class="section-title">Kontak</h2>
        </div>
      </div>

      <div class="contact-wrap">
        <div class="card">
          <div class="card-inner">
            <div class="badge">
              <i class="bi bi-info-circle"></i>
              Layanan DLH Parepare
            </div>

            <div class="contact-item">
              <div class="icon"><i class="bi bi-envelope"></i></div>
              <div>
                <b>Email</b>
                <small>
                  <a
                    href="mailto:dlhpare@gmail.com?subject=Permintaan%20Informasi%20DLH%20Parepare&body=Halo%20DLH%20Parepare,%0A%0ASaya%20ingin%20bertanya%20tentang%20...%0A%0ANama:%20%0ANo.%20HP:%20%0ALokasi:%20%0A%0ATerima%20kasih."
                  >
                    dlhpare@gmail.com
                  </a>
                </small>
              </div>
            </div>

            <div class="contact-item">
              <div class="icon"><i class="bi bi-telephone"></i></div>
              <div>
                <b>Telepon / WhatsApp</b>
                <small>
                  <a href="https://wa.me/6281239100912" target="_blank" rel="noopener">
                    0812-3910-0912
                  </a>
                </small>
              </div>
            </div>

            <div class="contact-item">
              <div class="icon"><i class="bi bi-building"></i></div>
              <div>
                <b>Kantor</b>
                <small>Jl. Jenderal Ahmad Yani, Km. 6, Lapadde, Bukit Harapan, Kec. Soreang, Kota Parepare, Sulawesi Selatan 91112</small>
              </div>
            </div>

            <div class="contact-item">
              <div class="icon"><i class="bi bi-bell"></i></div>
              <div>
                <b>Catatan</b>
                <small>Jika Anda Petugas dan tidak bisa login, silakan hubungi Admin DLH untuk aktivasi akun.</small>
              </div>
            </div>
          </div>
        </div>

        <iframe
          class="map"
          title="Peta DLH Parepare"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          src="https://www.google.com/maps?q=Dinas%20Lingkungan%20Hidup%20Kota%20Parepare&output=embed">
        </iframe>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <div class="footer-row">
        <div>© <span id="y"></span> DLH Kota Parepare — Monitoring Tempat Sampah</div>
        <div>
          <a href="#beranda">Beranda</a> ·
          <a href="#fitur">Fitur</a> ·
          <a href="#kontak">Kontak</a>
        </div>
      </div>
    </div>
  </footer>

  <script>

    document.getElementById('y').textContent = new Date().getFullYear();

    const body = document.body;
    const btn = document.getElementById('menuBtn');
    const backdrop = document.getElementById('backdrop');
    const drawer = document.getElementById('drawer');

    function closeMenu(){
      body.classList.remove('menu-open');
      btn.setAttribute('aria-expanded', 'false');
    }
    function openMenu(){
      body.classList.add('menu-open');
      btn.setAttribute('aria-expanded', 'true');
    }

    btn.addEventListener('click', () => {
      body.classList.contains('menu-open') ? closeMenu() : openMenu();
    });

    backdrop.addEventListener('click', closeMenu);

    drawer.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', closeMenu);
    });

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMenu();
    });
  </script>
</body>
</html>
