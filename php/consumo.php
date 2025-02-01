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

// Convertir a JSON para usar en JavaScript
$fechas_json = json_encode($fechas);
$litros_seg_json = json_encode($litros_seg);
$flujo_agua_total_json = json_encode($flujo_agua_total);
$horas_json = json_encode($horas);

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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .chart-container {
            height: 400px;
            position: relative;
        }
        .value-update {
            animation: highlight 1s ease-out;
        }
        @keyframes highlight {
            0% { background-color: rgba(59, 130, 246, 0.1); }
            100% { background-color: transparent; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="../bienvenido.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Menú Principal
                </a>
                <a href="cerrar_sesion.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Sección de Tiempo Real -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Datos en Tiempo Real</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Flujo Actual</p>
                            <p class="text-3xl font-bold text-blue-600 rounded p-2" id="current-flow"><?= $litros_seg_actual ?> L/seg</p>
                        </div>
                        <i class="fas fa-tint text-4xl text-blue-400"></i>
                    </div>
                </div>
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Consumo Diario</p>
                            <p class="text-3xl font-bold text-green-600 rounded p-2"><?= $daily_consumption ?> L</p>
                        </div>
                        <i class="fas fa-chart-line text-4xl text-green-400"></i>
                    </div>
                </div>
            </div>
            <div class="glass-card p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Gráfico en Tiempo Real</h3>
                <div class="chart-container">
                    <canvas id="realTimeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Sección de Datos Históricos -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Datos Históricos</h2>
            <div class="glass-card p-6 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <select id="historicalRange" class="px-4 py-2 border rounded-lg">
                        <option value="24h">Últimas 24 horas</option>
                        <option value="7d">Últimos 7 días</option>
                        <option value="30d">Últimos 30 días</option>
                    </select>
                    <button onclick="loadHistoricalData()" class="px-4 py-2 bg-blue-500 text-white rounded-lg">
                        Cargar Datos
                    </button>
                </div>
            </div>
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold mb-4">Gráfico Histórico</h3>
                <div class="chart-container">
                    <canvas id="historicalChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let realTimeChart, historicalChart;

        // Configuración del gráfico en tiempo real
        const realTimeConfig = {
            type: 'line',
            data: {
                labels: <?= $fechas_json ?>,
                datasets: [
                    {
                        label: 'Flujo de Agua (L/seg)',
                        data: <?= $litros_seg_json ?>,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Flujo Total (L)',
                        data: <?= $flujo_agua_total_json ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'minute',
                            displayFormats: {
                                minute: 'HH:mm'
                            }
                        }
                    },
                    y: {
                        beginAtZero: false
                    }
                }
            }
        };

        // Inicializar gráficos
        function initCharts() {
            const realTimeCtx = document.getElementById('realTimeChart').getContext('2d');
            const historicalCtx = document.getElementById('historicalChart').getContext('2d');
            
            realTimeChart = new Chart(realTimeCtx, realTimeConfig);
            historicalChart = new Chart(historicalCtx, {
                type: 'line',
                data: { labels: [], datasets: [] },
                options: realTimeConfig.options
            });
        }

        // Cargar datos históricos
        async function loadHistoricalData() {
            const range = document.getElementById('historicalRange').value;
            try {
                const response = await fetch(`fetch_historical.php?range=${range}`);
                const data = await response.json();

                historicalChart.data.labels = data.labels;
                historicalChart.data.datasets = [
                    {
                        label: 'Flujo de Agua (L/seg)',
                        data: data.flow,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Flujo Total (L)',
                        data: data.totalFlow,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.1
                    }
                ];
                historicalChart.update();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Inicializar gráficos
        initCharts();
    </script>
</body>
</html>