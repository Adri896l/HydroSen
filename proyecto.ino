#include <WiFi.h>
#include <WebServer.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

#define SENSOR_1 26  // GPIO del Sensor 1
#define SENSOR_2 25  // GPIO del Sensor 2
#define RELAY_1 27   // GPIO del relé que controla la válvula 1
#define RELAY_2 14   // GPIO del relé que controla la válvula 2

// Configuración WiFi y servidor
const char* ssid = "OPPO Reno7";
const char* password = "11112222";
const char* serverUrl = "http://192.168.221.188/proyecto_detector_fugas2/api/recibir_datos.php";
const char* fugaUrl = "http://192.168.221.188/proyecto_detector_fugas2/api/cerrar_fuga.php";

volatile int pulseCount1 = 0;
volatile int pulseCount2 = 0;
WebServer server(80);

bool estadoValvula1 = true; // true = abierta, false = cerrada
bool estadoValvula2 = false;
bool fugaConfirmada = false;
unsigned long ultimoEnvio = 0;
unsigned long tiempoDetectadaFuga = 0;
const long intervaloEnvio = 5000; // Enviar datos cada 5 segundos
const long tiempoEsperaManual = 120000; // 2 minutos en milisegundos

// Funciones de interrupción
void IRAM_ATTR countPulse1() { pulseCount1++; }
void IRAM_ATTR countPulse2() { pulseCount2++; }

void setup() {
  Serial.begin(115200);
  
  // Configurar pines
  pinMode(SENSOR_1, INPUT_PULLUP);
  pinMode(SENSOR_2, INPUT_PULLUP);
  pinMode(RELAY_1, OUTPUT);
  pinMode(RELAY_2, OUTPUT);
  actualizarValvulas();

  // Interrupciones
  attachInterrupt(digitalPinToInterrupt(SENSOR_1), countPulse1, RISING);
  attachInterrupt(digitalPinToInterrupt(SENSOR_2), countPulse2, RISING);

  // Conexión WiFi
  conectarWiFi();

  // Rutas del servidor web local
  server.on("/datos", enviarDatos);
  server.on("/control_valvulas", HTTP_POST, controlValvulas);
  server.begin();
  Serial.println("Servidor HTTP iniciado");
}

void loop() {
  static unsigned long ultimaMedicion = 0;
  static unsigned long contadorFuga = 0;
  
  server.handleClient();

 if (fugaConfirmada && (millis() - tiempoDetectadaFuga >= tiempoEsperaManual)) {
    Serial.println("Tiempo de espera agotado. Activando cierre automático.");
    estadoValvula1 = false;  // Cerrar válvula principal
    estadoValvula2 = true;   // Abrir válvula secundaria
    actualizarValvulas();
    registrarAccion("auto_cierre");
    
    fugaConfirmada = false;
  }

  if (millis() - ultimaMedicion >= 1000) { // Medir cada segundo
    ultimaMedicion = millis();
    
    float flujo1 = pulseCount1 / 7.5; // L/min
    float flujo2 = pulseCount2 / 7.5;
    pulseCount1 = 0;
    pulseCount2 = 0;

    bool posibleFuga = flujo2 < flujo1 * 0.5;
    
    Serial.print("Flujo 1: "); Serial.print(flujo1); Serial.println(" L/min");
    Serial.print("Flujo 2: "); Serial.print(flujo2); Serial.println(" L/min");

    if (posibleFuga) {
      contadorFuga++;
      Serial.print("Posible fuga detectada. Confirmación: "); 
      Serial.println(contadorFuga);
      
      if (contadorFuga >= 5) { // Confirmar después de 5 segundos
        confirmarFuga(flujo1, flujo2);
        contadorFuga = 0;
      }
    } else {
      contadorFuga = 0;
    }

    // Enviar datos al servidor web periódicamente
    if (millis() - ultimoEnvio >= intervaloEnvio) {
      enviarDatosAlServidor(flujo1, flujo2, posibleFuga);
      ultimoEnvio = millis();
    }
  }
}

void conectarWiFi() {
  Serial.println("Conectando a WiFi...");
  WiFi.begin(ssid, password);
  
  int intentos = 0;
  while (WiFi.status() != WL_CONNECTED && intentos < 20) {
    delay(500);
    Serial.print(".");
    intentos++;
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi conectado!");
    Serial.print("IP del ESP32: "); 
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("\nError al conectar WiFi");
  }
}

