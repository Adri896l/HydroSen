<?php
require_once '../models/UsuarioModel.php';
require_once '../models/Database.php';

class AuthController {
    public function __construct() {
        session_start();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = $_POST['correo'];
            $password = $_POST['password'];
    
            $database = new Database();
            $db = $database->getConnection();
            $usuarioModel = new UsuarioModel($db);
            $usuario = $usuarioModel->login($correo, $password);
    
            if ($usuario) {
                $_SESSION['usuario'] = [
                    'id_usuario' => $usuario['id_usuario'],
                    'nombre' => $usuario['nombre'],
                    'correo' => $usuario['correo'],
                    'rol' => $usuario['rol']
                ];
                header('Location: ../views/perfil.php');
                exit();
            } else {
                $_SESSION['error'] = "Credenciales incorrectas";
                header('Location: ../views/login.php');
                exit();
            }
        }
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        header("Location: ../views/login.php");
        exit();
    }

    public static function checkAuth() {
        session_start();
        if (!isset($_SESSION['usuario'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
                exit;
            } else {
                header("Location: /proyecto_detector_fugas2/views/login.php");
                exit;
            }
        }
        return $_SESSION['usuario'];
    }
}

// Manejo de acciones
if (isset($_GET['action'])) {
    $authController = new AuthController();
    $action = $_GET['action'];
    
    if (method_exists($authController, $action)) {
        $authController->$action();
    } else {
        die("Error: Acción no válida.");
    }
}
?>