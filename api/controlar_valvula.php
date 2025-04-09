<?php
require_once '../models/Database.php';
require_once '../controllers/AuthController.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$response = ['status' => 'error', 'message' => 'Invalid request'];

// Identificar si es llamada automática (ESP32)
$isAuto = strpos($_SERVER['HTTP_USER_AGENT'] ?? '', 'ESP32') !== false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (isset($data['id_dispositivo'], $data['accion'])) {
        $id_dispositivo = (int)$data['id_dispositivo'];
        $tipo = $isAuto ? 'automatica' : 'manual';
        
        if ($isAuto) {
            $id_usuario = 0; // Sistema
            $accion = $data['accion']; // "protocolo_emergencia" o "restablecer_normal"
        } else {
            session_start();
            if (!isset($_SESSION['usuario'])) {
                $response['message'] = 'No autenticado';
                echo json_encode($response);
                exit;
            }
            $id_usuario = $_SESSION['usuario']['id_usuario'];
            $accion = $data['accion'];
        }

        try {
            // Registrar acción
            $stmt = $conn->prepare("INSERT INTO acciones (id_usuario, id_dispositivo, accion, tipo_accion) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $id_usuario, $id_dispositivo, $accion, $tipo);
            
            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'message' => 'Acción registrada',
                    'data' => [
                        'id_usuario' => $id_usuario,
                        'tipo' => $tipo,
                        'accion' => $accion
                    ]
                ];
            } else {
                $response['message'] = 'Error en BD: ' . $conn->error;
            }
            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'Excepción: ' . $e->getMessage();
        }
    }
}

echo json_encode($response);
$conn->close();
?>