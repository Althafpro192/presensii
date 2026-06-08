#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Preferences.h>
#include <WebServer.h>
#include <ArduinoJson.h>

#define RST_PIN         22
#define SS_PIN          21

MFRC522 mfrc522(SS_PIN, RST_PIN);
Preferences preferences;
WebServer server(80);

String ssid;
String password;
String serverDomain;
String apiKey;
String deviceId;

// State management
bool isWritingMode = false;
String uidToWrite = "";
int currentJobId = 0;

void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  
  loadConfig();

  if (ssid == "") {
    setupAP();
  } else {
    connectWiFi();
    setupServer();
  }
}

void loop() {
  server.handleClient();
  
  if (WiFi.status() == WL_CONNECTED) {
    if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
      String cardUid = getUID();
      
      if (isWritingMode) {
        writeCard(cardUid);
      } else {
        sendScan(cardUid);
      }
      
      mfrc522.PICC_HaltA();
      delay(1000); // Prevent multi-scan
    }
  }
}

// ---------------------------
// Config & WiFi
// ---------------------------
void loadConfig() {
  preferences.begin("config", false);
  ssid = preferences.getString("ssid", "");
  password = preferences.getString("password", "");
  serverDomain = preferences.getString("serverDomain", "http://presensi.test");
  apiKey = preferences.getString("apiKey", "");
  deviceId = preferences.getString("deviceId", "1");
  preferences.end();
}

void setupAP() {
  WiFi.softAP("ESP32-RFID-Setup");
  server.on("/", HTTP_GET, []() {
    String html = "<html><body><h1>Setup Device</h1><form method='POST' action='/save'>";
    html += "SSID: <input name='ssid'><br>";
    html += "PASS: <input name='password' type='password'><br>";
    html += "SERVER: <input name='serverDomain' value='http://presensi.test'><br>";
    html += "API KEY: <input name='apiKey'><br>";
    html += "DEVICE ID: <input name='deviceId' value='1'><br>";
    html += "<input type='submit' value='Save'></form></body></html>";
    server.send(200, "text/html", html);
  });

  server.on("/save", HTTP_POST, []() {
    preferences.begin("config", false);
    preferences.putString("ssid", server.arg("ssid"));
    preferences.putString("password", server.arg("password"));
    preferences.putString("serverDomain", server.arg("serverDomain"));
    preferences.putString("apiKey", server.arg("apiKey"));
    preferences.putString("deviceId", server.arg("deviceId"));
    preferences.end();
    server.send(200, "text/plain", "Saved! Rebooting...");
    delay(2000);
    ESP.restart();
  });
  
  server.begin();
}

void connectWiFi() {
  WiFi.begin(ssid.c_str(), password.c_str());
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    Serial.print(".");
    attempts++;
  }
  if(WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi connected. IP: " + WiFi.localIP().toString());
  } else {
    setupAP(); // Fallback to AP if connection fails
  }
}

// ---------------------------
// Server API (Listening)
// ---------------------------
void setupServer() {
  // Receive POST request from Laravel to write a new UID
  server.on("/write_rfid", HTTP_POST, []() {
    if (server.header("X-Device-API-Key") != apiKey) {
      server.send(401, "application/json", "{\"error\":\"Unauthorized\"}");
      return;
    }

    String body = server.arg("plain");
    StaticJsonDocument<200> doc;
    deserializeJson(doc, body);

    uidToWrite = doc["uid_to_write"].as<String>();
    currentJobId = doc["job_id"].as<int>();
    isWritingMode = true;

    server.send(200, "application/json", "{\"status\":\"accepted\"}");
    Serial.println("Write Job received. Waiting for card...");
  });

  server.begin();
}

// ---------------------------
// RFID Operations
// ---------------------------
String getUID() {
  String uid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    uid += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
    uid += String(mfrc522.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  return uid;
}

void sendScan(String cardUid) {
  HTTPClient http;
  String url = serverDomain + "/api/rfid/scan";
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-Device-API-Key", apiKey);
  
  String payload = "{\"card_uid\":\"" + cardUid + "\", \"device_id\":" + deviceId + "}";
  int httpCode = http.POST(payload);
  
  if (httpCode > 0) {
    String response = http.getString();
    Serial.println("Scan OK: " + response);
    // Beep or turn on Green LED here
  } else {
    Serial.println("Scan Failed");
    // Beep error here
  }
  http.end();
}

void writeCard(String currentCardUid) {
  // In a real scenario, writing block data to sector 0 to change UID is complex
  // and requires specific Chinese magic cards. 
  // If we only associate the UID with the NIS in the database, 
  // "writing" is just reading the card and sending the callback to Laravel.
  
  // Send callback
  HTTPClient http;
  String url = serverDomain + "/api/rfid/write-callback";
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-Device-API-Key", apiKey);
  
  String payload = "{\"job_id\":" + String(currentJobId) + ", \"status\":\"success\", \"card_uid\":\"" + currentCardUid + "\"}";
  int httpCode = http.POST(payload);
  
  if (httpCode > 0) {
    Serial.println("Write Callback OK");
  } else {
    Serial.println("Write Callback Failed");
  }
  http.end();

  // Reset mode
  isWritingMode = false;
  uidToWrite = "";
  currentJobId = 0;
}
