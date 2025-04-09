<?php
require_once '../models/Database.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['id_dispositivo'], $input['accion'])) {
        $id_dispositivo = (int)$input['id_dispositivo'];
        $accion = $input['accion'];
        
        $accion_db = 'cerrar'; 
        if ($accion === 'abrir') {
            $accion_db = 'abrir';
        } elseif ($accion === 'auto_cerrar') {
            $accion_db = 'cerrar';
        }
        
        $stmt = $conn->prepare("INSERT INTO acciones (id_usuario, id_dispositivo, accion, tipo_accion) 
                               VALUES (0, ?, ?, ?)");
        $tipo_accion = ($accion === 'manual_cerrar' || $accion === 'abrir') ? 'manual' : 'automatica';
        $stmt->bind_param("iss", $id_dispositivo, $accion_db, $tipo_accion);
        
        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Acción registrada',
                'data' => [
                    'id_dispositivo' => $id_dispositivo,
                    'accion' => $accion_db,
                    'tipo' => $tipo_accion
                ]
            ];
        } else {
            $response['message'] = 'Error en la base de datos: ' . $conn->error;
        }
        $stmt->close();
    } else {
        $response['message'] = 'Parámetros faltantes';
    }
}

echo json_encode($response);
$conn->close();
?>