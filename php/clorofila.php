<?php
// index.php - El dashboard principal
session_start();
include_once 'conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

// Obtener últimos 50 registros para tiempo real con datos de ambos sensores
$SQL = "SELECT 
    cl.valor_clorofila1,
    cl.fecha_registro,
    cl.hora_registro
FROM clorofila cl
ORDER BY cl.fecha_registro DESC 
LIMIT 50";

$consulta = mysqli_query($con, $SQL);
$datos = [];
$valor_clorofila = [];
$fechas = [];
$fechas_hora = []; // Nuevo arreglo para fecha + hora

if($consulta && mysqli_num_rows($consulta) > 0){
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        // Formatear la fecha para quitar la hora
        $fecha_formateada = date('Y-m-d', strtotime($resultado['fecha_registro']));
        $hora_formateada = date('H:i:s', strtotime($resultado['hora_registro']));
        
        $datos[] = [
            'fecha_registro' => $fecha_formateada,
            'hora_registro' => $hora_formateada,
            'valor_clorofila1' => $resultado['valor_clorofila1']
        ];
        
        $valor_clorofila[] = $resultado['valor_clorofila1'];
        $fechas[] = $fecha_formateada;
        $fechas_hora[] = $fecha_formateada . ' ' . $hora_formateada; // Combinar fecha y hora
    }
    
    // Invertir para orden cronológico correcto
    $valor_clorofila = array_reverse($valor_clorofila);
    $fechas = array_reverse($fechas);
    $fechas_hora = array_reverse($fechas_hora); // También invertir este arreglo
    
    // Valores actuales
    $clorofila_actual = end($valor_clorofila);
} else {
    $clorofila_actual = 'N/A';
    $datos = [];
    $valor_clorofila = [];
    $fechas = [];
    $fechas_hora = [];
}

// Define el rango óptimo
$rango_min = 35;
$rango_max = 50;

// Determina si el valor actual está en el rango óptimo
$estado_salud = ($clorofila_actual >= $rango_min && $clorofila_actual <= $rango_max) ? "Óptimo" : "No Óptimo";
$color_estado = ($estado_salud == "Óptimo") ? "text-green-800" : "text-red-600";
$icono_estado = ($estado_salud == "Óptimo") ? "heartbeat" : "exclamation-triangle";
$color_icono = ($estado_salud == "Óptimo") ? "text-red-400" : "text-yellow-500";

// Convertir a JSON para JavaScript
$fechas_json = json_encode($fechas);
$valor_clorofila_json = json_encode($valor_clorofila);
$fechas_hora_json = json_encode($fechas_hora); // Nuevo JSON para fecha y hora

