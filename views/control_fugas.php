<?php
require_once '../models/Database.php';

$db = new Database();
$conn = $db->getConnection();

$query = "SELECT * FROM fugas ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($query);
$ultimaFuga = $result->fetch_assoc();

$accionesQuery = "SELECT a.*, u.nombre as nombre_usuario 
                 FROM acciones a 
                 LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario 
                 WHERE a.id_dispositivo = 1 
                 ORDER BY a.timestamp DESC 
                 LIMIT 10";
$accionesResult = $conn->query($accionesQuery);
$acciones = [];
while ($row = $accionesResult->fetch_assoc()) {
    $acciones[] = $row;
}

$conn->close();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .alert-status {
            transition: all 0.5s ease;
        }
        .valve-status {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .valve-open {
            color: #28a745;
        }
        .valve-closed {
            color: #dc3545;
        }

        .full-width-chart {
        margin-top: 20px;
    }
    
    .card-body canvas {
        width: 100% !important;
        height: 100% !important;
    }
    
    @media (max-width: 768px) {
        .card-body {
            height: 300px !important;
        }
        
        /* Ajustes para móviles */
        .row.mb-4 > div {
            margin-bottom: 15px;
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
                <a class="nav-link" href="#">
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

                <div class="container py-4">
        <h1 class="text-center mb-4">Sistema de Detección de Fugas</h1>
        
        <!-- Panel de estado -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Estado Actual</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-<?= $ultimaFuga['estado'] == 'Fuga Detectada' ? 'danger' : 'success' ?> alert-status">
                            <h4 class="alert-heading"><?= $ultimaFuga['estado'] ?></h4>
                            <p>Flujo principal: <strong><?= $ultimaFuga['flujo_medido'] ?> L/min</strong></p>
                            <p>Gravedad: <span class="badge bg-<?= 
                                $ultimaFuga['gravedad'] == 'leve' ? 'info' : 
                                ($ultimaFuga['gravedad'] == 'moderada' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($ultimaFuga['gravedad']) ?>
                            </span></p>
                            <hr>
                            <p class="mb-0">Última actualización: <?= $ultimaFuga['timestamp'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Control de Válvulas</h5>
                    </div>
                    <div class="card-body">
    <div class="mb-3">
        <h6>Válvula Principal</h6>
        <span id="valve1-status" class="valve-status valve-<?= $ultimaFuga['estado'] == 'Fuga Detectada' ? 'closed' : 'open' ?>">
            <?= $ultimaFuga['estado'] == 'Fuga Detectada' ? 'CERRADA' : 'ABIERTA' ?>
        </span>
    </div>
    <div class="mb-3">
        <h6>Válvula Secundaria</h6>
        <span id="valve2-status" class="valve-status valve-<?= $ultimaFuga['estado'] == 'Fuga Detectada' ? 'open' : 'closed' ?>">
            <?= $ultimaFuga['estado'] == 'Fuga Detectada' ? 'ABIERTA' : 'CERRADA' ?>
        </span>
    </div>
    
    <?php if ($ultimaFuga['estado'] == 'Fuga Detectada' && empty($ultimaFuga['accion_realizada'])): ?>
        <div class="alert alert-danger" id="alerta-fuga">
            <h5>Fuga detectada en <?= htmlspecialchars($ultimaFuga['nombre_asentamiento'] ?? 'el sistema') ?></h5>
            <p>El protocolo de emergencia se activará automáticamente en <span id="tiempo-restante">2:00</span></p>
        </div>
    <?php elseif ($ultimaFuga['estado'] == 'Fuga Detectada'): ?>
        <div class="alert alert-success">
            <h5>Protocolo completado</h5>
            <p>Válvula principal cerrada y secundaria abierta automáticamente</p>
        </div>
    <?php endif; ?>
</div>
                   
                </div>
            </div>
        </div>
        
        <!-- Gráfico y historial -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Histórico de Flujo</h5>
                    </div>
                    <div class="card-body p-0" style="position: relative; height: 400px;">
                        <canvas id="flowChart"></canvas>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        function actualizarEstado() {
    fetch('../api/ultimo_estado.php')
        .then(response => {
            if (!response.ok) throw new Error('Error al obtener estado');
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                const estado = data.data;
                const alerta = document.querySelector('.alert-status');
                
                // Actualizar alerta
                alerta.className = `alert alert-${estado.fuga.estado === 'Fuga Detectada' ? 'danger' : 'success'} alert-status`;
                alerta.querySelector('.alert-heading').textContent = estado.fuga.estado;
                alerta.querySelector('strong').textContent = `${estado.fuga.flujo_medido} L/min`;
                
                // Actualizar estado de válvulas
                const valve1 = document.getElementById('valve1-status');
                const valve2 = document.getElementById('valve2-status');
                const btnCerrar = document.getElementById('btn-cerrar');
                const btnAbrir = document.getElementById('btn-abrir');
                
                valve1.className = `valve-status valve-${estado.valvulas.valvula1 === 'cerrada' ? 'closed' : 'open'}`;
                valve1.textContent = estado.valvulas.valvula1 === 'cerrada' ? 'CERRADA' : 'ABIERTA';
                
                valve2.className = `valve-status valve-${estado.valvulas.valvula2 === 'abierta' ? 'open' : 'closed'}`;
                valve2.textContent = estado.valvulas.valvula2 === 'abierta' ? 'ABIERTA' : 'CERRADA';
                
                // Actualizar estado de botones
                btnCerrar.disabled = estado.valvulas.valvula1 === 'cerrada';
                btnAbrir.disabled = estado.valvulas.valvula2 === 'abierta';
            }
        })
        .catch(error => {
            console.error('Error al actualizar estado:', error);
        });
}
        
        // Función para controlar válvulas
        function controlarValvula(accion) {
    fetch('../api/controlar_valvula.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest' // Para detectar AJAX en PHP
        },
        body: JSON.stringify({
            id_dispositivo: 1,
            accion: accion,
            tipo: 'manual'
        }),
        credentials: 'include' 
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la red');
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            const usuario = data.data.id_usuario === 0 ? 'Sistema' : `Usuario ${data.data.id_usuario}`;
            mostrarNotificacion(`Válvula ${data.data.accion}da por ${usuario}`, 'success');
            actualizarEstado();
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        mostrarNotificacion(`Error: ${error.message}`, 'danger');
        console.error('Error:', error);
    });
}


// Cuenta regresiva para acción automática
function iniciarCuentaRegresiva() {
    let tiempoRestante = 180;
    const btnCerrar = document.getElementById('btn-cerrar');
    const intervalo = setInterval(() => {
        tiempoRestante--;
        
        const minutos = Math.floor(tiempoRestante / 60);
        const segundos = tiempoRestante % 60;
        
        btnCerrar.innerHTML = `Cierre automático en <span class="fw-bold">${minutos}:${segundos.toString().padStart(2, '0')}</span>`;
        
        if (tiempoRestante <= 0) {
            clearInterval(intervalo);
            // Ejecutar cierre automático
            fetch('../api/controlar_valvula.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    id_dispositivo: 1, 
                    accion: 'cerrar',
                    tipo: 'automatica'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    mostrarNotificacion("Cierre automático ejecutado", "success");
                    actualizarEstado();
                }
            });
            
            btnCerrar.textContent = 'Cierre automático activado';
            btnCerrar.className = 'btn btn-dark me-md-2';
            btnCerrar.disabled = true;
        }
    }, 1000);
}

