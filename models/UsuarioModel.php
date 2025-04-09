<?php
require_once 'Database.php';

class UsuarioModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($correo, $password) {
        $query = "SELECT id_usuario, nombre, correo, password, rol FROM usuarios WHERE correo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            if (password_verify($password, $usuario['password'])) {
                // No retorna el password en el array
                unset($usuario['password']);
                return $usuario;
            }
        }
        return false;
    }
}
?>