<?php
require_once 'DispositivoController.php';

// Obtener el ID del asentamiento desde la solicitud POST
$id_asentamiento = $_POST['id_asentamiento'];

// Crear una instancia del controlador y obtener los dispositivos
$dispositivoController = new DispositivoController();
$dispositivos = $dispositivoController->obtenerDispositivosPorAsentamiento($id_asentamiento);

// Devolver los dispositivos en formato JSON
echo json_encode($dispositivos);
?>