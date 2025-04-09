<?php
require_once '../models/MunicipioModel.php';

class MunicipioController {
    private $municipioModel;

    public function __construct() {
        $this->municipioModel = new MunicipioModel();
    }

    public function obtenerMunicipios() {
        return $this->municipioModel->obtenerMunicipios();
    }

    public function obtenerMunicipiosParaMapa() {
        return $this->municipioModel->obtenerMunicipiosConCoordenadas();
    }

    public function contarMunicipios() {
        return $this->municipioModel->contarMunicipios();
    }
}
?>