<?php
require_once '../models/AsentamientoModel.php';
require_once '../models/DispositivoModel.php';

class AsentamientoController {
    private $asentamientoModel;
    private $dispositivoModel;

    public function __construct() {
        $this->asentamientoModel = new AsentamientoModel();
        $this->dispositivoModel = new DispositivoModel();
    }

    public function obtenerAsentamientosConDispositivos($id_municipio) {
        
        $asentamientos = $this->asentamientoModel->obtenerAsentamientosPorMunicipio($id_municipio);
        
        if (empty($asentamientos)) {
            return [];
        }

        foreach ($asentamientos as &$asentamiento) {
            $dispositivos = $this->dispositivoModel->obtenerDispositivosPorAsentamiento($asentamiento['id_asentamiento']);
            
            $dispositivosPorTipo = [];
            $coordenadas = [];
            
            foreach ($dispositivos as $dispositivo) {
                $tipo = $dispositivo['tipo'];
                
                if (!isset($dispositivosPorTipo[$tipo])) {
                    $dispositivosPorTipo[$tipo] = 0;
                }
                $dispositivosPorTipo[$tipo]++;
                
                $coordenadas[] = [
                    'tipo' => $tipo,
                    'lat' => $dispositivo['latitud'],
                    'lng' => $dispositivo['longitud'],
                    'id' => $dispositivo['id_dispositivo']
                ];
            }
            
            $asentamiento['dispositivos_por_tipo'] = $dispositivosPorTipo;
            $asentamiento['total_dispositivos'] = count($dispositivos);
            $asentamiento['coordenadas'] = $coordenadas;
        }
        
        return $asentamientos;
    }

    public function contarAsentamientos() {
        return $this->asentamientoModel->contarAsentamientos();
    }
}
?>