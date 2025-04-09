<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Database.php';

require_once 'AsentamientoController.php';

$id_municipio = isset($_POST['id_municipio']) ? $_POST['id_municipio'] : null;

if ($id_municipio) {

    $database = new Database();
    $mysqli = $database->getConnection();

    $asentamientoController = new AsentamientoController($mysqli);
    $asentamientos = $asentamientoController->obtenerAsentamientosPorMunicipio($id_municipio);

    header('Content-Type: application/json');

    
    echo json_encode($asentamientos);
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>