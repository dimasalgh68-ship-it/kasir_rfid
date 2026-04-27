/*
 * ============================================
 *  KantinKu - ESP32 NFC/RFID + Firebase
 *  Sistem Pembayaran Kantin Digital
 * ============================================
 * 
 * Wiring ESP32 -> RC522:
 *   3.3V    -> 3.3V
 *   GND     -> GND
 *   GPIO 5  -> SDA (SS)
 *   GPIO 18 -> SCK
 *   GPIO 23 -> MOSI
 *   GPIO 19 -> MISO
 *   GPIO 21 -> RST
 * 
 * Buzzer -> GPIO 4
 * 
 * Library:
 *   1. MFRC522 (miguelbalboa)
 *   2. Firebase ESP32 Client (mobizt)
 */

#include <WiFi.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Firebase_ESP_Client.h>
#include "addons/TokenHelper.h"
#include "addons/RTDBHelper.h"

// ===== WiFi =====
const char* WIFI_SSID     = "Dimas123";
const char* WIFI_PASSWORD = "12345678";

// ===== Firebase Database URL (Hapus https:// jika masih error) =====
#define FIREBASE_DB_URL "device-streaming-76c51e32-default-rtdb.asia-southeast1.firebasedatabase.app"

const char* DEVICE_ID = "ESP32-KANTIN-01";

// ===== Pin =====
#define SS_PIN     5
#define RST_PIN    21
#define BUZZER_PIN 4
#define LED_PIN    2

// ===== Objects =====
MFRC522 rfid(SS_PIN, RST_PIN);
FirebaseData fbdo;
FirebaseAuth auth;
FirebaseConfig config;

unsigned long lastHeartbeat = 0;
unsigned long lastCheck = 0;

void setup() {
    Serial.begin(115200);
    Serial.println("\n=== KantinKu NFC/RFID + Firebase ===\n");

    pinMode(BUZZER_PIN, OUTPUT);
    pinMode(LED_PIN, OUTPUT);

    SPI.begin(18, 19, 23, 5);  // SCK, MISO, MOSI, SS - eksplisit untuk ESP32
    rfid.PCD_Init();
    delay(100);
    
    // Cek apakah RC522 terbaca
    byte v = rfid.PCD_ReadRegister(rfid.VersionReg);
    Serial.print("[RC522] Firmware: 0x");
    Serial.println(v, HEX);
    if (v == 0x00 || v == 0xFF) {
        Serial.println("[RC522] ERROR: Tidak terdeteksi! Cek wiring!");
    } else {
        Serial.println("[RC522] Ready - Siap scan");
    }

    // WiFi
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    Serial.print("[WiFi] Connecting");
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\n[WiFi] IP: " + WiFi.localIP().toString());

    // Firebase - tanpa API Key, tanpa Auth
    config.database_url = FIREBASE_DB_URL;
    config.signer.test_mode = true;  // Mode test, tanpa API key
    config.token_status_callback = tokenStatusCallback;

    Firebase.begin(&config, &auth);
    Firebase.reconnectNetwork(true);

    Serial.println("[Firebase] Connected!");
    Firebase.RTDB.setString(&fbdo, "/devices/" + String(DEVICE_ID) + "/status", "online");
    buzzSuccess();

    Serial.println("\n[READY] Tap kartu atau tag NFC...\n");
}

void loop() {
    // Heartbeat 30s
    if (millis() - lastHeartbeat > 30000) {
        Firebase.RTDB.setString(&fbdo, "/devices/" + String(DEVICE_ID) + "/status", "online");
        Firebase.RTDB.setInt(&fbdo, "/devices/" + String(DEVICE_ID) + "/rssi", WiFi.RSSI());
        lastHeartbeat = millis();
        Serial.println("[HEARTBEAT] OK");
    }

    // Cek response tiap 1s
    if (millis() - lastCheck > 1000) {
        checkResponse();
        lastCheck = millis();
    }

    // Baca kartu RC522
    if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) return;

    String uid = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
        if (rfid.uid.uidByte[i] < 0x10) uid += "0";
        uid += String(rfid.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();
    Serial.println("[SCAN] UID: " + uid);

    // Kirim ke Firebase
    FirebaseJson json;
    json.set("rfid_uid", uid);
    json.set("device_id", DEVICE_ID);
    json.set("status", "pending");

    if (Firebase.RTDB.setJSON(&fbdo, "/scans/latest", &json)) {
        Serial.println("[OK] Scan terkirim");
        tone(BUZZER_PIN, 1500, 100);
        delay(150);
        noTone(BUZZER_PIN);
    } else {
        Serial.println("[ERROR] " + fbdo.errorReason());
        buzzError();
    }

    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    delay(2000);
}

void checkResponse() {
    if (Firebase.RTDB.getString(&fbdo, "/responses/" + String(DEVICE_ID) + "/status")) {
        String status = fbdo.stringData();
        if (status.length() > 0) {
            String name = "";
            String bal = "0";

            if (Firebase.RTDB.getString(&fbdo, "/responses/" + String(DEVICE_ID) + "/user_name")) {
                name = fbdo.stringData();
            }
            if (Firebase.RTDB.getString(&fbdo, "/responses/" + String(DEVICE_ID) + "/balance")) {
                bal = fbdo.stringData();
            }

            Serial.println("User: " + name + " | Saldo: Rp " + bal + " | Status: " + status);

            if (status == "success") buzzSuccess();
            else buzzError();

            Firebase.RTDB.deleteNode(&fbdo, "/responses/" + String(DEVICE_ID));
        }
    }
}

void buzzSuccess() {
    tone(BUZZER_PIN, 2000, 100); delay(150);
    tone(BUZZER_PIN, 2500, 100); delay(150);
    noTone(BUZZER_PIN);
}

void buzzError() {
    tone(BUZZER_PIN, 400, 300); delay(350);
    tone(BUZZER_PIN, 300, 300); delay(350);
    noTone(BUZZER_PIN);
}
