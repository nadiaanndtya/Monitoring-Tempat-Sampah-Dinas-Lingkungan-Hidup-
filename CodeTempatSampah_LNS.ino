/*
  ESP32 SMART TRASH BIN (3 Unit)
  - 3 tempat sampah: masing-masing punya
    1x Ultrasonic FULL, 1x Ultrasonic TANGAN, 1x SERVO
  - 1x LCD I2C 16x2 (bergiliran tampil TPS1->TPS2->TPS3)
  - Kirim ke Firebase RTDB: tempat_sampah/TPSID00X/{volume,status}

  PENTING (hardware):
  - Jika pakai HC-SR04 5V: pin ECHO keluaran 5V WAJIB diturunkan ke 3.3V (pembagi resistor/level shifter) sebelum ke ESP32.
*/

#include <WiFi.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <ESP32Servo.h>
#include <Firebase_ESP_Client.h>

#include "addons/TokenHelper.h"
#include "addons/RTDBHelper.h"

// ===================== WIFI & FIREBASE =====================
#define WIFI_SSID       "alnis"
#define WIFI_PASSWORD   "12345670"

#define API_KEY         "AIzaSyDnXwpAU-Gu9V4qgCXxkRc2xbVGr_YTt4Q"
#define DATABASE_URL    "https://monitoring-project-c6b65-default-rtdb.firebaseio.com/"

// ===================== AUTH MODE =====================

#define USE_EMAIL_PASSWORD  1

#if USE_EMAIL_PASSWORD
  #define USER_EMAIL     "andiluluas@gmail.com"
  #define USER_PASSWORD  "123456"
#endif

// ===================== FIREBASE OBJECTS =====================
FirebaseData fbdo;
FirebaseAuth auth;
FirebaseConfig config;

// ===================== LCD (1 UNIT) =====================
LiquidCrystal_I2C lcd(0x27, 16, 2);

// ===================== JUMLAH BIN =====================
static const int BIN_COUNT = 3;

const char* BIN_ID[BIN_COUNT] = {
  "TPSID001",
  "TPSID002",
  "TPSID003"
};



const uint8_t TRIG_FULL[BIN_COUNT] = {32, 27, 17};
const uint8_t ECHO_FULL[BIN_COUNT] = {33, 14, 16};

const uint8_t TRIG_HAND[BIN_COUNT] = {19, 18, 23};
const uint8_t ECHO_HAND[BIN_COUNT] = {34, 35, 36};

const uint8_t SERVO_PIN[BIN_COUNT] = {13, 12, 15};

// ===================== SERVO OBJECTS =====================
Servo servos[BIN_COUNT];

// ===================== PARAMETER =====================
#define MAX_HEIGHT_CM         30.0     // tinggi bin (cm) dari sensor FULL ke dasar
#define HAND_OPEN_DIST_CM     15.0     // jarak tangan untuk buka
#define FULLNESS_BLOCK_OPEN   90       // jika >= ini, servo tidak dibuka
#define SERVO_OPEN_ANGLE      90
#define SERVO_CLOSE_ANGLE     0
#define SERVO_DELAY_MS        5000     // servo tutup otomatis setelah ms

// ===================== FILTER MOVING AVERAGE =====================
#define FILTER_SIZE 10
float filterBuffer[BIN_COUNT][FILTER_SIZE];
int   filterIndex[BIN_COUNT]  = {0, 0, 0};
bool  filterFilled[BIN_COUNT] = {false, false, false};

// ===================== STATE =====================
bool isOpen[BIN_COUNT] = {false, false, false};
unsigned long lastOpenTime[BIN_COUNT] = {0, 0, 0};

// simpan nilai terakhir (buat LCD & kirim Firebase)
int    currentFullness[BIN_COUNT] = {0, 0, 0};
String currentStatus[BIN_COUNT]   = {"", "", ""};
float  currentFullDist[BIN_COUNT] = {0, 0, 0};
float  currentHandDist[BIN_COUNT] = {0, 0, 0};

