#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>
#include <LiquidCrystal_I2C.h>

// RFID Pins for ESP32
#define SS_PIN  5
#define RST_PIN 22

MFRC522 mfrc522(SS_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2);

// Configuration (Normally you would save this in Preferences/EEPROM via a Captive Portal)
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const char* serverUrl = "http://presensi.local/api/rfid/scan"; // Change to your actual domain
const char* apiKey = "YOUR_DEVICE_API_KEY"; // Must match the api_key in `rfid_devices` table

void setup() {
    Serial.begin(115200);
    SPI.begin();
    mfrc522.PCD_Init();
    
    lcd.init();
    lcd.backlight();
    lcd.setCursor(0, 0);
    lcd.print("Sistem Presensi");
    lcd.setCursor(0, 1);
    lcd.print("Menghubungkan...");

    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    
    Serial.println("\nWiFi Connected!");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Siap Digunakan");
    lcd.setCursor(0, 1);
    lcd.print("Tempelkan Kartu");
}

void loop() {
    // Look for new cards
    if (!mfrc522.PICC_IsNewCardPresent()) {
        return;
    }
    // Select one of the cards
    if (!mfrc522.PICC_ReadCardSerial()) {
        return;
    }

    String cardUid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
        cardUid += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
        cardUid += String(mfrc522.uid.uidByte[i], HEX);
    }
    cardUid.toUpperCase();
    
    Serial.println("Card UID: " + cardUid);
    
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Memproses...");

    // Send to Server
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(serverUrl);
        http.addHeader("Content-Type", "application/json");
        http.addHeader("X-Device-API-Key", apiKey);
        
        StaticJsonDocument<200> doc;
        doc["card_uid"] = cardUid;
        String requestBody;
        serializeJson(doc, requestBody);
        
        int httpResponseCode = http.POST(requestBody);
        
        if (httpResponseCode > 0) {
            String responseStr = http.getString();
            StaticJsonDocument<500> responseDoc;
            deserializeJson(responseDoc, responseStr);
            
            String status = responseDoc["status"]; // success or error
            String studentName = responseDoc["student_name"] | "Tidak Dikenal";
            
            lcd.clear();
            if (status == "success") {
                lcd.setCursor(0, 0);
                lcd.print("Berhasil:");
                lcd.setCursor(0, 1);
                lcd.print(studentName.substring(0, 16));
            } else {
                String message = responseDoc["message"] | "Gagal";
                lcd.setCursor(0, 0);
                lcd.print("Akses Ditolak");
                lcd.setCursor(0, 1);
                lcd.print(message.substring(0, 16));
            }
        } else {
            lcd.clear();
            lcd.setCursor(0, 0);
            lcd.print("Error Koneksi");
            lcd.setCursor(0, 1);
            lcd.print("Coba lagi");
            Serial.print("Error code: ");
            Serial.println(httpResponseCode);
        }
        http.end();
    } else {
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("WiFi Terputus!");
    }
    
    // Halt PICC to prevent multiple reads
    mfrc522.PICC_HaltA();
    delay(2000);
    
    // Reset LCD
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Siap Digunakan");
    lcd.setCursor(0, 1);
    lcd.print("Tempelkan Kartu");
}
