<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
require_once '../models/Database.php';
$database = new Database();
$connection = $database->getConnection();

// Obtener usuarios
$query = "SELECT id_usuario, nombre, correo, telefono, rol, id_municipio, fecha_creacion FROM usuarios";
$result = $connection->query($query);
$usuarios = [];

while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

// Obtener municipios
$queryMunicipios = "SELECT id_municipio, nombre FROM Municipios";
$resultMunicipios = $connection->query($queryMunicipios);
$municipios = [];

while ($row = $resultMunicipios->fetch_assoc()) {
    $municipios[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin-Usuarios</title>
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/logo-03.png" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .table-container {
            margin: 20px auto;
            max-width: 90%; 
            padding-left: 15px;
            padding-right: 15px;
        }

        .table {
            width: 100%; 
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>

    <div id="wrapper">
         <!-- Menu lateral -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="perfil1.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <img src="images/logo-03.png" alt="Hydrosen Logo" style="width: 40px;">
                </div>
                <div class="sidebar-brand-text mx-3">Hydrosen</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="perfil.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="usuarios.php">
                <i class="fas fa-users"></i>
                    <span>Usuario</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dispositivos.php">
                <i class="fas fa-map"></i>
                    <span>Dispositivos</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="control_fugas.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Control de fugas</span></a>
            </li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                     <!-- Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- notificaciones -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="../controllers/AuthController.php?action=logout" id="userDropdown" role="button">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="main-content">
                <div class="table-container">
                    <h1>Usuarios</h1>
    
                    <div class="mb-3">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuario...">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus"></i> Agregar Usuario
                        </button>

                        <!-- Tabla de usuarios -->
                   
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Telefono</th>
                                    <th>Rol</th>
                                    <th>Municipio</th>
                                    <th>Fecha de Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id_usuario']; ?></td>
                                <td><?php echo $usuario['nombre']; ?></td>
                                <td><?php echo $usuario['correo']; ?></td>
                                <td><?php echo $usuario['telefono']; ?></td>
                                <td><?php echo $usuario['rol']; ?></td>
                                <td data-municipio-id="<?php echo $usuario['id_municipio']; ?>"><?php echo $usuario['id_municipio']; ?></td> 
                                <td><?php echo $usuario['fecha_creacion']; ?></td>
                                <td class="text-center">
                                    <button onclick="openEditModal(<?php echo $usuario['id_usuario']; ?>)" class="btn btn-warning btn-sm me-2" style="color: white;">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <form method="POST" action="../controllers/UsuarioController.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?')" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <!-- Modal para editar usuario -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true" style="color: black;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editUserForm">
                            <input type="hidden" id="edit_id_usuario" name="id_usuario">
                            <div class="mb-3">
                                <label for="edit_nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="edit_nombre" name="nombre" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="edit_correo" class="form-label">Correo:</label>
                                <input type="email" class="form-control" id="edit_correo" name="correo" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_telefono" class="form-label">Teléfono:</label>
                                <input type="text" class="form-control" id="edit_telefono" name="telefono" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_password" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="edit_password" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="edit_rol" class="form-label">Rol:</label>
                                <select class="form-select" id="edit_rol" name="rol" disabled>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_municipio" class="form-label">Municipio:</label>
                                <select class="form-select" id="edit_municipio" name="municipio" disabled>
                                    <?php foreach ($municipios as $municipio): ?>
                                        <option value="<?php echo $municipio['id_municipio']; ?>"><?php echo $municipio['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para agregar usuario -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true" style="color: black;">
           <div class="modal-dialog">
             <div class="modal-content">
                    <div class="modal-header">
                       <h5 class="modal-title" id="addUserModalLabel">Agregar Usuario</h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                            <form method="POST" action="../controllers/UsuarioController.php">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="correo" class="form-label">Correo</label>
                                        <input type="email" class="form-control" id="correo" name="correo" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Telefono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rol" class="form-label">Rol</label>
                                        <select class="form-select" id="rol" name="rol" required>
                                            <option value="admin">Admin</option>
                                            <option value="empleado">Empleado</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                    <label for="municipio" class="form-label">Municipio</label>
                                    <select class="form-select" id="municipio" name="municipio">
                                        <option value="" disabled selected>Seleccionar municipio</option> 
                                        <?php foreach ($municipios as $municipio): ?>
                                            <option value="<?php echo $municipio['id_municipio']; ?>"><?php echo $municipio['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                             <input type="hidden" name="action" value="add">
                          </form>
                        </div>
                    </div>
                </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Hydrosen 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function openEditModal(id) {
            fetch('../controllers/UsuarioController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get&id_usuario=' + id
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id_usuario').value = data.id_usuario;
                document.getElementById('edit_nombre').value = data.nombre;
                document.getElementById('edit_correo').value = data.correo;
                document.getElementById('edit_telefono').value = data.telefono;

                const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editModal.show();
            })
            .catch(error => {
                console.error('Error al obtener los datos del usuario:', error);
            });
        }

        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update');

            fetch('../controllers/UsuarioController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload(); 
                }
            })
            .catch(error => {
                console.error('Error al actualizar el usuario:', error);
            });
        });
    </script>
</body>
</html>