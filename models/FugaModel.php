<?php
require_once 'Database.php';

class FugaModel {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function obtenerFugas() {
        $query = "SELECT f.*, a.nombre as nombre_asentamiento, m.nombre as nombre_municipio 
                 FROM fugas f
                 JOIN asentamientos a ON f.id_asentamiento = a.id_asentamiento
                 JOIN municipios m ON a.id_municipio = m.id_municipio
                 ORDER BY f.timestamp ASC";
        $result = $this->connection->query($query);
        $fugas = [];

        while ($row = $result->fetch_assoc()) {
            $fugas[] = $row;
        }

        return $fugas;
    }

    public function obtenerFugasActivas() {
        $query = "SELECT f.*, a.nombre as nombre_asentamiento, m.nombre as nombre_municipio 
                 FROM fugas f
                 JOIN asentamientos a ON f.id_asentamiento = a.id_asentamiento
                 JOIN municipios m ON a.id_municipio = m.id_municipio
                 WHERE f.estado = 'Fuga Detectada'
                 ORDER BY CASE f.gravedad 
                    WHEN 'grave' THEN 1
                    WHEN 'moderada' THEN 2
                    WHEN 'leve' THEN 3
                 END, f.timestamp DESC";
        
        $result = $this->connection->query($query);
        $fugas = [];
        
        while ($row = $result->fetch_assoc()) {
            $fugas[] = $row;
        }
        
        return $fugas;
    }

    // Método para contar fugas críticas
    public function contarFugasGraves() {
        $query = "SELECT COUNT(*) as count FROM fugas 
                 WHERE estado = 'Fuga Detectada' AND gravedad = 'grave'";
        
        $result = $this->connection->query($query);
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }

    // Método para registrar acciones
    public function registrarAccion($id_usuario, $id_dispositivo, $accion) {
        $stmt = $this->connection->prepare("INSERT INTO acciones (id_usuario, id_dispositivo, accion) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_usuario, $id_dispositivo, $accion);
        return $stmt->execute();
    }
}
?>
