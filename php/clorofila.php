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

if($consulta && mysqli_num_rows($consulta) > 0){
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        // Formatear la fecha para quitar la hora
        $fecha_formateada = date('Y-m-d', strtotime($resultado['fecha_registro']));
        $hora_formateada = date('H:i:s', strtotime($resultado['fecha_registro']));
        $datos[] = [
            'fecha_registro' => $fecha_formateada,
            'hora_registro' => $hora_formateada,
            'valor_clorofila1' => $resultado['valor_clorofila1']
        ];
        $valor_clorofila[] = $resultado['valor_clorofila1'];
        $fechas[] = $fecha_formateada;
    }
    
    // Invertir para orden cronológico correcto
    $valor_clorofila = array_reverse($valor_clorofila);
    $fechas = array_reverse($fechas);
    
    // Valores actuales
    $clorofila_actual = end($valor_clorofila);
} else {
    $clorofila_actual = 'N/A';
    $datos = [];
    $valor_clorofila = [];
    $fechas = [];
}

// Convertir a JSON para JavaScript
$fechas_json = json_encode($fechas);
$valor_clorofila_json = json_encode($valor_clorofila);

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
                        <p class="text-4xl font-bold text-green-800 mt-2"><?= $clorofila_actual ?></p>
                    </div>
                    <i class="fas fa-seedling text-3xl text-green-600"></i>
                </div>
            </div>
            
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Estado de Salud</p>
                        <p class="text-2xl font-bold text-green-800 mt-2">Óptimo</p>
                        <span class="text-sm text-green-600">Rango ideal: 35-50 SPAD</span>
                    </div>
                    <i class="fas fa-heartbeat text-3xl text-red-400"></i>
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
                    <table class="min-w-full bg-white">
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
        // Gráfico principal con configuración mejorada
        const ctx = document.getElementById('mainChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= $fechas_json ?>,
                datasets: [{
                    label: 'Nivel de Clorofila (SPAD)',
                    data: <?= $valor_clorofila_json ?>,
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46, 125, 50, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    x: {
                        ticks: {
                            maxTicksLimit: 8, // Limita el número de etiquetas en el eje X
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: { 
                            text: 'Unidades SPAD', 
                            display: true 
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>