void actualizarValvulas() {
  digitalWrite(RELAY_1, estadoValvula1 ? LOW : HIGH);  // LOW activa el relé
  digitalWrite(RELAY_2, estadoValvula2 ? LOW : HIGH);
  Serial.println("Estado válvulas actualizado:");
  Serial.print("Válvula 1: "); Serial.println(estadoValvula1 ? "Abierta" : "Cerrada");
  Serial.print("Válvula 2: "); Serial.println(estadoValvula2 ? "Abierta" : "Cerrada");
}

void confirmarFuga(float flujo1, float flujo2) {
  Serial.println("Fuga confirmada! Activando protocolo de emergencia...");
  fugaConfirmada = true;
  tiempoDetectadaFuga = millis();
  
  // Notificar al servidor web
  notificarFugaAlServidor(flujo1, flujo2);
  
  // Enviar notificación
  enviarNotificacionFuga();
}

void enviarDatosAlServidor(float flujo1, float flujo2, bool alerta) {
  if (isnan(flujo1)) flujo1 = 0;
  if (isnan(flujo2)) flujo2 = 0;
  
  HTTPClient http;
  String url = String(serverUrl) + 
    "?flujo1=" + String(flujo1) + 
    "&flujo2=" + String(flujo2) + 
    "&alerta_fuga=" + (alerta ? "true" : "false");
  
  http.begin(url);
  http.setTimeout(5000);
  
  int httpCode = http.GET();
  String payload = http.getString();
  
  if (httpCode == HTTP_CODE_OK) {
    Serial.println("Datos enviados correctamente");
  } else {
    Serial.print("Error HTTP: ");
    Serial.println(httpCode);
  }
  http.end();
}

void notificarFugaAlServidor(float flujo1, float flujo2) {
  if (WiFi.status() != WL_CONNECTED) {
    conectarWiFi();
    if (WiFi.status() != WL_CONNECTED) return;
  }

  HTTPClient http;
  http.begin(fugaUrl);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
  String postData = "id_dispositivo=1&flujo1=" + String(flujo1) + 
                   "&flujo2=" + String(flujo2);
  
  int httpCode = http.POST(postData);
  http.end();
}

void controlValvulas() {
  String body = server.arg("plain");
  Serial.println("Comando recibido: " + body);
  
  DynamicJsonDocument doc(256);
  DeserializationError error = deserializeJson(doc, body);
  
  if (error) {
    server.send(400, "application/json", "{\"status\":\"error\",\"message\":\"JSON inválido\"}");
    return;
  }
  
  String accion = doc["accion"];
  
  if (accion == "cerrar") {
    estadoValvula1 = false;
    estadoValvula2 = true;
    fugaConfirmada = false; // Cancelar cierre automático
    registrarAccion("manual_cerrar");
    server.send(200, "application/json", "{\"status\":\"ok\",\"message\":\"Válvula principal cerrada\"}");
  } 
  else if (accion == "abrir") {
    estadoValvula1 = true;
    estadoValvula2 = false;
    registrarAccion("abrir");
    server.send(200, "application/json", "{\"status\":\"ok\",\"message\":\"Válvula principal abierta\"}");
  }
  else {
    server.send(400, "application/json", "{\"status\":\"error\",\"message\":\"Acción no válida\"}");
    return;
  }
  
  actualizarValvulas();
}

void registrarAccion(const String& tipoAccion) {
  if (WiFi.status() != WL_CONNECTED) return;
  
  HTTPClient http;
  http.begin("http://192.168.221.188/proyecto_detector_fugas2/api/registrar_accion.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
  String postData = "id_dispositivo=1&accion=" + tipoAccion + "&tipo=automatica";
  http.POST(postData);
  http.end();
}

void enviarDatos() {
  float flujo1 = pulseCount1 / 7.5;
  float flujo2 = pulseCount2 / 7.5;
  bool alerta = flujo2 < flujo1 * 0.5;

  DynamicJsonDocument doc(256);
  doc["flujo1"] = flujo1;
  doc["flujo2"] = flujo2;
  doc["alerta_fuga"] = alerta;
  doc["valvula1"] = estadoValvula1;
  doc["valvula2"] = estadoValvula2;
  doc["fuga_confirmada"] = fugaConfirmada;
  if (fugaConfirmada) {
    doc["tiempo_restante"] = (tiempoEsperaManual - (millis() - tiempoDetectadaFuga)) / 1000;
  }

  String json;
  serializeJson(doc, json);
  server.send(200, "application/json", json);
}

void enviarNotificacionFuga() {
  Serial.println("¡ALERTA! Notificando fuga detectada");
}