// Actualiza estado cada 5 segundos
setInterval(actualizarEstado, 5000);
        
        // Configurar gráfico
        const ctx = document.getElementById('flowChart').getContext('2d');
const flowChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Flujo Principal (L/min)',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                borderWidth: 2
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
                position: 'top',
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
                },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    }
});

// Cargar datos históricos para el gráfico
function cargarDatosGrafica() {
    fetch('../api/historico_flujo.php?limit=40') // Aumenta el límite para más datos
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                
                const labels = data.data.map(item => {
                    const date = new Date(item.timestamp);
                    return `${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')}`;
                });
                
                flowChart.data.labels = labels;
                flowChart.data.datasets[0].data = data.data.map(item => item.flujo_medido);
                flowChart.data.datasets[1].data = data.data.map(item => 
                    item.estado === 'Fuga Detectada' ? item.flujo_medido * 1.2 : null
                );
                
                if (window.innerWidth < 768) {
                    flowChart.options.scales.x.ticks.maxRotation = 90;
                    flowChart.options.scales.x.ticks.minRotation = 90;
                } else {
                    flowChart.options.scales.x.ticks.maxRotation = 45;
                    flowChart.options.scales.x.ticks.minRotation = 45;
                }
                
                flowChart.update();
            }
        });
}

// Llamar a la función inicialmente y cada 5 segundos
cargarDatosGrafica();
setInterval(cargarDatosGrafica, 5000);

let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        flowChart.resize();
    }, 250);
});
        
        // Función para mostrar notificaciones estilo Toast
function mostrarNotificacion(mensaje, tipo = 'info') {
    const toastContainer = document.getElementById('toast-container') || crearToastContainer();
    const toast = document.createElement('div');
    
    toast.className = `toast show align-items-center text-white bg-${tipo} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${mensaje}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Eliminar el toast después de 5 segundos
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function crearToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '11';
    document.body.appendChild(container);
    return container;
}

        // Cargar datos históricos para el gráfico
        fetch('../api/historico_flujo.php?limit=20')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    flowChart.data.labels = data.data.map(item => item.timestamp);
                    flowChart.data.datasets[0].data = data.data.map(item => item.flujo_medido);
                    flowChart.data.datasets[1].data = data.data.map(item => 
                        item.estado === 'Fuga Detectada' ? item.flujo_medido * 1.2 : null
                    );
                    flowChart.update();
                }
            });

            function activarProtocoloEmergencia() {
    if (confirm('¿Está seguro que desea activar el protocolo de emergencia? Esto cerrará la válvula principal y abrirá la secundaria.')) {
        fetch('../api/controlar_valvula.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id_dispositivo: 1,
                accion: 'protocolo_emergencia',
                tipo: 'manual'
            }),
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                mostrarNotificacion("Protocolo de emergencia activado", "success");
                actualizarEstado();
                iniciarCuentaRegresiva();
            }
        });
    }
}

function normalizarSistema() {
    if (confirm('¿Está seguro que desea restablecer el flujo normal? Esto abrirá la válvula principal y cerrará la secundaria.')) {
        fetch('../api/controlar_valvula.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id_dispositivo: 1,
                accion: 'restablecer_normal',
                tipo: 'manual'
            }),
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                mostrarNotificacion("Sistema restablecido a flujo normal", "success");
                actualizarEstado();
            }
        });
    }
}
    </script>
</body>
</html>