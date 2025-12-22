import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

public class RouteModel {

    // ==========================
    // Konfigurasi
    // ==========================

    // Path ke file CSV (sesuaikan dengan lokasi file kamu)
    private static final String CSV_PATH =
            "C:\\Users\\ACER\\project sistem cerdas LNS\\algoritma_LNS.csv";

    // Ambang minimal persentase TPS yang akan dikunjungi (boleh kamu ubah)
    private static final double MIN_PERCENTAGE = 80.0;

    // Jari-jari bumi (meter) untuk Haversine
    private static final double EARTH_RADIUS_METERS = 6371000.0;

    // ==========================
    // Model data lokasi
    // ==========================

    public static class Location {
        public int idLokasi;
        public String kodeNode;
        public String namaLokasi;
        public double latitude;
        public double longitude;
        public Double persentasePenuh; // bisa null untuk DLH
        public String jenis;           // "Depo" atau "TPS"

        public Location(int idLokasi, String kodeNode, String namaLokasi,
                        double latitude, double longitude,
                        Double persentasePenuh, String jenis) {
            this.idLokasi = idLokasi;
            this.kodeNode = kodeNode;
            this.namaLokasi = namaLokasi;
            this.latitude = latitude;
            this.longitude = longitude;
            this.persentasePenuh = persentasePenuh;
            this.jenis = jenis;
        }

        @Override
        public String toString() {
            return String.format("[%s] %s (%.6f, %.6f) %s%%",
                    kodeNode,
                    namaLokasi,
                    latitude,
                    longitude,
                    persentasePenuh == null ? "-" : String.format("%.1f", persentasePenuh));
        }
    }

    // ==========================
    // Fungsi baca dataset CSV
    // ==========================

    public static List<Location> loadLocationsFromCsv(String csvPath) throws IOException {
        List<Location> locations = new ArrayList<>();

        try (BufferedReader br = new BufferedReader(new FileReader(csvPath))) {
            String line;
            boolean firstLine = true;

            while ((line = br.readLine()) != null) {
                // Lewati header
                if (firstLine) {
                    firstLine = false;
                    continue;
                }

                if (line.trim().isEmpty()) {
                    continue;
                }

                // File CSV dari Excel pakai pemisah titik-koma (;)
                // -1 supaya kolom kosong di akhir tetap terbaca
                String[] parts = line.split(";", -1);

                if (parts.length < 7) {
                    System.out.println("Baris tidak lengkap, dilewati: " + line);
                    continue;
                }

                try {
                    int idLokasi = Integer.parseInt(parts[0].trim());
                    String kodeNode = parts[1].trim();
                    String namaLokasi = parts[2].trim();
                    double latitude = Double.parseDouble(parts[3].trim());
                    double longitude = Double.parseDouble(parts[4].trim());

                    String persenStr = parts[5].trim();
                    Double persentasePenuh = null;
                    if (!persenStr.isEmpty()) {
                        persentasePenuh = Double.parseDouble(persenStr);
                    }

                    String jenis = parts[6].trim();

                    Location loc = new Location(
                            idLokasi,
                            kodeNode,
                            namaLokasi,
                            latitude,
                            longitude,
                            persentasePenuh,
                            jenis
                    );
                    locations.add(loc);
                } catch (NumberFormatException ex) {
                    System.out.println("Gagal parse baris, dilewati: " + line);
                }
            }
        }

        return locations;
    }

    // ==========================
    // Fungsi Haversine (meter)
    // ==========================

    public static double haversineDistanceMeters(double lat1, double lon1,
                                                 double lat2, double lon2) {
        double radLat1 = Math.toRadians(lat1);
        double radLat2 = Math.toRadians(lat2);
        double deltaLat = Math.toRadians(lat2 - lat1);
        double deltaLon = Math.toRadians(lon2 - lon1);

        double a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2)
                + Math.cos(radLat1) * Math.cos(radLat2)
                * Math.sin(deltaLon / 2) * Math.sin(deltaLon / 2);

