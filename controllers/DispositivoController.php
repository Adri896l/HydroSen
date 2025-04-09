<?php
require_once '../models/DispositivoModel.php';

class DispositivoController {
    private $dispositivoModel;

    public function __construct() {
        $this->dispositivoModel = new DispositivoModel();
    }

    public function obtenerDispositivosPorAsentamiento($id_asentamiento) {
        return $this->dispositivoModel->obtenerDispositivosPorAsentamiento($id_asentamiento);
    }
}
?>
