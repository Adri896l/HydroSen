<?php
require_once '../models/Database.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

// Obtener último estado de fuga
$query = "SELECT * FROM fugas ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($query);
$fuga = $result->fetch_assoc();

// Obtener estado de válvulas 
$queryValvulas = "SELECT 
    MAX(CASE WHEN accion = 'restablecer_normal' THEN timestamp END) as ultimo_restablecimiento,
    MAX(CASE WHEN accion = 'protocolo_emergencia' THEN timestamp END) as ultima_emergencia
FROM acciones";
$resultValvulas = $conn->query($queryValvulas);
$valvulas = $resultValvulas->fetch_assoc();

echo json_encode([
    'status' => 'success',
    'data' => [
        'fuga' => $fuga,
        'valvulas' => [
            'valvula1' => ($valvulas['ultima_emergencia'] > $valvulas['ultimo_restablecimiento']) ? 'cerrada' : 'abierta',
            'valvula2' => ($valvulas['ultima_emergencia'] > $valvulas['ultimo_restablecimiento']) ? 'abierta' : 'cerrada'
        ]
    ]
]);
?>