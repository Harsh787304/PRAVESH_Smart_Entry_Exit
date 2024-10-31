#include <WiFiS3.h>
#include <ArduinoHttpClient.h>
#include <SPI.h>
#include <MFRC522.h>

// Define constants
#define RED_LED_PIN 1
#define GREEN_LED_PIN 2
#define BLUE_LED_PIN 3
#define BUZZER_PIN 5 // Define the buzzer pin

String URL = "http://192.168.143.232/dht11_project/test_data.php";
const char* ssid = "kk"; // Your WiFi network name
const char* password = "12345678"; // Your WiFi password

#define RST_PIN 9
#define SS_PIN 10

MFRC522 rfid(SS_PIN, RST_PIN); // Create MFRC522 instance
WiFiClient wifiClient; // Create WiFiClient instance
HttpClient client = HttpClient(wifiClient, "192.168.143.232", 80); // Use IP and port for the server
// const SPISettings spiSettings = SPISettings(AGT_CLOCK_P403, MSBFIRST, SPI_MODE0);
void setup() {
  Serial.begin(115200);
  SPI.begin();    
  // SPI.beginTransaction(spiSettings);    // Initialize SPI bus
  rfid.PCD_Init();    // Initialize MFRC522
  Serial.println("RFID Reader initialized.");
  // SPI.endTransaction();

  // Initialize LED pins
  pinMode(RED_LED_PIN, OUTPUT);
  pinMode(GREEN_LED_PIN, OUTPUT);
  pinMode(BLUE_LED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT); // Initialize buzzer pin

  // Initialize LEDs to off
  digitalWrite(RED_LED_PIN, HIGH);
  digitalWrite(GREEN_LED_PIN, LOW);
  digitalWrite(BLUE_LED_PIN, LOW);
  digitalWrite(BUZZER_PIN, LOW); // Initialize buzzer to off

  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi...");

  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }

  Serial.println("\nConnected to WiFi!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    // If not connected to Wi-Fi, turn on red LED
    digitalWrite(RED_LED_PIN, HIGH);
    digitalWrite(GREEN_LED_PIN, LOW);  // Ensure green LED is off
    digitalWrite(BLUE_LED_PIN, LOW);   // Ensure blue LED is off
    return;
  } else {
    // If connected to Wi-Fi, turn on green LED
    digitalWrite(RED_LED_PIN, LOW);    // Ensure red LED is off
    digitalWrite(GREEN_LED_PIN, HIGH); // Turn on green LED
    digitalWrite(BLUE_LED_PIN, LOW);   // Ensure blue LED is off
  }

  // Look for new RFID tags
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    return;
  }

  // Read UID
  String uid = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) {
      uid += "0"; // Add leading zero if needed
    }
    uid += String(rfid.uid.uidByte[i], HEX);
  }

  // Print the UID
  Serial.print("UID tag: ");
  Serial.println(uid);

  // Turn on blue LED and buzzer when tag is read
  digitalWrite(RED_LED_PIN, LOW);   // Ensure red LED is off
  digitalWrite(GREEN_LED_PIN, LOW); // Ensure green LED is off
  digitalWrite(BLUE_LED_PIN, HIGH); // Turn on blue LED
  digitalWrite(BUZZER_PIN, HIGH);   // Turn on buzzer

  // Send UID to server
  String postData = "uid=" + uid;
  client.beginRequest();
  client.post("/dht11_project/test_data.php");
  client.sendHeader("Content-Type", "application/x-www-form-urlencoded");
  client.sendHeader("Content-Length", postData.length());
  client.beginBody();
  client.print(postData);
  client.endRequest();

  int statusCode = client.responseStatusCode();
  String response = client.responseBody();

  Serial.print("Status code: ");
  Serial.println(statusCode);
  Serial.print("Response: ");
  Serial.println(response);
  Serial.println("------------------------------------------");

  // After sending UID, turn off blue LED and buzzer, turn on green LED
  digitalWrite(BLUE_LED_PIN, LOW);
  digitalWrite(GREEN_LED_PIN, HIGH);
  digitalWrite(BUZZER_PIN, LOW);  // Turn off buzzer

  // Halt PICC
  rfid.PICC_HaltA();
  // Stop encryption on PCD
  rfid.PCD_StopCrypto1();

  delay(5000); // Short delay to prevent rapid switching
}
