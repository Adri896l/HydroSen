<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header('Location: perfil.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style_login.css">
    <link rel="shortcut icon" href="views/images/logo-03.png" type="image/x-icon">
</head>
<body>
    <header id="header">
        <div class="container__header">
            <div class="logo"></div>
            <div class="container__nav">
                <nav id="nav">
                    <ul></ul>
                </nav>
                <div class="btn__menu" id="btn_menu"><i class="fas fa-bars"></i></div>
            </div>
        </div>
    </header>
    <div class="login-container">
        <form action="../controllers/AuthController.php?action=login" method="POST">
            <h3>Iniciar Sesión</h3>
            <div class="mb-3">
                <label for="correo">Correo</label><br>
                <input type="email" id="correo" name="correo" class="form-control" placeholder="Ingresa tu correo" required>
            </div>
            <label for="contrasena">Contraseña</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                <span class="toggle-password" onclick="togglePassword('password')"></span>
            </div>
            <div class="register-message">
                <p>¿No tienes cuenta? <a href="register.html">Regístrate aquí</a></p>
            </div>
            <input type="submit" class="btn-1" value="Iniciar sesión">
        </form>
    </div>
    <script>
        function togglePassword(id) {
            var passwordField = document.getElementById(id);
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
    <script>
        const btnMenu = document.getElementById('btn_menu');
        const nav = document.getElementById('nav');

        btnMenu.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    </script>
</body>
</html>