<?php
require_once 'Database.php';

class Usuario {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerUsuarios() {
        $query = "SELECT * FROM usuarios";
        return $this->conn->query($query);
    }

    public function agregarUsuario($nombre, $correo, $telefono, $password, $rol, $id_municipio) {
        if (strlen($password) < 8) {
            echo "La contraseña debe tener al menos 8 caracteres.";
            return false;
        }
        
        // Verificar si el correo ya existe
        $query = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "El correo ya está registrado.";
            return false;
        }
        
        // Hash de la contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Consulta para insertar el usuario
        $query = "INSERT INTO usuarios (nombre, correo, telefono, password, rol, id_municipio, fecha_creacion) 
          VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssi", $nombre, $correo, $telefono, $passwordHash, $rol, $id_municipio);
        return $stmt->execute();
    }

    public function actualizarUsuario($id, $correo, $telefono, $password) {
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE usuarios SET correo=?, telefono=?, password=? WHERE id_usuario=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssi", $correo, $telefono, $passwordHash, $id);
        } else {
            $query = "UPDATE usuarios SET correo=?, telefono=? WHERE id_usuario=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssi", $correo, $telefono, $id);
        }
    
        return $stmt->execute();
    }

    public function obtenerUsuarioPorId($id) {
        $query = "SELECT * FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    

    public function eliminarUsuario($id) {
        // Verificar si el ID es válido
        if (empty($id)) {
            return false;
        }
   
        $query = "DELETE FROM usuarios WHERE id_usuario=?";
        $stmt = $this->conn->prepare($query);
        
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $this->conn->error);
        }
   
        $stmt->bind_param("i", $id);
   
        if ($stmt->execute()) {
            return true;
        } else {
            die('Error al eliminar usuario: ' . $stmt->error);
        }
    }
   
}


?>
