# 🌊 **HydroSen** 🚰

## 💡 Descripción

**HydroSen** es un prototipo de **sistema domótico** que detecta fugas de agua en tiempo real en la comunidad del Estado de México. Su objetivo es **mitigar el desperdicio de agua** y prevenir cortes de suministro debido a la falta de detección temprana de fugas. Este sistema **automatizado** utiliza sensores de flujo, microcontroladores ESP32 y válvulas solenoides, con una interfaz web para monitoreo y notificación en tiempo real.

## 🛠️ Tecnologías Utilizadas

- **API REST**: Comunicación entre el ESP32, la plataforma web y la base de datos.
- **PHP**: Backend de la aplicación web.
- **CSS, HTML, JS, Bootstrap**: Diseño y desarrollo de la interfaz web.
- **ESP32**: Microcontrolador con capacidad Wi-Fi para procesar datos y conectarse a la web.
- **Arduino IDE**: Programación del ESP32 y control de sensores/actuadores.
- **Sensor de Flujo YF-S201**: Mide el caudal de agua en la tubería.
- **Electroválvula Solenoide 12V (1/2 pulgada)**: Controla el flujo de agua en las tuberías.

## 🎯 Objetivo

El objetivo principal de **HydroSen** es **detectar fugas de agua en tiempo real**, redirigiendo el flujo de agua automáticamente para evitar el desperdicio y asegurar un **suministro continuo**. Este sistema ayuda a optimizar la gestión del agua y a garantizar el suministro en comunidades vulnerables a cortes por fugas no detectadas.

## ⚙️ Funcionamiento del Sistema

### 1. 🔍 **Monitoreo del Flujo de Agua**
- Se instalan **sensores de flujo YF-S201** en la tubería principal para medir el caudal de agua.
- El **ESP32** procesa estos datos y los envía a un servidor a través de Wi-Fi.
- Se establecen **valores de referencia** para detectar **variaciones anómalas** en el flujo.

### 2. 🚨 **Detección de Fuga de Agua**
- Si el sensor detecta una **disminución repentina** en el caudal, el **ESP32** activa una alerta.
- Un algoritmo verifica la **continuidad del flujo** para evitar falsas alarmas.
- Si la anomalía persiste, el sistema **cierra automáticamente** la válvula solenoide de la tubería principal.

### 3. 🔄 **Cierre de la Tubería Principal y Apertura de la Secundaria**
- La **válvula solenoide** de la tubería principal se cierra automáticamente para evitar el desperdicio de agua.
- Se **abre una válvula** en la tubería secundaria, permitiendo el flujo desde una línea alternativa, garantizando el suministro continuo.

### 4. 📱 **Notificación al Usuario**
- El sistema envía **notificaciones en tiempo real** a la aplicación web indicando:
  - Existencia de la fuga.
  - Sector afectado dentro de la red hídrica.
  - Estado de las válvulas (principal cerrada, secundaria abierta).
- Se **almacena un historial de incidentes** en la base de datos.

### 5. 🔧 **Monitoreo y Reparación de la Fuga**
- El usuario recibe la alerta y puede verificar el estado del suministro de agua desde la plataforma web.
- Una vez reparada la fuga, el usuario puede **reactivar la tubería principal** desde la aplicación:
  - La válvula de la tubería secundaria se **cierra**.
  - La válvula de la tubería principal se **abre** para restablecer el flujo de agua.

## 📸 Capturas de Pantalla

Home
![image](https://github.com/user-attachments/assets/3d5b7b30-d59a-42b8-bb51-0bd8ceb8739c)

Inicio Sesion
![image](https://github.com/user-attachments/assets/0f260969-e619-4ce7-8747-7ab1f7681377)

Perfil
![image](https://github.com/user-attachments/assets/34b57e44-f3ca-4c80-a629-107dcb4103c3)

Busqueda de dispositivos:
![image](https://github.com/user-attachments/assets/2c094a0e-c315-48ab-a724-6b906cda4889)
![image](https://github.com/user-attachments/assets/ae3d956a-4176-4e1d-bf00-fff722544bbd)

Sección de Control:
![image](https://github.com/user-attachments/assets/9355bc97-b87b-4893-a877-3579a66ec278)
![image](https://github.com/user-attachments/assets/95f67b1d-3567-4974-b7f4-d8f5192fadee)


## 🚀 Instrucciones de Instalación

Sigue estos pasos para instalar y ejecutar el proyecto:

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/usuario/HydroSen.git
