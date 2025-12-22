import { initializeApp } from "https://www.gstatic.com/firebasejs/11.1.0/firebase-app.js";
import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/11.1.0/firebase-database.js";

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

const btnProfil = document.getElementById("btnProfil");
const profilOverlay = document.getElementById("profilOverlay");
const closeProfil = document.getElementById("closeProfil");
const overlayBackdrop = document.getElementById("overlayBackdrop");

if (btnProfil && profilOverlay && closeProfil && overlayBackdrop) {
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
  overlayBackdrop.addEventListener("click", closeOverlay);
}

const currentUserId =
  document.querySelector('meta[name="firebase-uid"]')?.content || "";

onValue(ref(db, "users"), (snapshot) => {
  const users = snapshot.val();
  if (!users) return;

  let wargaData = users[currentUserId];
  if (!wargaData) {

    wargaData = Object.values(users).find((u) => u.role === "warga");
  }

  if (wargaData) {
    const namaEl = document.getElementById("petugasNama");
    const emailEl = document.getElementById("petugasEmail");
    const roleEl = document.getElementById("petugasRole");
    const alamatEl = document.getElementById("petugasStatus");

    if (namaEl) namaEl.textContent = wargaData.nama ?? "Warga";
    if (emailEl) emailEl.textContent = wargaData.email ?? "-";
    if (roleEl) roleEl.textContent = wargaData.role ?? "Warga";
    if (alamatEl) alamatEl.textContent = wargaData.alamat ?? "-";
  }
});

const mapContainer = document.getElementById("map");
let map;
let tpsMarkers = {};
let mobilMarkers = {};

