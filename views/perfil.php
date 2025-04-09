<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

require_once '../controllers/FugaController.php';
require_once '../controllers/MunicipioController.php';
require_once '../controllers/AsentamientoController.php';

$fugaController = new FugaController();
$municipioController = new MunicipioController();
$asentamientoController = new AsentamientoController(); 

$fugas = $fugaController->obtenerFugas();
$municipios = $municipioController->obtenerMunicipiosParaMapa();
$totalMunicipios = $municipioController->contarMunicipios();
$totalAsentamientos = $asentamientoController->contarAsentamientos();

// Obtener la última fuga registrada para la fecha
$fugas = $fugaController->obtenerFugas();

if (!empty($fugas)) {
    $ultimaFuga = end($fugas);
    $ultimaActualizacion = date('d/m/Y H:i:s', strtotime($ultimaFuga['timestamp']));
} else {
    $ultimaActualizacion = 'No hay datos';
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

    <title>Admin Dashboard</title>
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/logo-03.png" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

                    <div class="d-sm-flex align-items-center justify-content-between mb-4" style="padding-top: 14px; padding-left: 10px;">
                        <h1 class="h3 mb-0 text-gray-800">Bienvenid@, <?php echo $_SESSION['usuario']['nombre']; ?></h1>
                    </div>

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

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Municipios (TOTAL)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $totalMunicipios; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                        <i class="fa-solid fa-earth-oceania fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Asentamientos (Totales)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $totalAsentamientos; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                        <i class="fa-solid fa-city fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Ultima actialización (Flujo)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $ultimaActualizacion; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                        <i class="fa-solid fa-magnifying-glass fa-2x  text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- mapa -->
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Histórico de Consumo</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container" style="position: relative; height:300px; width:100%">
                                            <canvas id="consumoChart"></canvas>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <span class="badge bg-info me-2"><i class="fas fa-circle"></i> Consumo Promedio</span>
                                            <span class="badge bg-warning"><i class="fas fa-circle"></i> Límite Recomendado</span>
                                        </div>
                                    </div>
                                </div>
                        </div>
                
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Monitoreo de Flujo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="position: relative; height:300px; width:100%">
                                        <canvas id="flujoAguaChart"></canvas>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <span class="badge bg-primary me-2"><i class="fas fa-circle"></i> Flujo Normal</span>
                                        <span class="badge bg-danger"><i class="fas fa-circle"></i> Fuga Detectada</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card shadow mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Municipios</h6>
                                </div>
                                <div class="card-body">
                                    <div class="map-container">
                                      <div id="map" style="height: 500px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

    <!-- Scripts para el mapa y la gráfica -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };

    const municipios = <?php echo json_encode($municipios); ?>;
    const fugas = <?php echo json_encode($fugas); ?>;
    const centroInicial = [19.3525, -99.6286];
    const zoomInicial = 9;

    const map = L.map('map').setView(centroInicial, zoomInicial);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    municipios.forEach(municipio => {
        const marker = L.marker([municipio.latitud, municipio.longitud]).addTo(map);

        marker.bindTooltip(municipio.nombre, {
            permanent: false,
            direction: 'top',
            opacity: 0.9,
        });

        marker.on('click', () => {
            map.setView([municipio.latitud, municipio.longitud], 13);
        });

        marker.on('popupclose', () => {
            map.setView(centroInicial, zoomInicial);
        });
    });

</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== Configuración de la primera gráfica (Flujo de Agua) ==========
    const flujoCanvas = document.getElementById('flujoAguaChart');
    if (flujoCanvas.chart) {
        flujoCanvas.chart.destroy();
    }

    const flujoCtx = flujoCanvas.getContext('2d');
    const flujoAguaChart = new Chart(flujoCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Flujo de Agua (L/min)',
                    data: [],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#4e73df'
                },
                {
                    label: 'Fuga Detectada',
                    data: [],
                    borderColor: 'rgba(255, 99, 132, 0)',
                    backgroundColor: 'rgba(255, 99, 132, 0.3)',
                    borderWidth: 0,
                    pointBackgroundColor: function(context) {
                        const index = context.dataIndex;
                        const value = context.dataset.data[index];
                        return value !== null ? 'rgb(255, 99, 132)' : 'rgba(0, 0, 0, 0)';
                    },
                    pointRadius: function(context) {
                        const index = context.dataIndex;
                        const value = context.dataset.data[index];
                        return value !== null ? 5 : 0;
                    },
                    pointHoverRadius: 7,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y + ' L/min';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Flujo (L/min)'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
    flujoCanvas.chart = flujoAguaChart;

    // ========== Configuración de la segunda gráfica (Consumo) ==========
    const consumoCanvas = document.getElementById('consumoChart');
    if (consumoCanvas.chart) {
        consumoCanvas.chart.destroy();
    }

    const consumoCtx = consumoCanvas.getContext('2d');
    const consumoChart = new Chart(consumoCtx, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [
                {
                    label: 'Consumo Promedio',
                    data: [120, 190, 170, 210, 230, 250, 240, 260, 230, 210, 190, 150],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Límite Recomendado',
                    data: [200, 200, 200, 200, 200, 200, 200, 200, 200, 200, 200, 200],
                    type: 'line',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Consumo (L)'
                    }
                }
            }
        }
    });
    consumoCanvas.chart = consumoChart;

    // ========== Función para cargar datos del flujo ==========
    function cargarDatosFlujo() {
        fetch('../api/historico_flujo.php?limit=40')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success' && data.data && data.data.length > 0) {
                    const labels = data.data.map(item => {
                        const date = new Date(item.timestamp);
                        return `${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')}`;
                    });
                    const valoresFlujo = data.data.map(item => parseFloat(item.flujo_medido));
                    const valoresFugas = data.data.map(item => 
                        item.estado === 'Fuga Detectada' ? item.flujo_medido * 1.2 : null
                    );
                    
                    flujoAguaChart.data.labels = labels;
                    flujoAguaChart.data.datasets[0].data = valoresFlujo;
                    flujoAguaChart.data.datasets[1].data = valoresFugas;
                    flujoAguaChart.update();
                }
            })
            .catch(error => {
                console.error('Error al obtener datos:', error);
            });
    }

    // Cargar datos iniciales
    cargarDatosFlujo();
    
    // Actualizar cada 5 segundos
    const intervalo = setInterval(cargarDatosFlujo, 5000);
    
    // Manejar el redimensionamiento de la ventana
    window.addEventListener('resize', function() {
        flujoAguaChart.resize();
        consumoChart.resize();
    });
});
</script>
</body>
</html>