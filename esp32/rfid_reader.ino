/*
 * ============================================
 *  KantinKu - ESP32 NFC/RFID + Firebase
 *  Sistem Pembayaran Kantin Digital
 * ============================================
 * 
 * Wiring ESP32 -> RC522:
 *   3.3V    -> 3.3V
 *   GND     -> GND
 *   D5  -> SDA (SS)
 *   D18 -> SCK
 *   D23 -> MOSI
 *   D19 -> MISO
 *   D21 -> RST
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

    // Cek response tiap 500ms (lebih responsif)
    if (millis() - lastCheck > 500) {
        checkResponse();
        lastCheck = millis();
    }

    // Baca kartu RC522 (hanya jika sudah melewati cooldown 2 detik)
    static unsigned long lastScanTime = 0;
    if (millis() - lastScanTime < 2000) return;

    if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) return;

    lastScanTime = millis();
    String uid = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
        if (rfid.uid.uidByte[i] < 0x10) uid += "0";
        uid += String(rfid.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();
    Serial.println("\n[SCAN] RFID terdeteksi!");
    Serial.println("[SCAN] UID: " + uid);
    Serial.println("[SCAN] Sedang memproses...");

    // Kirim ke Firebase
    FirebaseJson json;
    json.set("rfid_uid", uid);
    json.set("device_id", DEVICE_ID);
    json.set("status", "pending");

    if (Firebase.RTDB.setJSON(&fbdo, "/scans/latest", &json)) {
        Serial.println("[OK] Data terkirim ke server");
        tone(BUZZER_PIN, 1500, 100);
    } else {
        Serial.println("[ERROR] Gagal kirim: " + fbdo.errorReason());
        buzzError();
    }

    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
}

void checkResponse() {
    if (Firebase.RTDB.getJSON(&fbdo, "/responses/" + String(DEVICE_ID))) {
        if (fbdo.dataType() != "null") {
            FirebaseJson &json = fbdo.jsonObject();
            FirebaseJsonData result;
            
            String status = "";
            String name = "Guest";
            String bal = "0";
            String msg = "";

            if (json.get(result, "status")) status = result.stringValue;
            if (json.get(result, "user_name")) name = result.stringValue;
            if (json.get(result, "balance")) bal = result.stringValue;
            if (json.get(result, "message")) msg = result.stringValue;

            if (status.length() > 0) {
                Serial.println("\n==================================");
                Serial.println("       HASIL SCAN RFID");
                Serial.println("==================================");
                
                if (status == "success") {
                    Serial.println("Status  : BERHASIL");
                    Serial.println("User    : " + name);
                    Serial.println("Saldo   : Rp " + bal);
                    if (msg != "Kartu valid") Serial.println("Pesan   : " + msg);
                    buzzSuccess();
                } else {
                    Serial.println("Status  : GAGAL");
                    if (name != "Guest") Serial.println("User    : " + name);
                    Serial.println("Pesan   : " + msg);
                    buzzError();
                }
                Serial.println("==================================\n");

                // Hapus response setelah dibaca agar tidak terbaca ulang
                Firebase.RTDB.deleteNode(&fbdo, "/responses/" + String(DEVICE_ID));
            }
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