        double c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return EARTH_RADIUS_METERS * c;
    }

    public static double haversineDistanceMeters(Location a, Location b) {
        return haversineDistanceMeters(a.latitude, a.longitude, b.latitude, b.longitude);
    }

    // ==========================
    // Nearest Neighbor Route
    // ==========================

    public static List<Integer> nearestNeighborRoute(List<Location> locations, int startIndex) {
        int n = locations.size();
        boolean[] visited = new boolean[n];
        List<Integer> route = new ArrayList<>();

        int current = startIndex;
        visited[current] = true;
        route.add(current);

        for (int step = 1; step < n; step++) {
            double minDist = Double.MAX_VALUE;
            int nearest = -1;

            for (int j = 0; j < n; j++) {
                if (!visited[j]) {
                    double dist = haversineDistanceMeters(
                            locations.get(current),
                            locations.get(j)
                    );
                    if (dist < minDist) {
                        minDist = dist;
                        nearest = j;
                    }
                }
            }

            if (nearest == -1) {
                // tidak ada node tersisa (harusnya tidak terjadi)
                break;
            }

            visited[nearest] = true;
            route.add(nearest);
            current = nearest;
        }

        return route;
    }

    // ==========================
    // Main: Baca dataset + hitung rute
    // ==========================

    public static void main(String[] args) {
        try {
            System.out.println("Memuat dataset dari: " + CSV_PATH);
            List<Location> allLocations = loadLocationsFromCsv(CSV_PATH);
            System.out.println("Total lokasi terbaca: " + allLocations.size());

            // Cari node DLH sebagai titik awal
            int depotIndex = -1;
            for (int i = 0; i < allLocations.size(); i++) {
                if (allLocations.get(i).kodeNode.equalsIgnoreCase("dlh")) {
                    depotIndex = i;
                    break;
                }
            }

            if (depotIndex == -1) {
                System.out.println("ERROR: Node dengan kode_node = 'dlh' tidak ditemukan di CSV.");
                return;
            }

            Location depot = allLocations.get(depotIndex);
            System.out.println("Depot awal: " + depot);

            // Bangun daftar lokasi aktif:
            // - Depot
            // - Semua TPS dengan persentase_penuh >= MIN_PERCENTAGE
            List<Location> activeLocations = new ArrayList<>();
            activeLocations.add(depot); // indeks 0

            for (int i = 0; i < allLocations.size(); i++) {
                if (i == depotIndex) continue;

                Location loc = allLocations.get(i);
                if (loc.persentasePenuh != null && loc.persentasePenuh >= MIN_PERCENTAGE) {
                    activeLocations.add(loc);
                }
            }

            System.out.println("Jumlah lokasi aktif (TPS >= " + MIN_PERCENTAGE + "%) + depot: "
                    + activeLocations.size());

            if (activeLocations.size() <= 1) {
                System.out.println("Tidak ada TPS yang memenuhi ambang " + MIN_PERCENTAGE +
                        "%. Rute tidak bisa dihitung.");
                return;
            }

            // Jalankan Nearest Neighbor dari depot (indeks 0 pada activeLocations)
            List<Integer> routeIndices = nearestNeighborRoute(activeLocations, 0);

            // Hitung total jarak rute (pergi) + kembali ke depot
            double totalDistanceGo = 0.0;
            for (int i = 0; i < routeIndices.size() - 1; i++) {
                Location from = activeLocations.get(routeIndices.get(i));
                Location to = activeLocations.get(routeIndices.get(i + 1));
                totalDistanceGo += haversineDistanceMeters(from, to);
            }

            // Jarak kembali ke depot
            Location last = activeLocations.get(routeIndices.get(routeIndices.size() - 1));
            double distanceBack = haversineDistanceMeters(last, depot);
            double totalDistanceRoundTrip = totalDistanceGo + distanceBack;

            // Tampilkan hasil rute
            System.out.println("\n=== RUTE OPTIMAL (Nearest Neighbor) ===");
            for (int step = 0; step < routeIndices.size(); step++) {
                int idx = routeIndices.get(step);
                Location loc = activeLocations.get(idx);
                if (step == 0) {
                    System.out.printf("Start [%d] %s%n", step, loc);
                } else {
                    System.out.printf(" -> [%d] %s%n", step, loc);
                }
            }
            System.out.println("Kembali ke depot: " + depot.namaLokasi);

            System.out.printf("%nTotal jarak pergi (tanpa kembali): %.2f km%n",
                    totalDistanceGo / 1000.0);
            System.out.printf("Jarak kembali ke depot: %.2f km%n",
                    distanceBack / 1000.0);
            System.out.printf("Total jarak PP (round trip): %.2f km%n",
                    totalDistanceRoundTrip / 1000.0);

        } catch (IOException e) {
            System.out.println("Terjadi kesalahan saat membaca CSV: " + e.getMessage());
        }
    }
}