// Obtener página actual para la paginación
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$registros_por_pagina = 10;
$total_registros = count($datos);
$total_paginas = ceil($total_registros / $registros_por_pagina);
$inicio = ($pagina_actual - 1) * $registros_por_pagina;
$datos_pagina = array_slice($datos, $inicio, $registros_por_pagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroVision</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .chlorophyll-bg {
            background: linear-gradient(135deg, #e6f4ea 20%, #f8fcf3 80%);
        }
        .chlorophyll-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(46, 125, 50, 0.1);
        }
        .chlorophyll-gradient {
            background: linear-gradient(135deg, #2e7d32 0%, #66bb6a 100%);
        }
        .leaf-marker {
            background: url('https://img.icons8.com/ios-filled/50/228B22/leaf.png') no-repeat center;
            background-size: contain;
            width: 40px;
            height: 40px;
        }
    </style>
</head>
<body class="chlorophyll-bg min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Parte izquierda -->
            <div class="flex items-center space-x-4">
                <a href="../bienvenido.php" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al menú principal
                </a>
            </div>

            <!-- Parte derecha -->
            <div class="flex items-center space-x-4">
                <a href="cerrar_sesion.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800">Monitoreo de la clorofila en las plantas</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Nivel Actual (SPAD)</p>
                        <p id="valorActual" class="text-4xl font-bold text-green-800 mt-2"><?= $clorofila_actual ?></p>
                    </div>
                    <i class="fas fa-seedling text-3xl text-green-600"></i>
                </div>
            </div>
            
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Estado de Salud Actual</p>
                        <p id="estadoSalud" class="text-2xl font-bold <?= $color_estado ?> mt-2"><?= $estado_salud ?></p>
                        <i id="iconoEstado" class="fas fa-<?= $icono_estado ?> text-3xl <?= $color_icono ?>"></i>
                        <span class="text-sm text-green-600">Rango ideal: <?= $rango_min ?>-<?= $rango_max ?> SPAD</span>
                    </div>
                    <i class="fas fa-<?= $icono_estado ?> text-3xl <?= $color_icono ?>"></i>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Gráfico Principal -->
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-green-800 mb-4">Tendencia de Clorofila (SPAD)</h2>
                <canvas id="mainChart"></canvas>
            </div>

            <!-- Tabla de Mediciones -->
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-green-800 mb-4">Últimas Mediciones</h2>
                <div class="overflow-x-auto">
                    <table id="tablaMediciones" class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Fecha</th>
                                <th class="py-2 px-4 border-b">Hora</th>
                                <th class="py-2 px-4 border-b">Valor Clorofila (SPAD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($datos_pagina)) {
                                foreach($datos_pagina as $resultado) {
                                    echo "<tr>";
                                    echo "<td class='py-2 px-4 border-b'>" . $resultado['fecha_registro'] . "</td>";
                                    echo "<td class='py-2 px-4 border-b'>" . $resultado['hora_registro'] . "</td>";
                                    echo "<td class='py-2 px-4 border-b'>" . $resultado['valor_clorofila1'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='py-2 px-4 border-b text-center'>No hay datos disponibles</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Controles de paginación -->
                <?php if ($total_paginas > 1): ?>
                <div class="flex justify-between items-center mt-4">
                    <button 
                        onclick="window.location.href='?pagina=<?php echo max($pagina_actual - 1, 1); ?>'"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg disabled:opacity-50 <?php echo $pagina_actual <= 1 ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                        <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>
                    >
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                    
                    <span class="text-sm text-gray-600">
                        Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?>
                    </span>
                    
                    <button 
                        onclick="window.location.href='?pagina=<?php echo min($pagina_actual + 1, $total_paginas); ?>'"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg disabled:opacity-50 <?php echo $pagina_actual >= $total_paginas ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                        <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>
                    >
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    const ctx = document.getElementById('mainChart').getContext('2d');
    // Obtenemos las etiquetas de fecha+hora
    const fechasHora = <?= $fechas_hora_json ?>;
    const rangoMin = <?= $rango_min ?>;
    const rangoMax = <?= $rango_max ?>;
    
    // Función para formatear fechas de manera más compacta
    function formatearFechaCorta(fecha) {
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit'
        });
    }
    
    // Preparar datos para la gráfica
    const fechas = <?= $fechas_json ?>;
    const fechasFormateadas = fechas.map(formatearFechaCorta);
    
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: fechasFormateadas, // Usamos fechas formateadas cortas
            datasets: [{
                label: 'Nivel de Clorofila (SPAD)',
                data: <?= $valor_clorofila_json ?>,
                borderColor: '#2e7d32',
                backgroundColor: 'rgba(46, 125, 50, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            },
            // Agregamos una línea para el rango mínimo óptimo
            {
                label: 'Rango Mínimo Óptimo',
                data: Array(fechas.length).fill(rangoMin),
                borderColor: 'rgba(255, 193, 7, 0.7)',
                borderDash: [5, 5],
                borderWidth: 2,
                pointRadius: 0,
                fill: false
            },
            // Agregamos una línea para el rango máximo óptimo
            {
                label: 'Rango Máximo Óptimo',
                data: Array(fechas.length).fill(rangoMax),
                borderColor: 'rgba(255, 193, 7, 0.7)',
                borderDash: [5, 5],
                borderWidth: 2,
                pointRadius: 0,
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { 
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 10
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        // Personalizar el título del tooltip para mostrar fecha y hora
                        title: function(tooltipItems) {
                            const index = tooltipItems[0].dataIndex;
                            return `Fecha y Hora: ${fechasHora[index]}`; // Muestra la fecha y hora exactas
                        },
                        // Personalizar el valor mostrado en el tooltip
                        label: function(context) {
                            // Solo mostrar valor para el dataset principal
                            if (context.datasetIndex === 0) {
                                return 'Valor SPAD: ' + context.raw;
                            } else if (context.datasetIndex === 1) {
                                return 'Rango mínimo: ' + context.raw;
                            } else if (context.datasetIndex === 2) {
                                return 'Rango máximo: ' + context.raw;
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        maxTicksLimit: 10, // Limitar el número de etiquetas en el eje X
                        maxRotation: 45, // Rotar las etiquetas para que ocupen menos espacio
                        minRotation: 0,
                        font: {
                            size: 10 // Reducir el tamaño de la fuente
                        }
                    }
                },
                y: {
                    title: { 
                        text: 'Unidades SPAD', 
                        display: true 
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    // Asegurar que el rango mínimo y máximo estén dentro del área visible
                    min: function(context) {
                        // Define un límite inferior que sea un poco menor que el rango mínimo o el valor mínimo
                        const valores = <?= $valor_clorofila_json ?>;
                        const minValor = Math.min(...valores);
                        return Math.max(0, Math.min(minValor, rangoMin) - 5);
                    },
                    max: function(context) {
                        // Define un límite superior que sea un poco mayor que el rango máximo o el valor máximo
                        const valores = <?= $valor_clorofila_json ?>;
                        const maxValor = Math.max(...valores);
                        return Math.max(maxValor, rangoMax) + 5;
                    }
                }
            }
        }
    });

    function updateChart() {
    fetch('get_data.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Verifica si hay nuevos datos
            if (data.fechas && data.fechas.length > 0) {
                // Actualizar la gráfica
                const fechasFormateadas = data.fechas.map(formatearFechaCorta);
                myChart.data.labels = fechasFormateadas;
                myChart.data.datasets[0].data = data.valores;
                myChart.data.datasets[1].data = Array(data.fechas.length).fill(rangoMin);
                myChart.data.datasets[2].data = Array(data.fechas.length).fill(rangoMax);
                myChart.update();

                // Actualizar el valor actual de clorofila
                document.getElementById('valorActual').textContent = data.clorofila_actual;

                // Actualizar el estado de salud
                const estadoSalud = document.getElementById('estadoSalud');
                const iconoEstado = document.getElementById('iconoEstado');
                estadoSalud.textContent = data.estado_salud;
                estadoSalud.className = data.estado_salud === "Óptimo" ? 
                    'text-2xl font-bold text-green-800 mt-2' : 
                    'text-2xl font-bold text-red-600 mt-2';
                iconoEstado.className = data.estado_salud === "Óptimo" ? 
                    'fas fa-heartbeat text-3xl text-red-400' : 
                    'fas fa-exclamation-triangle text-3xl text-yellow-500';

                // Actualizar la tabla
                const tablaBody = document.querySelector('#tablaMediciones tbody');
                tablaBody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos datos

                data.datos_tabla.forEach(item => {
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td class="py-2 px-4 border-b">${item.fecha_registro}</td>
                        <td class="py-2 px-4 border-b">${item.hora_registro}</td>
                        <td class="py-2 px-4 border-b">${item.valor_clorofila1}</td>
                    `;
                    tablaBody.appendChild(fila);
                });
            }
        })
        .catch(error => console.error('Error al obtener datos:', error));
}
    // Actualiza el gráfico cada 2 segundos
    setInterval(updateChart, 2000);
</script>
</body>
</html>