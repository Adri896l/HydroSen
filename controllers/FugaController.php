<?php
require_once '../models/FugaModel.php';

class FugaController {
    private $fugaModel;

    public function __construct() {
        $this->fugaModel = new FugaModel();
    }

    public function obtenerFugas() {
        return $this->fugaModel->obtenerFugas();
    }

    public function obtenerFugasActivas() {
        return $this->fugaModel->obtenerFugasActivas();
    }

    public function contarFugasGraves() {
        return $this->fugaModel->contarFugasGraves();
    }

    public function registrarAccion($id_usuario, $id_dispositivo, $accion) {
        return $this->fugaModel->registrarAccion($id_usuario, $id_dispositivo, $accion);
    }

    // Método para manejar solicitudes AJAX
    public function handleRequest() {
        if (isset($_GET['action'])) {
            header('Content-Type: application/json');
            
            switch ($_GET['action']) {
                case 'checkFugasGraves':
                    echo json_encode(['fugas_graves' => $this->contarFugasGraves()]);
                    break;
                case 'registrarAccion':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $result = $this->registrarAccion(
                        $_SESSION['usuario']['id'],
                        $data['id_dispositivo'],
                        $data['accion']
                    );
                    echo json_encode(['success' => $result]);
                    break;
            }
            exit();
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new FugaController();
    $controller->handleRequest();
}
?>