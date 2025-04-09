<?php
require_once '../models/Database.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['flujo1'], $_GET['flujo2'], $_GET['alerta_fuga'])) {
        // Validación y conversión segura de parámetros
        $flujo1 = max(0, floatval($_GET['flujo1'])); // Asegura valor no negativo
        $flujo2 = max(0, floatval($_GET['flujo2']));
        $alerta = $_GET['alerta_fuga'] == "true";
        
        // Cálculo seguro de diferencia y porcentaje
        $diferencia = $flujo1 - $flujo2;
        $porcentaje = ($flujo1 > 0) ? ($diferencia / $flujo1) * 100 : 0;
        
        // Determinación de gravedad
        $gravedad = 'leve';
        if ($porcentaje > 30) $gravedad = 'moderada';
        if ($porcentaje > 60) $gravedad = 'grave';
        
        try {
            $stmt = $conn->prepare("INSERT INTO fugas (id_dispositivo, id_asentamiento, flujo_medido, estado, gravedad) 
                                   VALUES (1, 1, ?, ?, ?)");
            $estado = $alerta ? 'Fuga Detectada' : 'Normal';
            $stmt->bind_param("dss", $flujo1, $estado, $gravedad);
            
            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'data' => [
                        'flujo1' => $flujo1,
                        'flujo2' => $flujo2,
                        'alerta' => $alerta,
                        'gravedad' => $gravedad,
                        'diferencia' => $diferencia,
                        'porcentaje' => $porcentaje
                    ]
                ];
                
                if ($alerta) {
                    $conn->query("INSERT INTO acciones (id_usuario, id_dispositivo, accion) 
                                 VALUES (0, 1, 'cerrar')");
                }
            } else {
                $response['message'] = 'Database error: ' . $conn->error;
            }
            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'Exception: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Missing parameters';
    }
}

echo json_encode($response);
$conn->close();
?>