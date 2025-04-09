<?php
require_once 'Database.php';

class ValvulaModel {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function cambiarEstadoValvula($fugaId, $tipoValvula, $accion) {
        $estado = ($accion == 'abrir') ? 1 : 0;
        $tipo = ($tipoValvula == 'principal') ? 'principal' : 'secundaria';
        
        $sql = "UPDATE valvulas 
                SET estado = ?, ultima_accion = NOW() 
                WHERE fuga_id = ? AND tipo = ?";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("iis", $estado, $fugaId, $tipo);
        
        return $stmt->execute();
    }

    public function cerrarValvulasPrincipales() {
        $sql = "UPDATE valvulas v
                JOIN fugas f ON v.fuga_id = f.id
                SET v.estado = 0, v.ultima_accion = NOW()
                WHERE v.tipo = 'principal' AND f.prioridad = 'alta'";
        
        return $this->connection->query($sql);
    }
}
?>