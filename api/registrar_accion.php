<?php
require_once '../models/Database.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$response = ['status' => 'error', 'message' => 'Invalid request'];

// Obtener datos de la solicitud
$input = file_get_contents('php://input');
parse_str($input, $data);

if (isset($data['id_dispositivo'], $data['accion'])) {
    $id_dispositivo = (int)$data['id_dispositivo'];
    $accion = $data['accion'];
    $tipo = isset($data['tipo']) ? $data['tipo'] : 'automatica';
    
    try {
        // Registrar acción
        $stmt = $conn->prepare("INSERT INTO acciones (id_dispositivo, accion, tipo_accion) 
                              VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_dispositivo, $accion, $tipo);
        
        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Acción registrada',
                'data' => [
                    'tipo' => $tipo,
                    'accion' => $accion
                ]
            ];
            
            // Si es un cierre automático, actualizar tabla fugas
            if ($accion === 'auto_cierre') {
                $update = $conn->prepare("UPDATE fugas SET 
                                        accion_realizada = 'protocolo_emergencia',
                                        gravedad = 'grave'
                                        WHERE id_dispositivo = ? 
                                        AND accion_realizada IS NULL
                                        ORDER BY timestamp DESC LIMIT 1");
                $update->bind_param("i", $id_dispositivo);
                $update->execute();
                $update->close();
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);
$conn->close();
?>