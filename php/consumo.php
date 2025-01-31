<?php
session_start();

if(!isset($_SESSION['usuario'])){
    echo '
        <script>
            alert("Por favor debes de iniciar sesion primero");
            window.location = "../../index.php";
        </script>
    ';
    session_destroy();
    die();
}
include 'conexion.php';

// Consulta para obtener los datos
$SQL = "SELECT * FROM flujo_agua ORDER BY fecha_registro";
$consulta = mysqli_query($con, $SQL);

$litros_seg = [];
$flujo_agua_total = [];
$fecha_total = [];
$fechas = [];
$horas = [];

while ($resultado = mysqli_fetch_array($consulta)) {
    // Datos para los gráficos de Chart.js
    $litros_seg[] = floatval($resultado['litros_seg']);
    $flujo_agua_total[] = floatval($resultado['flujo_agua_total']);
    $fecha_total = date('Y:m:d H:i:s', strtotime($resultado['fecha_registro']));
    $fechas[] = date('Y:m:d', strtotime($resultado['fecha_registro']));
    $horas[] = date('H:i:s', strtotime($resultado['fecha_registro']));
    
}
// Obtener valores actuales (último registro)
$litros_seg_actual = !empty($litros_seg) ? end($litros_seg) : 0;
$hum_actual = !empty($hum) ? end($hum) : 0;

// Convertir a JSON para usar en JavaScript
$fechas = json_encode($fechas);
$litros_seg = json_encode($litros_seg);
$fecha_total = json_encode($fecha_total);
$flujo_agua_total = json_encode($flujo_agua_total);
$horas = json_encode($horas);


// Simulación de datos - Reemplazar con datos reales de los sensores
$current_flow = 2.5; // L/min
$daily_consumption = 450; // L
$weekly_consumption = 2800; // L
$monthly_consumption = 12500; // L

// Datos para la gráfica por horas
$hourly_data = [
    ["hora" => "00:00", "consumo" => 15],
    ["hora" => "03:00", "consumo" => 12],
    ["hora" => "06:00", "consumo" => 25],
    ["hora" => "09:00", "consumo" => 45],
    ["hora" => "12:00", "consumo" => 38],
    ["hora" => "15:00", "consumo" => 42],
    ["hora" => "18:00", "consumo" => 30],
    ["hora" => "21:00", "consumo" => 20]
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroVision</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>
<style>
.header {
    background: linear-gradient(to right,rgb(75, 116, 230),rgb(102, 194, 236));
    color: white;
    padding: 1rem;
    margin-bottom: 2rem;
}
.consumo-bg {
            background: linear-gradient(135deg, #dff6fa 20%, #a4e0ea 80%);
        }

.back-button {
    display: inline-block;
    padding: 10px 20px;
    background-color:rgb(219, 140, 120);
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 20px;
    transition: background-color 0.3s;
}

.back-button:hover {
    background-color: #0056b3;
}

.metric-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
}

.flow-indicator {
    width: 150px;
    height: 150px;
    margin: 0 auto;
    border-radius: 50%;
    border: 10px solid #e2e8f0;
    position: relative;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
    100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
}

.water-wave {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(to bottom, #3b82f6 0%, #60a5fa 100%);
    clip-path: polygon(0 50%, 100% 50%, 100% 100%, 0% 100%);
    animation: wave 3s ease-in-out infinite;
}

@keyframes wave {
    0%, 100% { clip-path: polygon(0 50%, 100% 50%, 100% 100%, 0% 100%); }
    50% { clip-path: polygon(0 45%, 100% 45%, 100% 100%, 0% 100%); }
}
</style>

<body class="consumo-bg"> 
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="../bienvenido.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>Volver al menú principal
                    </a>
                </div>
                <div class="flex items-center">
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
            <h1 class="text-4xl font-bold text-gray-800">Monitoreo de Consumo de agua</h1>
    </div>

    <div class="container mx-auto px-4">
        <!-- Métricas principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="metric-card">
                <div class="flow-indicator mb-4">
                    <div class="water-wave"></div>
                </div>
                <h3 class="text-lg font-semibold text-center text-gray-800">Flujo Actual</h3>
                <p class="text-3xl font-bold text-center text-blue-600"><?php echo $litros_seg_actual; ?> L/seg</p>
            </div>
            <div class="metric-card">
                <h3 class="text-lg font-semibold text-gray-800">Consumo Diario</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo $daily_consumption; ?> L</p>
                <div class="mt-2 text-sm text-gray-500">Último día</div>
            </div>
            <div class="metric-card">
                <h3 class="text-lg font-semibold text-gray-800">Consumo Semanal</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo $weekly_consumption; ?> L</p>
                <div class="mt-2 text-sm text-gray-500">Última semana</div>
            </div>
            <div class="metric-card">
                <h3 class="text-lg font-semibold text-gray-800">Consumo Mensual</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo $monthly_consumption; ?> L</p>
                <div class="mt-2 text-sm text-gray-500">Último mes</div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="metric-card">
                <div id="litros_h"></div>
            </div>
            <div class="metric-card">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Consumo por Zona</h3>
                <div class="relative" style="height: 300px; width: 300px; margin: 0 auto;">
                    <canvas id="zoneChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla de datos -->
        <div class="metric-card mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Registro Detallado</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Hora
                            </th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Consumo (L)
                            </th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hourly_data as $data): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                <?php echo $data['hora']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                <?php echo $data['consumo']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Normal
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Gráfica de consumo por hora
    Highcharts.chart('litros_h', {
    title: {
        text: 'Gráfico de Flujo de Agua'
    },
    xAxis: {
        categories: <?php echo $fechas; ?>, // Usamos las fechas directamente desde JSON
        labels: {
            formatter: function() {
                // Formatear la fecha para mostrar año-mes-dia hora:minuto:segundo
                return Highcharts.dateFormat('%Y-%m-%d %H:%m:%S', new Date(this.value).getTime());
            }
        }
    },
    yAxis: {
        title: {
            text: 'Litros'
        }
    },
    series: [
        {
            name: 'Litros por segundo (L/s)',
            data: <?php echo $litros_seg; ?> // Usamos los litros por segundo desde JSON
        },
        {
            name: 'Litros totales (L)',
            data: <?php echo $flujo_agua_total; ?> // Usamos los litros totales desde JSON
        }
    ]
});

    // Gráfica de consumo por zona
    const zoneCtx = document.getElementById('zoneChart').getContext('2d');
    new Chart(zoneCtx, {
        type: 'doughnut',
        data: {
            labels: ['Zona A', 'Zona B', 'Zona C', 'Zona D'],
            datasets: [{
                data: [30, 25, 20, 25],
                backgroundColor: [
                    '#3b82f6',
                    '#60a5fa',
                    '#93c5fd',
                    '#bfdbfe'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    </script>
</body>
</html>