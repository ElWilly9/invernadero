<?php
session_start();
include_once 'conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

// Obtener últimos 100 registros para tiempo real
$SQL = "SELECT 
    th.temperatura_ambiente,
    th.humedad_ambiente,
    th.fecha_registro,
    (
        SELECT hs.humedad
        FROM hum_suelo hs
        WHERE DATE(hs.fecha_registro) = DATE(th.fecha_registro)
        AND ABS(TIME_TO_SEC(TIMEDIFF(hs.hora_registro, TIME(th.fecha_registro)))) < 60
        ORDER BY ABS(TIME_TO_SEC(TIMEDIFF(hs.hora_registro, TIME(th.fecha_registro))))
        LIMIT 1
    ) as humedad
FROM temp_hum_amb th
ORDER BY th.fecha_registro DESC 
LIMIT 100";;

$consulta = mysqli_query($con, $SQL);

$temp = [];
$hum = [];
$suelo = [];
$fechas = [];

if($consulta && mysqli_num_rows($consulta) > 0){
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        $temp[] = $resultado['temperatura_ambiente'];
        $hum[] = $resultado['humedad_ambiente'];
        $suelo[] = $resultado['humedad']; // Cambiado a 'humedad'
        $fechas[] = $resultado['fecha_registro'];
    }
    
    // Invertir para orden cronológico correcto
    $temp = array_reverse($temp);
    $hum = array_reverse($hum);
    $suelo = array_reverse($suelo);
    $fechas = array_reverse($fechas);
    
    $temp_actual = end($temp);
    $hum_actual = end($hum);
    $suelo_actual = end($suelo) ?: 'N/A';
} else {
    $temp_actual = 0;
    $hum_actual = 0;
    $suelo_actual = 'N/A';
}

// Convertir a JSON para JavaScript
$fechas_json = json_encode($fechas);
$temp_json = json_encode($temp);
$hum_json = json_encode($hum);
$suelo_json = json_encode($suelo);
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
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Monitoreo de Temperatura y Humedad</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tarjeta de Temperatura -->
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Temperatura ambiente Actual</p>
                            <p class="text-3xl font-bold text-blue-600 rounded p-2" id="current-temp"><?= $temp_actual ?>°C</p>
                        </div>
                        <i class="fas fa-thermometer-half text-4xl text-blue-400"></i>
                    </div>
                </div>
                <!-- Tarjeta de Humedad Ambiente -->
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Humedad ambiente Actual</p>
                            <p class="text-3xl font-bold text-green-600 rounded p-2" id="current-hum"><?= $hum_actual ?>%</p>
                        </div>
                        <i class="fas fa-tint text-4xl text-green-400"></i>
                    </div>
                </div>
                <!-- Tarjeta de Humedad del Suelo -->
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Humedad del Suelo</p>
                            <p class="text-3xl font-bold text-purple-600 rounded p-2" id="current-suelo"><?= $suelo_actual ?>%</p>
                        </div>
                        <i class="fas fa-seedling text-4xl text-purple-400"></i>
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
        let lastUpdate = '<?= end($fechas) ?>';

        // Configuración del gráfico en tiempo real
        const realTimeConfig = {
            type: 'line',
            data: {
                labels: <?= $fechas_json ?>,
                datasets: [
                    {
                        label: 'Temperatura ambiente °C',
                        data: <?= $temp_json ?>,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Humedad ambiente %',
                        data: <?= $hum_json ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Humedad del Suelo %',
                        data: <?= $suelo_json ?>,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
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
                        },
                        ticks: {
                            source: 'auto',
                            autoSkip: true
                        }
                    },
                    y: {
                        beginAtZero: false
                    }
                },
                plugins: {
                    decimation: {
                        enabled: true,
                        algorithm: 'min-max'
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

        // Actualizar datos en tiempo real
        async function updateData() {
            try {
                const response = await fetch(`fetch_data.php?last_update=${lastUpdate}`);
                const data = await response.json();

                if(data.newData) {
                    // Actualizar valores
                    document.getElementById('current-temp').textContent = data.current.temp + '°C';
                    document.getElementById('current-hum').textContent = data.current.hum + '%';
                    document.getElementById('current-suelo').textContent = data.current.suelo + '%';

                    // Actualizar gráfico en tiempo real
                    realTimeChart.data.labels.push(...data.newDates);
                    realTimeChart.data.datasets[0].data.push(...data.newTemp);
                    realTimeChart.data.datasets[1].data.push(...data.newHum);
                    realTimeChart.data.datasets[2].data.push(...data.newSuelo);
                    realTimeChart.update();

                    lastUpdate = data.last_update;
                }
            } catch (error) {
                console.error('Error:', error);
            }
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
                        label: 'Temperatura °C',
                        data: data.temp,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Humedad %',
                        data: data.hum,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Humedad del Suelo %',
                        data: data.suelo,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.1
                    }
                ];
                historicalChart.update();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Inicializar y actualizar cada 2 segundos
        initCharts();
        setInterval(updateData, 2000);
    </script>
</body>
</html>