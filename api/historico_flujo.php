<?php
require_once '../models/Database.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

$db = new Database();
$conn = $db->getConnection();

// conexión
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Error de conexión: ' . $conn->connect_error]));
}

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

$query = "SELECT timestamp, flujo_medido, estado FROM fugas ORDER BY timestamp DESC LIMIT ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => 'Error en preparación: ' . $conn->error]));
}

$stmt->bind_param("i", $limit);
$stmt->execute();
$result = $stmt->get_result();

// Verifica si hay resultados
if ($result->num_rows === 0) {
    echo json_encode([
        'status' => 'success',
        'message' => 'No hay datos disponibles',
        'data' => []
    ]);
    exit();
}

$data = [];
while ($row = $result->fetch_assoc()) {
    // Asegura que los campos necesarios existen
    $data[] = [
        'timestamp' => $row['timestamp'],
        'flujo_medido' => floatval($row['flujo_medido']),
        'estado' => $row['estado'] ?? 'Normal' // Valor por defecto si no existe
    ];
}

echo json_encode([
    'status' => 'success',
    'data' => $data
]);

$stmt->close();
$conn->close();
?>