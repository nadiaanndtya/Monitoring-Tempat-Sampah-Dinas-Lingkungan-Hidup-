<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
            <li><a href="/admin/dashboard" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="/admin/laporan"><i class="fas fa-history"></i> Laporan Historis </a></li>
            <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header class="main-header">
            <h1>Dashboard Admin</h1>
            <p>Manajemen Data Pengguna, Tempat Sampah, dan GPS Mobil</p>
        </header>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <div class="table-container">
            <div class="section-header">
                <h2>Data Petugas</h2>
                <button class="btn-add" onclick="openAddModal('petugas')">
                    <i class="fas fa-user-plus"></i> Tambah Petugas
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($petugas as $p)
                        <tr>
                            <td>{{ $p['nama'] ?? '-' }}</td>
                            <td>{{ $p['email'] ?? '-' }}</td>
                            <td>{{ $p['telepon'] ?? '-' }}</td>
                            <td>{{ $p['alamat'] ?? '-' }}</td>
                            <td class="actions">
                            <button type="button"
                            onclick='editUser(
                                "{{ $p["id"] }}",
                                @json($p["nama"] ?? ""),
                                @json($p["email"] ?? ""),
                                @json($p["telepon"] ?? ""),
                                @json($p["alamat"] ?? "")
                            )'
                            class="btn-action edit">
                            <i class="fas fa-edit"></i>
                            </button>
                                <form action="{{ route('admin.user.delete', $p['id']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;">Tidak ada data petugas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <div class="section-header">
                <h2>Data Warga</h2>
                <button class="btn-add" onclick="openAddModal('warga')">
                    <i class="fas fa-user-plus"></i> Tambah Warga
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warga as $w)
                        <tr>
                            <td>{{ $w['nama'] ?? '-' }}</td>
                            <td>{{ $w['email'] ?? '-' }}</td>
                            <td>{{ $w['telepon'] ?? '-' }}</td>
                            <td>{{ $w['alamat'] ?? '-' }}</td>
                            <td class="actions">
                            <button type="button"
                                onclick='editUser(
                                    "{{ $w["id"] }}",
                                    @json($w["nama"] ?? ""),
                                    @json($w["email"] ?? ""),
                                    @json($w["telepon"] ?? ""),
                                    @json($w["alamat"] ?? "")
                                )'
                                class="btn-action edit">
                                <i class="fas fa-edit"></i>
                            </button>
                                <form action="{{ route('admin.user.delete', $w['id']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;">Tidak ada data warga</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <div class="section-header">
                <h2>Data Tempat Sampah</h2>
                <button class="btn-add" onclick="openAddModal('tps')">
                    <i class="fas fa-plus"></i> Tambah TPS
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Lokasi</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Status</th>
                        <th>Volume</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tps as $t)
                        <tr>
                            <td>{{ $t['lokasi'] ?? '-' }}</td>
                            <td>{{ $t['koordinat']['latitude'] ?? '-' }}</td>
                            <td>{{ $t['koordinat']['longitude'] ?? '-' }}</td>
                            <td>{{ $t['status'] ?? '-' }}</td>
                            <td>{{ $t['volume'] ?? '-' }}</td>
                            <td class="actions">
                                <button type="button"
                                    onclick='editTps(
                                        @json($t["id"]),
                                        @json($t["lokasi"] ?? ""),
                                        @json($t["koordinat"]["latitude"] ?? ""),
                                        @json($t["koordinat"]["longitude"] ?? "")
                                    )'
                                    class="btn-action edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="/admin/tps/delete/{{ $t['id'] }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" onclick="return confirm('Yakin ingin menghapus TPS ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;">Tidak ada data TPS</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <div class="section-header">
                <h2>Data GPS Mobil</h2>
                <button class="btn-add" onclick="openAddModal('mobil')">
                    <i class="fas fa-plus"></i> Tambah Mobil
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Plat</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mobil as $m)
                        <tr>
                            <td>{{ $m['plat'] ?? '-' }}</td>
                            <td>{{ $m['koordinat']['latitude'] ?? '-' }}</td>
                            <td>{{ $m['koordinat']['longitude'] ?? '-' }}</td>
                            <td class="actions">
                                <button type="button"
                                    onclick='editMobil(
                                        @json($m["id"]),
                                        @json($m["plat"] ?? ""),
                                        @json($m["koordinat"]["latitude"] ?? ""),
                                        @json($m["koordinat"]["longitude"] ?? "")
                                    )'
                                    class="btn-action edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="/admin/mobil/delete/{{ $m['id'] }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" onclick="return confirm('Yakin ingin menghapus mobil ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;">Tidak ada data mobil</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Tambah Pengguna</h3>
        <form id="userForm" method="POST">
            @csrf
            <input type="text" name="nama" id="nama" placeholder="Nama" required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="text" name="telepon" id="telepon" placeholder="Telepon">
            <input type="text" name="alamat" id="alamat" placeholder="Alamat">
            <input type="password" name="password" id="password" placeholder="Password (Minimal 6 karakter)">
            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<div id="tpsModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="tpsModalTitle">Tambah TPS</h3>
        <form id="tpsForm" method="POST">
            @csrf
            <input type="text" name="lokasi" id="tps_lokasi" placeholder="Lokasi" required>
            <input type="text"
                name="latitude"
                id="tps_latitude"
                inputmode="decimal"
                placeholder="Latitude (contoh: -4.012345)"
                required>

            <input type="text"
                name="longitude"
                id="tps_longitude"
                inputmode="decimal"
                placeholder="Longitude (contoh: 119.623456)"
                required>

            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<div id="mobilModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="mobilModalTitle">Tambah Mobil</h3>
        <form id="mobilForm" method="POST">
            @csrf
            <input type="text"
                name="plat"
                id="mobil_plat"
                placeholder="Plat Mobil (contoh: B 1234 CD)"
                required
                maxlength="20"
                pattern="[A-Za-z0-9\s\-]{3,20}"
                title="Gunakan huruf/angka/spasi/strip. Contoh: B 1234 CD">

            <input type="hidden" name="latitude" id="mobil_latitude">
            <input type="hidden" name="longitude" id="mobil_longitude">

            <div style="margin-top:8px; font-size:12px; opacity:.8;">
                Koordinat awal mobil otomatis di titik DLH.
            </div>

            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('userModal');
    const tpsModal = document.getElementById('tpsModal');
    const mobilModal = document.getElementById('mobilModal');
    const form = document.getElementById('userForm');
    const tpsForm = document.getElementById('tpsForm');
    const mobilForm = document.getElementById('mobilForm');
    const title = document.getElementById('modalTitle');
    const passwordField = document.getElementById('password');
    const DEFAULT_MOBIL_LAT = "-3.9884196361122606";
    const DEFAULT_MOBIL_LON = "119.6521610943085";

    function removeMethodSpoofing(targetForm) {
        const existing = targetForm.querySelector('input[name="_method"]');
        if (existing) existing.remove();
    }

    function normalizePlat(raw) {
        let v = (raw || '').toUpperCase().trim();
        v = v.replace(/\s+/g, ' ');          
        v = v.replace(/[^A-Z0-9 \-]/g, '');   
        return v;
    }

    function validateDecimalCoord(inputEl, label, min, max) {
        const v = (inputEl.value || '').trim();

        if (!/^-?\d+\.\d+$/.test(v)) {
            inputEl.setCustomValidity(`${label} harus memakai titik (.) contoh: -4.012345`);
            return false;
        }

        const num = Number(v);
        if (Number.isNaN(num) || num < min || num > max) {
            inputEl.setCustomValidity(`${label} tidak valid. Rentang: ${min} sampai ${max}`);
            return false;
        }

        inputEl.setCustomValidity('');
        return true;
    }

    function attachLiveValidation(id, label, min, max) {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('input', () => validateDecimalCoord(el, label, min, max));
    }

    function openAddModal(type) {
        if (type === 'petugas' || type === 'warga') {
            modal.style.display = 'flex';

            title.innerText = (type === 'petugas') ? 'Tambah Petugas' : 'Tambah Warga';

            form.action = '/admin/user/store/' + type;
            form.method = 'POST';

            removeMethodSpoofing(form);
            form.reset();

            passwordField.required = true;
            passwordField.value = '';
            passwordField.placeholder = 'Password (Minimal 6 karakter)';
        } else if (type === 'tps') {
            tpsModal.style.display = 'flex';
            document.getElementById('tpsModalTitle').innerText = 'Tambah TPS';
            tpsForm.action = "/admin/tps/store";
            tpsForm.method = 'POST';

            removeMethodSpoofing(tpsForm);
            tpsForm.reset();
        } else if (type === 'mobil') {
            mobilModal.style.display = 'flex';
            document.getElementById('mobilModalTitle').innerText = 'Tambah Mobil';
            mobilForm.action = "/admin/mobil/store";
            mobilForm.method = 'POST';

            removeMethodSpoofing(mobilForm);
            mobilForm.reset();

            const latEl = document.getElementById('mobil_latitude');
            const lonEl = document.getElementById('mobil_longitude');
            if (latEl) latEl.value = DEFAULT_MOBIL_LAT;
            if (lonEl) lonEl.value = DEFAULT_MOBIL_LON;
        }
    }

    function editUser(id, nama, email, telepon, alamat) {
        modal.style.display = 'flex';
        title.innerText = 'Edit Pengguna';

        form.action = '/admin/user/update/' + id;
        form.method = 'POST';

        removeMethodSpoofing(form);

        let methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);

        document.getElementById('nama').value = nama || '';
        document.getElementById('email').value = email || '';
        document.getElementById('telepon').value = telepon || '';
        document.getElementById('alamat').value = alamat || '';

        passwordField.required = false;
        passwordField.value = '';
        passwordField.placeholder = 'Password (Kosongkan jika tidak diubah)';
    }

    function editTps(id, lokasi, latitude, longitude) {
    tpsModal.style.display = 'flex';
    document.getElementById('tpsModalTitle').innerText = 'Edit TPS';
    tpsForm.action = '/admin/tps/update/' + id;
    tpsForm.method = 'POST';

    removeMethodSpoofing(tpsForm);

    let methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    tpsForm.appendChild(methodInput);

    document.getElementById('tps_lokasi').value = lokasi;
    document.getElementById('tps_latitude').value = latitude;
    document.getElementById('tps_longitude').value = longitude;
    }

    function editMobil(id, plat, latitude, longitude) {
    mobilModal.style.display = 'flex';
    document.getElementById('mobilModalTitle').innerText = 'Edit Mobil';
    mobilForm.action = '/admin/mobil/update/' + id;
    mobilForm.method = 'POST';

    removeMethodSpoofing(mobilForm);

    let methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    mobilForm.appendChild(methodInput);

    document.getElementById('mobil_plat').value = normalizePlat(plat);

    const latEl = document.getElementById('mobil_latitude');
    const lonEl = document.getElementById('mobil_longitude');

    if (latEl) latEl.value = (latitude !== null && latitude !== undefined && latitude !== '') ? latitude : DEFAULT_MOBIL_LAT;
    if (lonEl) lonEl.value = (longitude !== null && longitude !== undefined && longitude !== '') ? longitude : DEFAULT_MOBIL_LON;
    }

    attachLiveValidation('tps_latitude', 'Latitude', -90, 90);
    attachLiveValidation('tps_longitude', 'Longitude', -180, 180);

    tpsForm.addEventListener('submit', function (e) {
    const latEl = document.getElementById('tps_latitude');
    const lonEl = document.getElementById('tps_longitude');

    const ok = validateDecimalCoord(latEl, 'Latitude', -90, 90) &&
                validateDecimalCoord(lonEl, 'Longitude', -180, 180);

    if (!ok) {
        e.preventDefault();
        (latEl.reportValidity(), lonEl.reportValidity());
    }
    });

    function closeModal() {
        modal.style.display = 'none';
        tpsModal.style.display = 'none';
        mobilModal.style.display = 'none';

        form.reset();
        tpsForm.reset();
        mobilForm.reset();

        removeMethodSpoofing(form);
        removeMethodSpoofing(tpsForm);
        removeMethodSpoofing(mobilForm);
    }

    const mobilPlatEl = document.getElementById('mobil_plat');
    if (mobilPlatEl) {
    mobilPlatEl.addEventListener('blur', () => {
        mobilPlatEl.value = normalizePlat(mobilPlatEl.value);
    });
    }

    window.onclick = function(event) {
        if (event.target === modal || event.target === tpsModal || event.target === mobilModal) {
            closeModal();
        }
    }

    function lockSubmit(formEl) {
        if (!formEl) return;

        formEl.addEventListener('submit', function (e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            if (submitBtn.dataset.submitting === '1') {
                e.preventDefault();
                return;
            }

            submitBtn.dataset.submitting = '1';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        });
    }

    lockSubmit(form);      
    lockSubmit(tpsForm);   
    lockSubmit(mobilForm); 

</script>
</body>
</html>