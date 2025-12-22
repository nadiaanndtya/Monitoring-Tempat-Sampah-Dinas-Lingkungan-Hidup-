import { initializeApp } from "https://www.gstatic.com/firebasejs/11.1.0/firebase-app.js";
import { getDatabase, ref, onValue, set, get, push, update} from "https://www.gstatic.com/firebasejs/11.1.0/firebase-database.js";

let lastVolumeBeforeZero = {}; 

const firebaseConfig = {
  apiKey: "AIzaSyDnXwpAU-Gu9V4qgCXxkRc2xbVGr_YTt4Q",
  authDomain: "monitoring-project-c6b65.firebaseapp.com",
  databaseURL: "https://monitoring-project-c6b65-default-rtdb.firebaseio.com",
  projectId: "monitoring-project-c6b65",
  storageBucket: "monitoring-project-c6b65.firebasestorage.app",
  messagingSenderId: "429720588066",
  appId: "1:429720588066:web:629862a23c8e93663d5f7b",
  measurementId: "G-CBJX50QQRY"
};

const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

function nowWitaISOString() {
  const d = new Date();

  const parts = new Intl.DateTimeFormat("en-GB", {
    timeZone: "Asia/Makassar",
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  }).formatToParts(d);

  const p = Object.fromEntries(parts.map(x => [x.type, x.value]));

  return `${p.year}-${p.month}-${p.day}T${p.hour}:${p.minute}:${p.second}+08:00`;
}

const map = L.map("map").setView([-4.0095, 119.6291], 14);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "&copy; OpenStreetMap contributors"
}).addTo(map);


const DLH_COORDS = [-3.9884196361122606, 119.6521610943085]; 
let tpsDataCache = {};                  
const routeLayer = L.layerGroup().addTo(map); 
const routeOrderLayer = L.layerGroup().addTo(map); 
let visitOrderIndex = {}; 
let routeRequestId = 0;

function clearRouteAnnotations() {
  routeOrderLayer.clearLayers();
  visitOrderIndex = {};

  Object.values(tpsMarkers).forEach(m => {
    if (!m) return;

    if (m.__basePopup) m.setPopupContent(m.__basePopup);
    if (m.__baseTooltip && m.setTooltipContent) m.setTooltipContent(m.__baseTooltip);
  });
}

const dlhMarker = L.marker(DLH_COORDS).addTo(map)
  .bindPopup("<b>Dinas Lingkungan Hidup Parepare</b><br>Titik Awal Mobil");

dlhMarker.bindTooltip(
  `<div style="min-width:160px">
     <div style="font-weight:700">DLH Parepare</div>
     <div>Titik Awal Mobil</div>
   </div>`,
  {
    direction: "top",
    sticky: true,
    opacity: 0.95,
    offset: [0, -10],
    className: "tps-tooltip"
  }
);

let tpsMarkers = {};
let mobilMarkers = {};

