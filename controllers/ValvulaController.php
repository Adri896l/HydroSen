<?php
require_once '../models/ValvulaModel.php';

class ValvulaController {
    private $valvulaModel;

    public function __construct() {
        $this->valvulaModel = new ValvulaModel();
    }

    public function controlarValvula($fugaId, $tipoValvula, $accion) {
        // Lógica para controlar la válvula
        $resultado = $this->valvulaModel->cambiarEstadoValvula($fugaId, $tipoValvula, $accion);
        
        if ($resultado) {
            return ['success' => true, 'message' => 'Operación realizada con éxito'];
        } else {
            return ['success' => false, 'message' => 'Error al controlar la válvula'];
        }
    }

    public function cierreAutomatico() {
        // Cerrar válvulas principales automáticamente
        $this->valvulaModel->cerrarValvulasPrincipales();
        return ['success' => true, 'message' => 'Cierre automático ejecutado'];
    }
}

// Manejo de solicitudes AJAX
if (isset($_GET['action'])) {
    $controller = new ValvulaController();
    header('Content-Type: application/json');
    
    if ($_GET['action'] == 'controlar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($controller->controlarValvula(
            $data['fuga_id'],
            $data['tipo_valvula'],
            $data['accion']
        ));
    } elseif ($_GET['action'] == 'cierreAutomatico' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode($controller->cierreAutomatico());
    }
}
?>