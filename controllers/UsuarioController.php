<?php
require_once '../models/Usuario.php';

$usuario = new Usuario();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] == 'add') {
        $municipio = isset($_POST['municipio']) ? $_POST['municipio'] : null;
        $usuario->agregarUsuario($_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_POST['password'], $_POST['rol'], $municipio);
    } elseif ($_POST['action'] == 'update') {
        // Actualizar solo los campos editables
        $id = $_POST['id_usuario'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $password = $_POST['password'];

        $usuario->actualizarUsuario($id, $correo, $telefono, $password);
    } elseif ($_POST['action'] == 'delete') {
        $usuario->eliminarUsuario($_POST['id_usuario']);
    } elseif ($_POST['action'] == 'get') {
        // Obtener la información del usuario
        $user = $usuario->obtenerUsuarioPorId($_POST['id_usuario']);
        echo json_encode($user);
        exit();
    }
    header('Location: ../views/usuarios.php');
}
?>