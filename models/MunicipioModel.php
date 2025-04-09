<?php
require_once 'Database.php';

class MunicipioModel {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function obtenerMunicipios() {
        $sql = "SELECT id_municipio, nombre FROM municipios ORDER BY nombre";
        $result = $this->connection->query($sql);
        
        $municipios = [];
        while ($row = $result->fetch_assoc()) {
            $municipios[] = $row;
        }
        return $municipios;
    }

    public function obtenerMunicipiosConCoordenadas() {
        $sql = "SELECT id_municipio, nombre, latitud, longitud FROM municipios 
                WHERE latitud IS NOT NULL AND longitud IS NOT NULL 
                ORDER BY nombre";
        $result = $this->connection->query($sql);
        
        $municipios = [];
        while ($row = $result->fetch_assoc()) {
            $municipios[] = $row;
        }
        return $municipios;
    }

    public function contarMunicipios() {
        $sql = "SELECT COUNT(*) as total FROM municipios";
        $result = $this->connection->query($sql);
        return $result->fetch_assoc()['total'];
    }
}
?>