if (mapContainer) {
  // === Inisialisasi Map ===
  map = L.map("map").setView([-4.0095, 119.6291], 14);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  const DLH_COORDS = [-3.9884196361122606, 119.6521610943085];
  const TPA_PAREPARE_COORDS = [-3.9766188359154477, 119.66376310938612];

  const dlhMarker = L.marker(DLH_COORDS)
    .addTo(map)
    .bindPopup("<b>Dinas Lingkungan Hidup Parepare</b>");

  dlhMarker.bindTooltip(
    `<div style="min-width:160px">
      <div style="font-weight:700">DLH Parepare</div>
      <div>Titik Awal Mobil</div>
    </div>`,
    { direction: "top", sticky: true, opacity: 0.95, offset: [0, -10], className: "tps-tooltip" }
  );

  const redTPAIcon = L.icon({
    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png",
    shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
  });

  const tpaMarker = L.marker(TPA_PAREPARE_COORDS, { icon: redTPAIcon })
    .addTo(map)
    .bindPopup("<b>TPA Kota Parepare</b><br>Titik Akhir Pembuangan Sampah");

  tpaMarker.bindTooltip(
    `<div style="min-width:160px">
      <div style="font-weight:700">TPA Kota Parepare</div>
      <div>Titik Akhir Pembuangan</div>
    </div>`,
    { direction: "top", sticky: true, opacity: 0.95, offset: [0, -10], className: "tps-tooltip" }
  );

  const tpsTrashPinSvg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 72">
  <defs>
    <filter id="ds" x="-30%" y="-30%" width="160%" height="160%">
      <feDropShadow dx="0" dy="3" stdDeviation="2.2" flood-opacity=".35"/>
    </filter>
    <linearGradient id="pinG" x1="14" y1="8" x2="50" y2="60" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#34d399"/>
      <stop offset="1" stop-color="#16a34a"/>
    </linearGradient>
    <linearGradient id="innerG" x1="18" y1="14" x2="46" y2="44" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#ffffff"/>
      <stop offset="1" stop-color="#f1f5f9"/>
    </linearGradient>
    <linearGradient id="binG" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#111827"/>
      <stop offset="1" stop-color="#0b1220"/>
    </linearGradient>
  </defs>
  <g filter="url(#ds)">
    <path d="M32 3
             C19.7 3 9.8 12.9 9.8 25.2
             c0 15.4 18.1 35.7 21 39.1
             c.6.7 1.8.7 2.4 0
             c2.9-3.4 21-23.7 21-39.1
             C54.2 12.9 44.3 3 32 3z"
          fill="url(#pinG)"/>
    <path d="M32 3
             C19.7 3 9.8 12.9 9.8 25.2
             c0 15.4 18.1 35.7 21 39.1
             c.6.7 1.8.7 2.4 0
             c2.9-3.4 21-23.7 21-39.1
             C54.2 12.9 44.3 3 32 3z"
          fill="none" stroke="#0f172a" stroke-opacity=".18" stroke-width="1"/>
    <circle cx="32" cy="26" r="15.3" fill="url(#innerG)" stroke="#0f172a" stroke-opacity=".12"/>
    <rect x="24" y="18" width="16" height="4.3" rx="2.2" fill="url(#binG)"/>
    <rect x="28.3" y="15.7" width="7.4" height="3.2" rx="1.6" fill="#111827"/>
    <path d="M25.6 22.8h12.8l-1.2 15.6c-.1 1.1-1 2-2.2 2H29c-1.2 0-2.1-.9-2.2-2l-1.2-15.6z"
          fill="url(#binG)"/>
    <rect x="29.2" y="26" width="1.8" height="12.2" rx="0.9" fill="#e5e7eb" opacity=".55"/>
    <rect x="31.9" y="26" width="1.8" height="12.2" rx="0.9" fill="#e5e7eb" opacity=".55"/>
    <rect x="34.6" y="26" width="1.8" height="12.2" rx="0.9" fill="#e5e7eb" opacity=".55"/>
    <circle cx="43.8" cy="37.5" r="6.1" fill="#22c55e" stroke="#ffffff" stroke-width="2"/>
    <path d="M41.6 37.8l1.1-2.1 2.2 1.2M45.1 37.2l-1.1 2.1-2.2-1.2"
          fill="none" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
  </g>
</svg>
`.trim();

  const tpsIcon = L.icon({
    iconUrl: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(tpsTrashPinSvg)}`,
    iconSize: [44, 52],
    iconAnchor: [22, 50],
    popupAnchor: [0, -46],
  });

  onValue(ref(db, "tempat_sampah"), (snapshot) => {
    const data = snapshot.val();

    Object.values(tpsMarkers).forEach((marker) => map.removeLayer(marker));
    tpsMarkers = {};

    if (!data) return;

    Object.entries(data).forEach(([id, tps]) => {
      const lat = parseFloat(tps.koordinat?.latitude);
      const lng = parseFloat(tps.koordinat?.longitude);

      if (!isNaN(lat) && !isNaN(lng)) {
        const marker = L.marker([lat, lng], { icon: tpsIcon, riseOnHover: true })
          .addTo(map)
          .bindPopup(`
          <b>Tempat Sampah </b><br>
          Lokasi: ${tps.lokasi ?? "-"}
        `);
        
        const tooltipContent = `
          <div style="min-width:160px">
            <div style="font-weight:700">TPS ${id}</div>
            <div>${tps.lokasi ?? "-"}</div>
          </div>
        `;
        
        marker.bindTooltip(tooltipContent, {
          direction: "top",
          sticky: true,
          opacity: 0.95,
          offset: [0, -10],
          className: "tps-tooltip",
        });

        tpsMarkers[id] = marker;
      }
    });
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
    iconSize: [50, 50],
    iconAnchor: [24, 48],
    popupAnchor: [0, -44],
  });

  onValue(ref(db, "mobil"), (snapshot) => {
    const data = snapshot.val() || {};

    const nextIds = new Set(Object.keys(data));
    Object.keys(mobilMarkers).forEach((id) => {
      if (!nextIds.has(id)) {
        map.removeLayer(mobilMarkers[id]);
        delete mobilMarkers[id];
      }
    });

    const list = [];
    Object.entries(data).forEach(([id, mobil]) => {
      const lat = parseFloat(mobil.koordinat?.latitude);
      const lng = parseFloat(mobil.koordinat?.longitude);
      const plat = (mobil.plat ?? "-").toString().trim();
      if (!isNaN(lat) && !isNaN(lng)) list.push({ id, plat, lat, lng });
    });

    const groups = {};
    list.forEach((m) => {
      const key = `${m.lat.toFixed(6)},${m.lng.toFixed(6)}`;
      (groups[key] ||= []).push(m);
    });

    Object.values(groups).forEach((group) => {
      group.sort((a, b) => a.id.localeCompare(b.id));
    });

    Object.values(groups).forEach((group) => {
      const total = group.length;

      group.forEach((m, idx) => {
        const [dispLat, dispLng] = spreadLatLng(m.lat, m.lng, idx, total, 7);

        const popupHtml = `
          <div style="min-width:180px">
            <div style="font-weight:700">Mobil Pengangkut Sampah</div>
            <div><b>Plat:</b> ${m.plat}</div>
            <div style="opacity:.8;font-size:12px;margin-top:4px">
              ${m.lat.toFixed(6)}, ${m.lng.toFixed(6)}
            </div>
          </div>
        `;

        const tooltipHtml = `
          <div style="min-width:180px">
            <div style="font-weight:700">Mobil Pengangkut Sampah</div>
            <div><b>Plat:</b> ${m.plat}</div>
          </div>
        `;

        if (mobilMarkers[m.id]) {
          mobilMarkers[m.id].setLatLng([dispLat, dispLng]);
          if (mobilMarkers[m.id].getPopup()) mobilMarkers[m.id].setPopupContent(popupHtml);
          if (mobilMarkers[m.id].setTooltipContent) mobilMarkers[m.id].setTooltipContent(tooltipHtml);
        } else {
          const marker = L.marker([dispLat, dispLng], {
            icon: garbageTruckIcon,
            riseOnHover: true,
          })
            .addTo(map)
            .bindPopup(popupHtml);

          marker.bindTooltip(tooltipHtml, {
            direction: "top",
            sticky: true,
            opacity: 0.95,
            offset: [0, -10],
            className: "tps-tooltip",
          });

          mobilMarkers[m.id] = marker;
        }
      });
    });
  });
}