// throttle kirim Firebase
int lastSentFullness[BIN_COUNT] = {-1, -1, -1};
String lastSentStatus[BIN_COUNT] = {"", "", ""};
unsigned long lastSendMs[BIN_COUNT] = {0, 0, 0};
const unsigned long SEND_INTERVAL_MS = 1500;

// LCD rotate
unsigned long lastLcdMs = 0;
int lcdIndex = 0;
const unsigned long LCD_ROTATE_MS = 2000;

// ===================== LCD PRINT (AMAN, TANPA String(' ', n)) =====================
static inline void lcdPrint16(uint8_t col, uint8_t row, const String &text) {
  lcd.setCursor(col, row);
  for (int i = 0; i < 16; i++) {
    if (i < (int)text.length()) lcd.print(text[i]);
    else lcd.print(' ');
  }
}

// ===================== ULTRASONIC READ =====================
float getDistanceCM(uint8_t trigPin, uint8_t echoPin) {
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

  long duration = pulseIn(echoPin, HIGH, 30000); // timeout 30ms
  if (duration <= 0) return MAX_HEIGHT_CM;

  float distance = (float)duration * 0.034f / 2.0f;
  if (distance < 2 || distance > 400) return MAX_HEIGHT_CM;
  return distance;
}

float smoothDistance(int bin, float newVal) {
  filterBuffer[bin][filterIndex[bin]] = newVal;
  filterIndex[bin] = (filterIndex[bin] + 1) % FILTER_SIZE;
  if (filterIndex[bin] == 0) filterFilled[bin] = true;

  float sum = 0.0f;
  int count = filterFilled[bin] ? FILTER_SIZE : filterIndex[bin];
  if (count <= 0) count = 1;

  for (int i = 0; i < count; i++) sum += filterBuffer[bin][i];
  return sum / (float)count;
}

String calcStatus(int fullness) {
  if (fullness >= 80) return "penuh";
  if (fullness >= 40) return "setengah";
  return "kosong";
}

// ===================== FIREBASE SEND =====================
void sendToFirebaseIfNeeded(int bin, int fullness, const String &status) {
  unsigned long now = millis();

  bool changed = (fullness != lastSentFullness[bin]) || (status != lastSentStatus[bin]);
  bool timeOk  = (now - lastSendMs[bin] >= SEND_INTERVAL_MS);

  if (!Firebase.ready()) return;
  if (!changed && !timeOk) return;

  String nodePath = String("tempat_sampah/") + BIN_ID[bin];

  FirebaseJson json;
  json.set("volume", fullness);
  json.set("status", status);


// KIRIM DATA KE FIREBASE
  if (Firebase.RTDB.updateNode(&fbdo, nodePath.c_str(), &json)) {
    Serial.printf("✔ [%s] update OK (vol=%d, status=%s)\n", BIN_ID[bin], fullness, status.c_str());
    lastSentFullness[bin] = fullness;
    lastSentStatus[bin]   = status;
    lastSendMs[bin]       = now;
  } else {
    Serial.printf("✖ [%s] update gagal: %s\n", BIN_ID[bin], fbdo.errorReason().c_str());
  }
}

// ===================== WIFI CONNECT =====================
void connectWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.setAutoReconnect(true);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  Serial.print("Menghubungkan WiFi");
  lcdPrint16(0, 0, "KELOMPOK LNS");
  lcdPrint16(0, 1, "WiFi connecting");

  unsigned long start = millis();
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(300);
    if (millis() - start > 20000) { // 20 detik timeout agar tidak hang selamanya
      Serial.println("\nWiFi timeout, restart...");
      ESP.restart();
    }
  }

  Serial.println();
  Serial.print("WiFi OK, IP: ");
  Serial.println(WiFi.localIP());
}

