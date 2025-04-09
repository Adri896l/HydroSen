<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

require_once '../controllers/MunicipioController.php';
require_once '../controllers/AsentamientoController.php';

// Instanciar controladores
$municipioController = new MunicipioController();
$asentamientoController = new AsentamientoController();

// Obtener municipios
$municipios = $municipioController->obtenerMunicipios();

// Procesar selección de municipio
$asentamientos = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_municipio'])) {
    $id_municipio = $_POST['id_municipio'];
    $asentamientos = $asentamientoController->obtenerAsentamientosConDispositivos($id_municipio);
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

    <title>Admin-Asentamientos</title>
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/logo-03.png" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        
        .select-municipio {
            max-width: 300px;
            margin-bottom: 20px;
        }
    /* Estilos para el mapa */
    #map {
        height: 500px;
        width: 100%;
        border-radius: 0.25rem;
    }
    
    /* Ajustes para el modal */
    .modal-lg {
        max-width: 90%;
    }
    
    /* Estilos para los marcadores */
    .leaflet-popup-content {
        font-size: 14px;
    }
    
    .leaflet-popup-content b {
        color: #2c3e50;
    }
    
    @media (max-width: 768px) {
        .modal-lg {
            max-width: 95%;
            margin: 0.5rem auto;
        }
        
        #map {
            height: 400px;
        }
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
                        <h1>Asentamientos por Municipio</h1>
                        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label for="id_municipio" class="form-label">Seleccione un municipio:</label>
                    <select class="form-select" id="id_municipio" name="id_municipio" required onchange="this.form.submit()">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($municipios as $municipio): ?>
                            <option value="<?= $municipio['id_municipio'] ?>" 
                                <?= isset($_POST['id_municipio']) && $_POST['id_municipio'] == $municipio['id_municipio'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($municipio['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <?php if (!empty($asentamientos)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Asentamiento</th>
                            <th>Dispositivos</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asentamientos as $asentamiento): ?>
                            <tr>
                                <td><?= htmlspecialchars($asentamiento['nombre']) ?></td>
                                <td>
                                    <?php foreach ($asentamiento['dispositivos_por_tipo'] as $tipo => $cantidad): ?>
                                        <span class="badge bg-info badge-tipo">
                                            <?= ucfirst(str_replace('_', ' ', $tipo)) ?>: <?= $cantidad ?>
                                        </span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">
                                        <?= $asentamiento['total_dispositivos'] ?>
                                    </span>
                                </td>
                                <td>
    <button class="btn btn-sm btn-success btn-ver-mapa" 
        data-asentamiento='<?= htmlspecialchars(json_encode([
            'nombre' => $asentamiento['nombre'],
            'coordenadas' => $asentamiento['coordenadas']
        ]), ENT_QUOTES, 'UTF-8') ?>'>
        <i class="fas fa-map-marked-alt"></i> Ver Mapa
    </button>
</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-info">
                No se encontraron asentamientos con dispositivos para este municipio.
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para el mapa -->
<div class="modal fade" id="mapaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="mapaModalLabel">Ubicación de Dispositivos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 500px;">
                <div id="map"></div>
            </div>
            <div class="modal-footer">
                <small class="text-muted me-auto">Haz clic en un marcador para ver detalles</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
const iconosDispositivos = {
    'válvula': L.icon({
        iconUrl: 'images/valvula.png', 
        iconSize: [32, 32],
        iconAnchor: [16, 32]
    }),
    'sensor_flujo': L.icon({
        iconUrl: 'images/sensor.png', 
        iconSize: [32, 32],
        iconAnchor: [16, 32]
    }),
    'default': L.icon({
        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41]
    })
};

// Variable para mantener referencia al mapa
let mapaDispositivos = null;

// Mostrar mapa al hacer clic en el botón
document.querySelectorAll('.btn-ver-mapa').forEach(btn => {
    btn.addEventListener('click', function() {
        const asentamiento = JSON.parse(this.dataset.asentamiento);
        const modal = new bootstrap.Modal(document.getElementById('mapaModal'));
        
        document.getElementById('mapaModalLabel').textContent = `Ubicación de dispositivos en ${asentamiento.nombre}`;
        
        $('#mapaModal').on('shown.bs.modal', function() {
            // Destruir mapa anterior si existe
            if (mapaDispositivos) {
                mapaDispositivos.remove();
            }
            
            // Crear nuevo mapa
            mapaDispositivos = L.map('map', {
                zoomControl: true,
                scrollWheelZoom: false // Evitar zoom accidental al desplazarse
            }).setView([19.4326, -99.1332], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(mapaDispositivos);
            
            // Agregar marcadores para cada dispositivo
            let markers = [];
            let bounds = new L.LatLngBounds();
            
            asentamiento.coordenadas.forEach(coord => {
                const icono = iconosDispositivos[coord.tipo] || iconosDispositivos['default'];
                
                const marker = L.marker([coord.lat, coord.lng], {
                    icon: icono
                }).addTo(mapaDispositivos)
                .bindPopup(`
                    <b>${asentamiento.nombre}</b><br>
                    <b>Tipo:</b> ${coord.tipo.replace('_', ' ').toUpperCase()}<br>
                    <b>Coordenadas:</b> ${parseFloat(coord.lat).toFixed(6)}, ${parseFloat(coord.lng).toFixed(6)}
                `);
                
                markers.push(marker);
                bounds.extend(marker.getLatLng());
            });
            
            if (markers.length > 0) {
                mapaDispositivos.fitBounds(bounds, {padding: [50, 50]});
                if (markers.length === 1) {
                    markers[0].openPopup();
                }
            }
            
            mapaDispositivos.scrollWheelZoom.enable();
        });
        
        modal.show();
    });
});

// Limpiar mapa al cerrar el modal
$('#mapaModal').on('hidden.bs.modal', function() {
    if (mapaDispositivos) {
        mapaDispositivos.remove();
        mapaDispositivos = null;
    }
});

// Auto-submit al seleccionar un municipio
document.getElementById('id_municipio').addEventListener('change', function() {
    if(this.value) {
        this.form.submit();
    }
});
    </script>
</body>
</html>

                        
            