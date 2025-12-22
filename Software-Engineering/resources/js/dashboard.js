import { initializeApp } from "https://www.gstatic.com/firebasejs/11.1.0/firebase-app.js";
import { getDatabase, ref, onValue, get, set, update } from "https://www.gstatic.com/firebasejs/11.1.0/firebase-database.js";

// === Firebase Config ===
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

// === Init Firebase ===
const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

// === Ambil ID user login dari Laravel Blade ===
const USER_ID = window.Laravel?.user_id ?? null;

if(!USER_ID){
  console.warm('USER_ID kosong - notifikasi user dibypass sampai login.')
}

// === Inisialisasi Map ===
const map = L.map("map").setView([-4.0095, 119.6291], 14);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "&copy; OpenStreetMap contributors"
}).addTo(map);

let tpsMarkers = {};
let mobilMarkers = {};

// === Ambil daftar notifikasi yang sudah dibaca oleh user ===
async function getUserNotifRead() {
  if (!USER_ID) return {};
  const snapshot = await get(ref(db, `user_petugas/${USER_ID}/notif_terbaca`));
  return snapshot.exists() ? snapshot.val() : {}; // default object kosong
}

// === Tandai TPS penuh sebagai sudah dibaca ===
async function markNotifAsRead(tpsIds) {
  if (!USER_ID) return {};
  const updates = {};
  tpsIds.forEach(id => {
    updates[`user_petugas/${USER_ID}/notif_terbaca/${id}`] = true;
  });
  await update(ref(db), updates);
}

// === Fungsi untuk menampilkan notifikasi modern ===
function showNotifications(tpsList) {
  const notifContainer = document.getElementById("notification");
  if (!notifContainer) return {};
  const messages = tpsList.map(tps =>
    `<li><strong>TPS ${tps.id}</strong> di ${tps.lokasi} sudah penuh (${tps.volume}%)</li>`
  ).join("");

  notifContainer.innerHTML = `
    <div class="alert alert-danger shadow-sm">
      <h5 class="mb-2">⚠ TPS Penuh</h5>
      <ul class="mb-0">${messages}</ul>
    </div>
  `;

  notifContainer.classList.remove("d-none");
}

// === Realtime listener untuk TPS ===
onValue(ref(db, "tempat_sampah"), async (snapshot) => {
  const data = snapshot.val();

  // Hapus marker lama
  Object.values(tpsMarkers).forEach(marker => map.removeLayer(marker));
  tpsMarkers = {};

  if (!data) return;

  // Ambil daftar TPS yang sudah dibaca user ini
  const userNotifRead = await getUserNotifRead();

  let notifToShow = [];

  Object.entries(data).forEach(([id, tps]) => {
    const lat = parseFloat(tps.koordinat?.latitude);
    const lng = parseFloat(tps.koordinat?.longitude);
    const volume = tps.volume ?? 0;

    if (!isNaN(lat) && !isNaN(lng)) {
      // Warna marker sesuai volume
      let color = "green";
      if (volume >= 75) color = "red";
      else if (volume >= 40) color = "orange";

      const marker = L.circleMarker([lat, lng], { color, radius: 8 })
        .addTo(map)
        .bindPopup(`
          <b>TPS ${id}</b><br>
          Lokasi: ${tps.lokasi ?? '-'}<br>
          Volume: ${volume}%
        `);

      tpsMarkers[id] = marker;

      // === Jika TPS penuh dan belum dibaca oleh user ===
      if (volume >= 75 && !userNotifRead[id]) {
        notifToShow.push({ id, lokasi: tps.lokasi, volume });
      }
    }
  });

  // Jika ada TPS penuh yang baru, tampilkan semua notifikasi sekaligus
  if (notifToShow.length > 0) {
    showNotifications(notifToShow);
    // Tandai TPS yang ditampilkan sebagai sudah dibaca
    markNotifAsRead(notifToShow.map(tps => tps.id));
  }
});

// === Listener realtime untuk mobil ===
onValue(ref(db, "mobil"), (snapshot) => {
  const data = snapshot.val();

  // Hapus marker lama mobil
  Object.values(mobilMarkers).forEach(marker => map.removeLayer(marker));
  mobilMarkers = {};

  if (data) {
    Object.entries(data).forEach(([id, mobil]) => {
      const lat = parseFloat(mobil.koordinat?.latitude);
      const lng = parseFloat(mobil.koordinat?.longitude);

      if (!isNaN(lat) && !isNaN(lng)) {
        const marker = L.marker([lat, lng], {
          icon: L.icon({
            iconUrl: "https://cdn-icons-png.flaticon.com/512/3202/3202926.png",
            iconSize: [35, 35]
          })
        }).addTo(map)
          .bindPopup(`<b>Mobil ${id}</b><br>Status: ${mobil.status}`);

        mobilMarkers[id] = marker;
      }
    });
  }
});