// ===================== FIREBASE INIT =====================
void initFirebase() {
  config.api_key = API_KEY;
  config.database_url = DATABASE_URL;
  config.token_status_callback = tokenStatusCallback;

#if USE_EMAIL_PASSWORD
  auth.user.email = USER_EMAIL;
  auth.user.password = USER_PASSWORD;
#else
  // Anonymous (pastikan enabled di Firebase Console)
  config.signer.anonymous = true;
#endif

  Firebase.begin(&config, &auth);
  Firebase.reconnectWiFi(true);
}

// ===================== SETUP =====================
void setup() {
  Serial.begin(115200);

  // LCD I2C (SDA=21, SCL=22)
  Wire.begin(21, 22);
  lcd.init();
  lcd.backlight();

  connectWiFi();
  initFirebase();

  // Pin + Servo
  for (int i = 0; i < BIN_COUNT; i++) {
    pinMode(TRIG_FULL[i], OUTPUT);
    pinMode(ECHO_FULL[i], INPUT);

    pinMode(TRIG_HAND[i], OUTPUT);
    pinMode(ECHO_HAND[i], INPUT);

    servos[i].attach(SERVO_PIN[i]);
    servos[i].write(SERVO_CLOSE_ANGLE);
  }

  lcdPrint16(0, 0, "Mencoba");
  lcdPrint16(0, 1, "Stabilisasi...");
  delay(1500);
  lcd.clear();
}

// ===================== LOOP =====================
void loop() {
  // 1) Update semua bin
  for (int i = 0; i < BIN_COUNT; i++) {
    // FULL sensor (pakai smoothing)
    float dFullRaw = getDistanceCM(TRIG_FULL[i], ECHO_FULL[i]);
    float dFullSm  = smoothDistance(i, dFullRaw);
    currentFullDist[i] = dFullSm;

    float fullnessF = ((MAX_HEIGHT_CM - dFullSm) / MAX_HEIGHT_CM) * 100.0f;
    fullnessF = constrain(fullnessF, 0.0f, 100.0f);
    int fullness = (int)round(fullnessF);

    String status = calcStatus(fullness);

    currentFullness[i] = fullness;
    currentStatus[i]   = status;

    // HAND sensor
    float dHand = getDistanceCM(TRIG_HAND[i], ECHO_HAND[i]);
    currentHandDist[i] = dHand;

    // Servo logic
    if (dHand > 0 && dHand < HAND_OPEN_DIST_CM && !isOpen[i] && fullness < FULLNESS_BLOCK_OPEN) {
      servos[i].write(SERVO_OPEN_ANGLE);
      isOpen[i] = true;
      lastOpenTime[i] = millis();
    }

    if (isOpen[i] && (millis() - lastOpenTime[i] > SERVO_DELAY_MS)) {
      servos[i].write(SERVO_CLOSE_ANGLE);
      isOpen[i] = false;
    }

    // Debug
    Serial.printf("[%s] full=%.1fcm hand=%.1fcm => %d%% (%s) open=%d\n",
                  BIN_ID[i],
                  currentFullDist[i],
                  currentHandDist[i],
                  currentFullness[i],
                  currentStatus[i].c_str(),
                  isOpen[i] ? 1 : 0);

    // Kirim ke Firebase (throttled)
    sendToFirebaseIfNeeded(i, currentFullness[i], currentStatus[i]);

    // Jeda kecil untuk kurangi crosstalk ultrasonic
    delay(60);
  }

  // 2) LCD rotate (1 LCD untuk 3 bin)
  unsigned long now = millis();
  if (now - lastLcdMs >= LCD_ROTATE_MS) {
    lastLcdMs = now;
    lcdIndex = (lcdIndex + 1) % BIN_COUNT;
  }

  String line1 = String("TPS") + String(lcdIndex + 1) + ": " + currentStatus[lcdIndex];
  String line2 = String("Vol:") + String(currentFullness[lcdIndex]) + "% " +
                 (isOpen[lcdIndex] ? "OPEN" : "CLOSE");

  lcdPrint16(0, 0, line1);
  lcdPrint16(0, 1, line2);

  delay(300);
}
