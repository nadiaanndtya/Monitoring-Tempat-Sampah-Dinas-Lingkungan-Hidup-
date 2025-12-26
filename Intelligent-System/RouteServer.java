import com.sun.net.httpserver.HttpServer;
import com.sun.net.httpserver.HttpHandler;
import com.sun.net.httpserver.HttpExchange;

import java.io.IOException;
import java.io.OutputStream;
import java.net.InetSocketAddress;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.List;

public class RouteServer {

    private static final String CSV_PATH =
            "C:\\Users\\ACER\\project sistem cerdas LNS\\algoritma_LNS.csv";

    private static final double MIN_PERCENTAGE = 80.0;

    public static void main(String[] args) throws IOException {
        int port = 9090; // http://localhost:9090

        HttpServer server = HttpServer.create(new InetSocketAddress(port), 0);

        server.createContext("/route", new RouteHandler());

        server.createContext("/", new StaticFileHandler("index.html",
                "text/html; charset=utf-8"));

        server.createContext("/index.js", new StaticFileHandler("index.js",
                "application/javascript; charset=utf-8"));

        server.createContext("/index.html", new StaticFileHandler("index.html",
                "text/html; charset=utf-8"));

        server.setExecutor(null); // default executor

        System.out.println("Server berjalan di:");
        System.out.println("  Peta  : http://localhost:" + port + "/");
        System.out.println("  Route : http://localhost:" + port + "/route");
        server.start();
    }

    static class StaticFileHandler implements HttpHandler {
        private final Path filePath;
        private final String contentType;

        StaticFileHandler(String fileName, String contentType) {
            this.filePath = Paths.get(fileName);
            this.contentType = contentType;
        }

        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (!"GET".equalsIgnoreCase(exchange.getRequestMethod())) {
                exchange.sendResponseHeaders(405, -1); // Method Not Allowed
                return;
            }

            if (!Files.exists(filePath)) {
                // File tidak ada -> 404
                exchange.sendResponseHeaders(404, -1);
                return;
            }

            byte[] bytes = Files.readAllBytes(filePath);
            exchange.getResponseHeaders().set("Content-Type", contentType);
            exchange.sendResponseHeaders(200, bytes.length);

            try (OutputStream os = exchange.getResponseBody()) {
                os.write(bytes);
            }
        }
    }

    static class RouteHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (!"GET".equalsIgnoreCase(exchange.getRequestMethod())) {
                exchange.sendResponseHeaders(405, -1); // Method Not Allowed
                return;
            }

            String response;
            try {
                response = buildRouteReport();
            } catch (Exception e) {
                e.printStackTrace();
                response = "Terjadi error saat menghitung rute: " + e.getMessage();
            }

            byte[] bytes = response.getBytes(StandardCharsets.UTF_8);
            exchange.getResponseHeaders().add("Content-Type", "text/plain; charset=utf-8");
            exchange.sendResponseHeaders(200, bytes.length);

            try (OutputStream os = exchange.getResponseBody()) {
                os.write(bytes);
            }
        }
    }

    private static String buildRouteReport() throws IOException {
        List<RouteModel.Location> allLocations =
                RouteModel.loadLocationsFromCsv(CSV_PATH);

        StringBuilder sb = new StringBuilder();
        sb.append("Total lokasi terbaca: ")
          .append(allLocations.size())
          .append("\n");

        int depotIndex = -1;
        for (int i = 0; i < allLocations.size(); i++) {
            if (allLocations.get(i).kodeNode.equalsIgnoreCase("dlh")) {
                depotIndex = i;
                break;
            }
        }

        if (depotIndex == -1) {
            sb.append("ERROR: Node dengan kode_node = 'dlh' tidak ditemukan.\n");
            return sb.toString();
        }

        RouteModel.Location depot = allLocations.get(depotIndex);
        sb.append("Depot awal: ").append(depot).append("\n");

        List<RouteModel.Location> activeLocations = new ArrayList<>();
        activeLocations.add(depot); // indeks 0 = depot

        for (int i = 0; i < allLocations.size(); i++) {
            if (i == depotIndex) continue;

            RouteModel.Location loc = allLocations.get(i);
            if (loc.persentasePenuh != null && loc.persentasePenuh >= MIN_PERCENTAGE) {
                activeLocations.add(loc);
            }
        }

        sb.append("Jumlah lokasi aktif (TPS >= ")
          .append(MIN_PERCENTAGE)
          .append("%) + depot: ")
          .append(activeLocations.size())
          .append("\n");

        if (activeLocations.size() <= 1) {
            sb.append("Tidak ada TPS yang memenuhi ambang, rute tidak bisa dihitung.\n");
            return sb.toString();
        }

        List<Integer> routeIndices =
                RouteModel.nearestNeighborRoute(activeLocations, 0);

        double totalDistanceGo = 0.0;
        for (int i = 0; i < routeIndices.size() - 1; i++) {
            RouteModel.Location from = activeLocations.get(routeIndices.get(i));
            RouteModel.Location to   = activeLocations.get(routeIndices.get(i + 1));
            totalDistanceGo += RouteModel.haversineDistanceMeters(from, to);
        }

        RouteModel.Location last =
                activeLocations.get(routeIndices.get(routeIndices.size() - 1));
        double distanceBack =
                RouteModel.haversineDistanceMeters(last, depot);
        double totalDistanceRoundTrip = totalDistanceGo + distanceBack;

        sb.append("\n=== RUTE OPTIMAL (Nearest Neighbor) ===\n");
        for (int step = 0; step < routeIndices.size(); step++) {
            int idx = routeIndices.get(step);
            RouteModel.Location loc = activeLocations.get(idx);
            if (step == 0) {
                sb.append("Start [")
                  .append(step)
                  .append("] ")
                  .append(loc)
                  .append("\n");
            } else {
                sb.append(" -> [")
                  .append(step)
                  .append("] ")
                  .append(loc)
                  .append("\n");
            }
        }
        sb.append("Kembali ke depot: ")
          .append(depot.namaLokasi)
          .append("\n\n");

        sb.append(String.format("Total jarak pergi (tanpa kembali): %.2f km%n",
                totalDistanceGo / 1000.0));
        sb.append(String.format("Jarak kembali ke depot: %.2f km%n",
                distanceBack / 1000.0));
        sb.append(String.format("Total jarak PP (round trip): %.2f km%n",
                totalDistanceRoundTrip / 1000.0));

        return sb.toString();
    }
}

