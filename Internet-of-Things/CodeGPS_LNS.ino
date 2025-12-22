#include <TinyGPS++.h>
#include <HardwareSerial.h>
#include <WiFi.h>
#include <Firebase_ESP_Client.h>

#include "addons/TokenHelper.h"
#include "addons/RTDBHelper.h"

#define WIFI_SSID "A.Naufal F.S"
#define WIFI_PASSWORD "tanyamamaku"

#define API_KEY "AIzaSyDnXwpAU-Gu9V4qgCXxkRc2xbVGr_YTt4Q"
#define DATABASE_URL "https://monitoring-project-c6b65-default-rtdb.firebaseio.com/"

FirebaseData fbdo;
FirebaseAuth auth;
FirebaseConfig config;

HardwareSerial GPSSerial(1);
TinyGPSPlus gps;

void setup() {
  Serial.begin(115200);

  GPSSerial.begin(9600, SERIAL_8N1, 16, 17);

  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.print("Connecting WiFi...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(300);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected!");

  config.api_key = API_KEY;
  config.database_url = DATABASE_URL;

  // wajib agar token diperbarui otomatis
  config.token_status_callback = tokenStatusCallback;

  // sign up pakai email dummy (sangat stabil)
  if (Firebase.signUp(&config, &auth, "gps@test.com", "12345678")) {
    Serial.println("Firebase Ready!");
  } else {
    Serial.print("Firebase Error: ");
    Serial.println(config.signer.signupError.message.c_str());
  }

  Firebase.begin(&config, &auth);
  Firebase.reconnectWiFi(true);
}

void loop() {

  while (GPSSerial.available()) {
    gps.encode(GPSSerial.read());
  }

  if (gps.location.isValid()) {

    double lat = gps.location.lat();
    double lng = gps.location.lng();

    Serial.printf("Lat: %.6f | Lng: %.6f\n", lat, lng);

    if (Firebase.ready()) {

      if (Firebase.RTDB.setDouble(&fbdo, "mobil/mobilID001/koordinat/latitude", lat)) {
        Serial.println("Latitude OK");
      } else {
        Serial.println(fbdo.errorReason());
      }

      if (Firebase.RTDB.setDouble(&fbdo, "mobil/mobilID001/koordinat/longitude", lng)) {
        Serial.println("Longitude OK");
      } else {
        Serial.println(fbdo.errorReason());
      }
    }
  } else {
    Serial.println("GPS belum lock...");
  }

  delay(2000);
}
