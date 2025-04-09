<?php
require_once 'Database.php';

class AsentamientoModel {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function obtenerAsentamientosPorMunicipio($id_municipio) {
        $sql = "SELECT id_asentamiento, nombre FROM asentamientos WHERE id_municipio = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('i', $id_municipio);
        $stmt->execute();
        $result = $stmt->get_result();

        $asentamientos = [];
        while ($row = $result->fetch_assoc()) {
            $asentamientos[] = $row;
        }

        return $asentamientos;
    }

    public function contarAsentamientos() {
        $sql = "SELECT COUNT(*) as total FROM asentamientos";
        $result = $this->connection->query($sql);
        return $result->fetch_assoc()['total'];
    }

    
}
?>