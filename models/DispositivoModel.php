<?php
require_once 'Database.php';

class DispositivoModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerDispositivos() {
        $query = "SELECT * FROM dispositivos";
        return $this->conn->query($query);
    }

    public function obtenerDispositivosPorAsentamiento($id_asentamiento) {
        $sql = "SELECT id_dispositivo, tipo, latitud, longitud 
                FROM dispositivos 
                WHERE id_asentamiento = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_asentamiento);
        $stmt->execute();
        $result = $stmt->get_result();

        $dispositivos = [];
        while ($row = $result->fetch_assoc()) {
            $dispositivos[] = $row;
        }

        return $dispositivos;
    }
    

    public function agregarDispositivo($nombre, $tipo, $modelo, $estado, $id_asentamiento, $coordenadas) {
        $query = "INSERT INTO dispositivos (tipo, modelo, estado, id_asentamiento, latitud, longitud, fecha_instalacion) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssiss", $tipo, $modelo, $estado, $id_asentamiento, $latitud, $longitud);
        return $stmt->execute();
    }

    public function actualizarDispositivo($id, $nombre, $tipo, $modelo, $estado, $id_asentamiento, $coordenadas) {
        $query = "UPDATE dispositivos SET tipo=?, modelo=?, estado=?, id_asentamiento=? WHERE id_dispositivo=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssis", $tipo, $modelo, $estado, $id_asentamiento, $latitud, $longitud, $id);
        return $stmt->execute();
    }

    public function obtenerDispositivoPorId($id) {
        $query = "SELECT * FROM dispositivos WHERE id_dispositivo = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function eliminarDispositivo($id) {
        if (empty($id)) {
            return false;
        }

        $query = "DELETE FROM dispositivos WHERE id_dispositivo=?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $this->conn->error);
        }
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        } else {
            die('Error al eliminar dispositivo: ' . $stmt->error);
        }
    }

}
?>
