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
            height: 300px;
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
        <!-- Tiempo Real -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Monitoreo en Tiempo Real</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tarjetas de valores actuales -->
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Temperatura Actual</p>
                            <p class="text-3xl font-bold text-blue-600" id="current-temp"><?= $temp_actual ?>°C</p>
                        </div>
                        <i class="fas fa-thermometer-half text-4xl text-blue-400"></i>
                    </div>
                </div>
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Humedad Actual</p>
                            <p class="text-3xl font-bold text-green-600" id="current-hum"><?= $hum_actual ?>%</p>
                        </div>
                        <i class="fas fa-tint text-4xl text-green-400"></i>
                    </div>
                </div>
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Humedad del Suelo</p>
                            <p class="text-3xl font-bold text-purple-600" id="current-suelo"><?= $suelo_actual ?>%</p>
                        </div>
                        <i class="fas fa-seedling text-4xl text-purple-400"></i>
                    </div>
                </div>
            </div>
            <!-- Gráfica tiempo real -->
            <div class="glass-card p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Gráfico en Tiempo Real</h3>
                <div class="chart-container">
                    <canvas id="realTimeChart"></canvas>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Datos Históricos</h2>
            
            <!-- Temperatura -->
            <div class="glass-card p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Temperatura Histórica (°C)</h3>
                    <div class="flex gap-4">
                        <select id="tempRange" class="px-4 py-2 border rounded-lg">
                            <option value="24h">Últimas 24 horas</option>
                            <option value="7d">Últimos 7 días</option>
                            <option value="30d">Últimos 30 días</option>
                        </select>
                        <button onclick="loadTempData()" class="px-4 py-2 bg-blue-500 text-white rounded-lg">
                            Cargar Datos
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="tempHistoricalChart"></canvas>
                </div>
            </div>

            <!-- Humedad -->
            <div class="glass-card p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Humedad Histórica (%)</h3>
                    <div class="flex gap-4">
                        <select id="humRange" class="px-4 py-2 border rounded-lg">
                            <option value="24h">Últimas 24 horas</option>
                            <option value="7d">Últimos 7 días</option>
                            <option value="30d">Últimos 30 días</option>
                        </select>
                        <button onclick="loadHumData()" class="px-4 py-2 bg-green-500 text-white rounded-lg">
                            Cargar Datos
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="humHistoricalChart"></canvas>
                </div>
            </div>

            <!-- Humedad del Suelo -->
            <div class="glass-card p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Humedad del Suelo Histórica (%)</h3>
                    <div class="flex gap-4">
                        <select id="sueloRange" class="px-4 py-2 border rounded-lg">
                            <option value="24h">Últimas 24 horas</option>
                            <option value="7d">Últimos 7 días</option>
                            <option value="30d">Últimos 30 días</option>
                        </select>
                        <button onclick="loadSueloData()" class="px-4 py-2 bg-purple-500 text-white rounded-lg">
                            Cargar Datos
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="sueloHistoricalChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let realTimeChart, tempHistoricalChart, humHistoricalChart, sueloHistoricalChart;
        let lastUpdate = '<?= end($fechas) ?>';

        // Configuración base para gráficos históricos
        const baseHistoricalConfig = {
            type: 'line',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'hour',
                            displayFormats: {
                                hour: 'dd/MM HH:mm'
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

        // Configuración del gráfico en tiempo real
        const realTimeConfig = {
            type: 'line',
            data: {
                labels: <?= $fechas_json ?>,
                datasets: [
                    {
                        label: 'Temperatura °C',
                        data: <?= $temp_json ?>,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Humedad %',
                        data: <?= $hum_json ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Humedad Suelo %',
                        data: <?= $suelo_json ?>,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.1
                    }
                ]
            },
            options: baseHistoricalConfig.options
        };

        // Inicializar gráficos
        function initCharts() {
            const realTimeCtx = document.getElementById('realTimeChart').getContext('2d');
            const tempCtx = document.getElementById('tempHistoricalChart').getContext('2d');
            const humCtx = document.getElementById('humHistoricalChart').getContext('2d');
            const sueloCtx = document.getElementById('sueloHistoricalChart').getContext('2d');
            
            realTimeChart = new Chart(realTimeCtx, realTimeConfig);

            // Inicializar gráficos históricos
            tempHistoricalChart = new Chart(tempCtx, {
                ...baseHistoricalConfig,
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Temperatura °C',
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1,
                        data: []
                    }]
                }
            });

            humHistoricalChart = new Chart(humCtx, {
                ...baseHistoricalConfig,
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Humedad %',
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.1,
                        data: []
                    }]
                }
            });

            sueloHistoricalChart = new Chart(sueloCtx, {
                ...baseHistoricalConfig,
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Humedad Suelo %',
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.1,
                        data: []
                    }]
                }
            });
        }

        // Actualizar datos en tiempo real
        async function updateData() {
            try {
                const response = await fetch(`fetch_data.php?last_update=${lastUpdate}`);
                const data = await response.json();

                if(data.newData) {
                    // Actualizar valores actuales
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

        async function loadTempData() {
            const range = document.getElementById('tempRange').value;
            try {
                const response = await fetch(`fetch_historical.php?range=${range}&variable=temp`);
                const data = await response.json();
                
                tempHistoricalChart.data.labels = data.labels;
                tempHistoricalChart.data.datasets[0].data = data.temp;
                tempHistoricalChart.update();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function loadHumData() {
            const range = document.getElementById('humRange').value;
            try {
                const response = await fetch(`fetch_historical.php?range=${range}&variable=hum`);
                const data = await response.json();
                
                humHistoricalChart.data.labels = data.labels;
                humHistoricalChart.data.datasets[0].data = data.hum;
                humHistoricalChart.update();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function loadSueloData() {
            const range = document.getElementById('sueloRange').value;
            try {
                const response = await fetch(`fetch_historical.php?range=${range}&variable=suelo`);
                const data = await response.json();
                
                sueloHistoricalChart.data.labels = data.labels;
                sueloHistoricalChart.data.datasets[0].data = data.suelo;
                sueloHistoricalChart.update();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Inicializar y actualizar cada 2 segundos el tiempo real
        initCharts();
        setInterval(updateData, 2000);
        
        // Cargar datos históricos iniciales
        loadTempData();
        loadHumData();
        loadSueloData();
    </script>
</body>
</html>