function showNotifications(tpsList) {
  const notifContainer = document.getElementById("notification");
  if (!notifContainer) return;

  if (tpsList.length === 0) {
    notifContainer.classList.add("d-none");
    notifContainer.innerHTML = "";
    return;
  }

  const messages = tpsList.map(tps =>
    `<li><strong>TPS ${tps.id}</strong> di ${tps.lokasi} sudah penuh ${tps.volume}% Segera Lakukan Pengangkutan!</li>`
  ).join("");

  notifContainer.innerHTML = `
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
      <h5 class="mb-2">⚠ Tempat Sampah Penuh!</h5>
      <ul class="mb-0">${messages}</ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;

  notifContainer.classList.remove("d-none");
}

const currentUserId = document.querySelector('meta[name="firebase-uid"]')?.content || '';

const ARM_THRESHOLD = 40;   
const EMPTY_THRESHOLD = 10; 

onValue(ref(db, "tempat_sampah"), async (snapshot) => {
  console.log('tps snapshot trigger');
  const data = snapshot.val();
  console.log('data tps:', data);

  Object.values(tpsMarkers).forEach(marker => map.removeLayer(marker));
  tpsMarkers = {};

  if (!data) return;

  let notifToShow = [];

  const laporanSnap = await get(ref(db, "laporan_pengambilan"));
  const laporanData = laporanSnap.exists() ? laporanSnap.val() : {};

  for (const [id, tps] of Object.entries(data)) {
    const lat = parseFloat(tps.koordinat?.latitude ?? NaN);
    const lng = parseFloat(tps.koordinat?.longitude ?? NaN);

    const rawVolume = tps.volume;
    const volume = rawVolume !== undefined && rawVolume !== null
      ? parseInt(rawVolume.toString().replace("%", "").trim())
      : 0;

    if (!isNaN(lat) && !isNaN(lng)) {

      if (volume > 10 ) lastVolumeBeforeZero[id] = volume;

      let color = "green";
      if (volume >= 80) color = "red";
      else if (volume >= 40) color = "orange";

      const laporanTPS = laporanData[id] || {};
      let armed = laporanTPS?.armed === true;
      let statusLaporan = laporanTPS?.status || "";

      if (volume >= ARM_THRESHOLD && (!armed || statusLaporan === "sudah_dilapor")) {
        armed = true;
        statusLaporan = "armed";
        await update(ref(db, `laporan_pengambilan/${id}`), {
          armed: true,
          status: "armed",
          last_armed: nowWitaISOString()
        });
      }

      const canReport = (volume <= EMPTY_THRESHOLD) && armed && (statusLaporan !== "sudah_dilapor");

      let popupContent = `
        <b>Tempat Sampah ${id}</b><br>
        Lokasi: ${tps.lokasi ?? '-'}<br>
        Volume: ${volume}%
      `;

      if (canReport) {
        popupContent += `
          <br><button id="lapor-${id}" class="btn btn-success btn-sm mt-2">
            ✅ Lapor Sudah Diambil
          </button>`;
      } else if (statusLaporan === "sudah_dilapor") {
        popupContent += `<br><span class="badge bg-secondary mt-2">Sudah Dilaporkan (menunggu TPS penuh lagi)</span>`;
      } else if (volume <= EMPTY_THRESHOLD && !armed) {
        popupContent += `<br><span class="badge bg-warning text-dark mt-2">Belum bisa lapor (TPS belum pernah ≥ ${ARM_THRESHOLD}%)</span>`;
      }

      const tooltipContent = `
        <div style="min-width:160px">
          <div style="font-weight:700">TPS ${id}</div>
          <div>${tps.lokasi ?? '-'}</div>
          <div>Volume: <b>${volume}%</b></div>
        </div>
      `;

      const marker = L.circleMarker([lat, lng], { color, radius: 8 })
        .addTo(map)
        .bindPopup(popupContent);

      marker.bindTooltip(tooltipContent, {
        direction: "top",
        sticky: true,
        opacity: 0.95,
        offset: [0, -8],
        className: "tps-tooltip"
      });

      marker.__basePopup = popupContent;
      marker.__baseTooltip = tooltipContent;

      tpsMarkers[id] = marker;

      (err) => {
      console.error("[TPS] read error:", err);
    }

      if (volume >= 80) {
        notifToShow.push({ id, lokasi: tps.lokasi, volume });
      }

      marker.on("popupopen", () => {
        const btn = document.getElementById(`lapor-${id}`);
        if (btn) {
          btn.addEventListener("click", async () => {
            btn.disabled = true;
            btn.innerText = "Mengirim...";

            const waktuSekarang = nowWitaISOString();
            const laporanUtamaRef = ref(db, `laporan_pengambilan/${id}`);

            await set(laporanUtamaRef, {
              petugas: currentUserId || "anon",
              nama_petugas: document.getElementById("petugasNama")?.textContent || "-",
              waktu: waktuSekarang,
              volume_sebelum: lastVolumeBeforeZero[id] ?? rawVolume,
              lokasi_tps: tps.lokasi ?? "-",
              status: "sudah_dilapor"
            });

            const laporanRef = ref(db, `laporan_pengambilan_riwayat/${id}`);
            const laporanSnap = await get(laporanRef);
            const laporanData = laporanSnap.exists() ? laporanSnap.val() : {};
            const nextKey = Object.keys(laporanData).length + 1;

            await set(ref(db, `laporan_pengambilan_riwayat/${id}/${nextKey}`), {
              petugas: currentUserId || "anon",
              nama_petugas: document.getElementById("petugasNama")?.textContent || "-",
              waktu: waktuSekarang,
              volume_sebelum: lastVolumeBeforeZero[id] ?? rawVolume,
              lokasi_tps: tps.lokasi ?? "-",
              status: "sudah_dilapor"
            });

            marker.setPopupContent(`
              <b>Tempat Sampah ${id}</b><br>
              Lokasi: ${tps.lokasi ?? '-'}<br>
              Volume: ${volume}%<br>
              <span class="badge bg-secondary mt-2">Sudah Dilaporkan</span>
            `);
          });
        }
      });
    }
  }

  tpsDataCache = {};
  Object.entries(data || {}).forEach(([id, tps]) => {
    const lat = parseFloat(tps.koordinat?.latitude);
    const lng = parseFloat(tps.koordinat?.longitude);
    if (!isNaN(lat) && !isNaN(lng)) {
      const rawVolume = tps.volume;
      const percentage = rawVolume !== undefined && rawVolume !== null
        ? parseInt(rawVolume.toString().replace("%", "").trim(), 10)
        : 0;
      tpsDataCache[id] = {
        id,
        name: tps.lokasi ?? `TPS ${id}`,
        coords: [lat, lng],
        percentage
      };
    }
  });

  showNotifications(notifToShow);
});

const garbageTruckSvg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <defs>
    <!-- shadow -->
    <filter id="shadow" x="-30%" y="-30%" width="160%" height="160%">
      <feDropShadow dx="0" dy="2.5" stdDeviation="2.2" flood-opacity=".35"/>
    </filter>

    <!-- gradients -->
    <linearGradient id="gBody" x1="18" y1="18" x2="48" y2="46" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#34d399"/>
      <stop offset="1" stop-color="#16a34a"/>
    </linearGradient>

    <linearGradient id="gCab" x1="8" y1="22" x2="28" y2="42" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#fbbf24"/>
      <stop offset="1" stop-color="#f59e0b"/>
    </linearGradient>

    <linearGradient id="gGlass" x1="12" y1="26" x2="24" y2="36" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#e0f2fe" stop-opacity="1"/>
      <stop offset="1" stop-color="#93c5fd" stop-opacity=".95"/>
    </linearGradient>

    <radialGradient id="gWheel" cx="50%" cy="40%" r="70%">
      <stop offset="0" stop-color="#6b7280"/>
      <stop offset=".55" stop-color="#111827"/>
      <stop offset="1" stop-color="#0b1220"/>
    </radialGradient>

    <linearGradient id="gMetal" x1="33" y1="23" x2="45" y2="35" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#ffffff"/>
      <stop offset="1" stop-color="#e5e7eb"/>
    </linearGradient>
  </defs>

  <g filter="url(#shadow)">
    <!-- subtle outline to pop over map -->
    <g stroke="#0f172a" stroke-opacity=".15" stroke-width="1">
      <!-- container body -->
      <path d="M18 20.5
               h27.5
               a4.5 4.5 0 0 1 4.5 4.5
               v9.5
               a3.5 3.5 0 0 1-3.5 3.5
               H18
               a4.5 4.5 0 0 1-4.5-4.5
               V25
               a4.5 4.5 0 0 1 4.5-4.5z"
            fill="url(#gBody)"/>

      <!-- lower body belt -->
      <path d="M18 34.5h30a3 3 0 0 1 3 3v2A3.5 3.5 0 0 1 47.5 43H18a4 4 0 0 1-4-4v-1.5a3 3 0 0 1 3-3z"
            fill="#15803d" stroke="none"/>

      <!-- highlight strip -->
      <path d="M19.5 23.2h25.5c1.2 0 2.2 1 2.2 2.2v1.2c0 .9-.7 1.6-1.6 1.6H19.2c-1 0-1.7-.8-1.7-1.7v-1c0-1.3 1-2.3 2-2.3z"
            fill="#ffffff" opacity=".18" stroke="none"/>

      <!-- cab -->
      <path d="M10 38V26.4c0-2.2 1.8-4 4-4H24c1.9 0 3.4 1 4.2 2.6l2.1 4.3c.2.5.4 1 .4 1.6V38H10z"
            fill="url(#gCab)"/>

      <!-- windshield -->
      <path d="M13 28.3h9.2c1.1 0 2 .9 2 2V34H13v-5.7z"
            fill="url(#gGlass)" stroke="none"/>

      <!-- small headlight -->
      <path d="M10.8 36.2h3.6c.6 0 1 .4 1 1v1.2c0 .6-.4 1-1 1h-2.4c-.7 0-1.2-.2-1.7-.7l-.5-.5v-1z"
            fill="#fde68a" stroke="none" opacity=".95"/>

      <!-- recycle badge -->
      <rect x="33.2" y="23.2" width="10.2" height="10.8" rx="2.2" fill="url(#gMetal)"/>
      <path d="M36 26.2h4.8M37.2 26.2v6.2M39.6 26.2v6.2"
            stroke="#111827" stroke-width="1.4" stroke-linecap="round"/>

      <!-- wheels -->
      <g stroke="none">
        <circle cx="20" cy="44" r="5.4" fill="url(#gWheel)"/>
        <circle cx="20" cy="44" r="2.3" fill="#d1d5db"/>
        <circle cx="20" cy="44" r="1.1" fill="#9ca3af"/>

        <circle cx="42" cy="44" r="5.4" fill="url(#gWheel)"/>
        <circle cx="42" cy="44" r="2.3" fill="#d1d5db"/>
        <circle cx="42" cy="44" r="1.1" fill="#9ca3af"/>
      </g>

      <!-- tiny road shadow -->
      <ellipse cx="31" cy="49.2" rx="20" ry="2.2" fill="#0b1220" opacity=".10" stroke="none"/>
    </g>
  </g>
</svg>
`.trim();

const garbageTruckIcon = L.icon({
  iconUrl: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(garbageTruckSvg)}`,
  iconSize: [53, 53],
  iconAnchor: [24, 48],
  popupAnchor: [0, -44],
});

function spreadLatLng(lat, lng, index, total, radiusMeters = 7) {
  if (total <= 1) return [lat, lng];

  const angle = (2 * Math.PI * index) / total;

  const dLat = (radiusMeters * Math.cos(angle)) / 111320;
  const dLng =
    (radiusMeters * Math.sin(angle)) /
    (111320 * Math.cos((lat * Math.PI) / 180));

  return [lat + dLat, lng + dLng];
}

onValue(ref(db, "mobil"), (snapshot) => {
  const data = snapshot.val();

  Object.values(mobilMarkers).forEach(marker => map.removeLayer(marker));
  mobilMarkers = {};

  if (!data) return;

  const list = [];
  Object.entries(data).forEach(([id, mobil]) => {
    const lat = parseFloat(mobil.koordinat?.latitude);
    const lng = parseFloat(mobil.koordinat?.longitude);
    const plat = (mobil.plat ?? "-").toString().trim();

    if (!isNaN(lat) && !isNaN(lng)) {
      list.push({ id, plat, lat, lng });
    }
  });

  const groups = {};
  list.forEach((m) => {
    const key = `${m.lat.toFixed(6)},${m.lng.toFixed(6)}`;
    if (!groups[key]) groups[key] = [];
    groups[key].push(m);
  });

  Object.values(groups).forEach((group) => {
    const total = group.length;

    group.forEach((m, idx) => {

      const [dispLat, dispLng] = spreadLatLng(m.lat, m.lng, idx, total, 7);

      const marker = L.marker([dispLat, dispLng], {
        icon: garbageTruckIcon,
        riseOnHover: true
      })
        .addTo(map)
        .bindPopup(`
          <div style="min-width:180px">
            <div style="font-weight:700">Mobil Pengangkut Sampah</div>
            <div><b>Plat:</b> ${m.plat}</div>
            <div style="opacity:.8;font-size:12px;margin-top:4px">
              Koordinat: ${m.lat.toFixed(6)}, ${m.lng.toFixed(6)}
            </div>
          </div>
        `);

      marker.bindTooltip(
        `<div style="min-width:180px">
          <div style="font-weight:700">Mobil Pengangkut Sampah</div>
          <div><b>Plat:</b> ${m.plat}</div>
        </div>`,
        {
          direction: "top",
          sticky: true,
          opacity: 0.95,
          offset: [0, -10],
          className: "tps-tooltip",
        }
      );

      mobilMarkers[m.id] = marker;
    });
  });
});

const btnProfil = document.getElementById("btnProfil");
const profilOverlay = document.getElementById("profilOverlay");
const closeProfil = document.getElementById("closeProfil");
const overlayBackdrop = document.getElementById("overlayBackdrop");

function openOverlay() {
    overlayBackdrop.style.display = "block";
    profilOverlay.style.display = "block";

    setTimeout(() => {
        overlayBackdrop.style.opacity = "1";
        profilOverlay.style.opacity = "1";
        profilOverlay.style.transform = "translate(-50%, -50%) scale(1)";
    }, 10);
}

function closeOverlay() {
    overlayBackdrop.style.opacity = "0";
    profilOverlay.style.opacity = "0";
    profilOverlay.style.transform = "translate(-50%, -50%) scale(0.8)";
    
    setTimeout(() => {
        overlayBackdrop.style.display = "none";
        profilOverlay.style.display = "none";
    }, 300);
}

btnProfil.addEventListener("click", openOverlay);
closeProfil.addEventListener("click", closeOverlay);
overlayBackdrop.addEventListener("click", closeOverlay); // klik di backdrop juga menutup

onValue(ref(db, "users"), (snapshot) => {
  const users = snapshot.val();
  if (!users) return;


  let petugasData = users[currentUserId];
  if (!petugasData) {
    petugasData = Object.values(users).find(u => u.role === "petugas");
  }

  if (petugasData) {
    document.getElementById("petugasNama").textContent = petugasData.nama ?? "Petugas";
    document.getElementById("petugasEmail").textContent = petugasData.email ?? "-";
    document.getElementById("petugasRole").textContent = petugasData.role ?? "Petugas";
    document.getElementById("petugasStatus").textContent = petugasData.alamat ?? "-";
  }
});

function haversineDistance([lat1, lon1], [lat2, lon2]) {
  const R = 6371e3, toRad = d => d * Math.PI / 180;
  const dLat = toRad(lat2 - lat1), dLon = toRad(lon2 - lon1);
  const a = Math.sin(dLat/2)**2 + Math.cos(toRad(lat1))*Math.cos(toRad(lat2))*Math.sin(dLon/2)**2;
  return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function nearestNeighborRoute(locations, startIndex = 0) {
  const n = locations.length, visited = Array(n).fill(false), route = [startIndex];
  visited[startIndex] = true;
  let cur = startIndex;
  for (let i = 1; i < n; i++) {
    let best = -1, bestD = Infinity;
    for (let j = 0; j < n; j++) if (!visited[j]) {
      const d = haversineDistance(locations[cur].coords, locations[j].coords);
      if (d < bestD) { bestD = d; best = j; }
    }
    if (best !== -1) { visited[best] = true; route.push(best); cur = best; }
  }
  return route;
}

async function getRouteData(coords) {
  if (!coords || coords.length < 2) return { distance: Infinity, geometry: null };
  const coordString = coords.map(c => `${c[1]},${c[0]}`).join(';');
  const url = `https://router.project-osrm.org/route/v1/driving/${coordString}?overview=full&steps=false&geometries=geojson`;
  try {
    const res = await fetch(url);
    if (!res.ok) return { distance: Infinity, geometry: null };
    const data = await res.json();
    if (data.code === 'Ok' && data.routes?.length) {
      return { distance: data.routes[0].distance, geometry: data.routes[0].geometry };
    }
  } catch(e){ /* noop */ }
  return { distance: Infinity, geometry: null };
}

// ======================
// Tampilkan Rute Teroptimasi (DLH -> semua TPS penuh -> DLH)
// ======================
async function displayOptimizedRoute() {

  routeLayer.clearLayers();
  clearRouteAnnotations();

  const fullTPS = Object.values(tpsDataCache).filter(t => (t.percentage ?? 0) >= 80);

  if (fullTPS.length === 0) {
    L.popup().setLatLng(DLH_COORDS).setContent('Tidak ada TPS penuh (≥80%) untuk dirutekan.').openOn(map);
    return;
  }

  const activeLocations = [
    { id:'dlh', name:'DLH KOTA PAREPARE', coords: DLH_COORDS, percentage: null },
    ...fullTPS
  ];

  const orderIdx = nearestNeighborRoute(activeLocations, 0);

  const goCoords = orderIdx.map(i => activeLocations[i].coords);

  const goData = await getRouteData(goCoords);
  if (goData.geometry) {
    L.geoJSON(goData.geometry, { style: { color: 'green', weight: 5 } }).addTo(routeLayer);
  }

  const last = goCoords[goCoords.length - 1];
  const backData = await getRouteData([last, DLH_COORDS]);
  if (backData.geometry) {
    L.geoJSON(backData.geometry, { style: { color: 'blue', weight: 5, dashArray: '10,10' } }).addTo(routeLayer);
  }

  const visitOrder = orderIdx
    .filter(i => activeLocations[i].id !== 'dlh')
    .map((i, idx) => ({
      seq: idx + 1,
      id: activeLocations[i].id,
      name: activeLocations[i].name,
      coords: activeLocations[i].coords
    }));

  visitOrder.forEach(({ id, seq, coords }) => {
    visitOrderIndex[id] = seq;

    const badge = L.divIcon({
      className: '',
      html: `
        <div style="
          width:26px;height:26px;border-radius:50%;
          background:#14B8A6;color:#fff;font-weight:700;
          display:flex;align-items:center;justify-content:center;
          border:2px solid #fff;box-shadow:0 0 6px rgba(0,0,0,.35);
          font-size:13px;line-height:1;
        ">${seq}</div>
      `,
      iconSize: [26, 26],
      iconAnchor: [13, 13]
    });

    L.marker(coords, { icon: badge, interactive: false }).addTo(routeOrderLayer);

    const m = tpsMarkers[id];
    if (m) {
      const base = m.__basePopup || m.getPopup()?.getContent() || '';
      m.setPopupContent(`${base}<br><b>Urutan kunjungan:</b> #${seq}`);
    
    const tbase = m.__baseTooltip || '';
    if (m.setTooltipContent) {
      m.setTooltipContent(`${tbase}<div>Urutan: <b>#${seq}</b></div>`);
    }
  }
  });

  const bounds = routeLayer.getBounds?.();
  if (bounds && bounds.isValid()) map.fitBounds(bounds, { padding: [50,50] });

  const cancelBtn = document.getElementById('btnCancelRute');
  if (cancelBtn) {
    cancelBtn.classList.remove('d-none');
  }
}

function clearOptimizedRoute() {

  routeLayer.clearLayers();

  clearRouteAnnotations();

  const cancelBtn = document.getElementById('btnCancelRute');
  if (cancelBtn) {
    cancelBtn.classList.add('d-none');
  }
}

window.displayOptimizedRoute = displayOptimizedRoute;
window.clearOptimizedRoute = clearOptimizedRoute;

document.getElementById('btnRute')
  ?.addEventListener('click', displayOptimizedRoute);

document.getElementById('btnCancelRute')
  ?.addEventListener('click', clearOptimizedRoute);