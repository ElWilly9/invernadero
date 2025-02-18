<?php
session_start();
include_once 'conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

// Consulta para obtener los últimos 50 registros con datos de consumo
$SQL = "SELECT 
    litros_min,
    flujo_acumulado,
    fecha_registro
FROM flujo_agua
ORDER BY fecha_registro DESC 
LIMIT 50";

$consulta = mysqli_query($con, $SQL);

$litros_min = [];
$flujo_acumulado = [];
$fechas = [];

if($consulta && mysqli_num_rows($consulta) > 0){
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        $litros_min[] = $resultado['litros_min'];
        $flujo_acumulado[] = $resultado['flujo_acumulado'];
        $fechas[] = $resultado['fecha_registro'];
    }
    
    // Invertir para orden cronológico correcto
    $litros_min = array_reverse($litros_min);
    $flujo_acumulado = array_reverse($flujo_acumulado);
    $fechas = array_reverse($fechas);
    
    // Valores actuales
    $litros_min_actual = end($litros_min) ?: 'N/A';
    $flujo_acumulado_actual = end($flujo_acumulado) ?: 'N/A';
} else {
    $litros_min_actual = 'N/A';
    $flujo_acumulado_actual = 'N/A';
}

// Convertir a JSON para JavaScript
$fechas_json = json_encode($fechas);
$litros_min_json = json_encode($litros_min);
$flujo_acumulado_json = json_encode($flujo_acumulado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumo de Agua</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
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
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen">
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
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Datos en Tiempo Real</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Flujo Actual</p>
                            <p class="text-3xl font-bold text-blue-600 rounded p-2" id="current-flow"><?= $litros_min_actual ?> L/min</p>
                        </div>
                        <i class="fas fa-tint text-4xl text-blue-400"></i>
                    </div>
                </div>
                <div class="glass-card p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Consumo Acumulado</p>
                            <p class="text-3xl font-bold text-green-600 rounded p-2" id="current-total"><?= $flujo_acumulado_actual ?> L</p>
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

        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Datos Históricos</h2>
            <div class="glass-card p-6 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <select id="historicalRange" class="px-4 py-2 border rounded-lg">
                        <option value="24h">Últimas 24 horas</option>
                        <option value="7d">Últimos 7 días</option>
                        <option value="30d">Últimos 30 días</option>
                    </select>
                    <button onclick="loadHistoricalData()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
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
    // Datos precargados desde PHP
    const labels = <?= $fechas_json ?>;
    const litrosMin = <?= $litros_min_json ?>;
    const flujoAcumulado = <?= $flujo_acumulado_json ?>;

    let realTimeChart, historicalChart;
    let lastUpdate = '<?= end($fechas) ?>'; // Última fecha registrada

    const chartConfig = {
        type: 'line',
        data: { 
            labels: labels, 
            datasets: [
                {
                    label: 'Flujo (L/min)',
                    data: litrosMin,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                },
                {
                    label: 'Flujo Acumulado (L)',
                    data: flujoAcumulado,
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
                            minute: 'HH:mm',
                            hour: 'HH:mm',
                            day: 'MMM d',
                            week: 'MMM d'
                        }
                    }
                },
                y: { 
                    beginAtZero: false 
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    };

    // Función para cargar datos históricos
    async function loadHistoricalData() {
        try {
            const range = document.getElementById('historicalRange').value;
            const response = await fetch(`consumo-agua-historico.php?range=${range}`);
            
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            
            // Actualizar datos del gráfico histórico
            historicalChart.data.labels = data.labels;
            historicalChart.data.datasets[0].data = data.litros_min;
            historicalChart.data.datasets[1].data = data.flujo_acumulado;
            
            // Ajustar las escalas de tiempo según el rango
            const timeUnit = range === '24h' ? 'hour' : range === '7d' ? 'day' : 'week';
            historicalChart.options.scales.x.time.unit = timeUnit;
            
            // Actualizar el gráfico
            historicalChart.update();
        } catch (error) {
            console.error('Error al cargar datos históricos:', error);
            alert('Error al cargar los datos históricos');
        }
    }

    // Función para cargar datos en tiempo real
    async function loadRealTimeData() {
        try {
            const response = await fetch(`fetch_consumo.php?last_update=${encodeURIComponent(lastUpdate)}`);
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            
            if (data.newData) {
                // Actualiza los datos de la gráfica
                realTimeChart.data.labels.push(...data.newDates);
                realTimeChart.data.datasets[0].data.push(...data.newLitrosMin);
                realTimeChart.data.datasets[1].data.push(...data.newFlujoAcumulado);
                
                // Actualiza los valores actuales
                const lastLitrosMin = data.newLitrosMin[data.newLitrosMin.length - 1];
                const lastFlujoAcumulado = data.newFlujoAcumulado[data.newFlujoAcumulado.length - 1];
                
                document.getElementById('current-flow').innerText = `${lastLitrosMin.toFixed(2)} L/min`;
                document.getElementById('current-total').innerText = `${lastFlujoAcumulado.toFixed(2)} L`;

                // Mantener solo los últimos 50 puntos para mejor rendimiento
                if (realTimeChart.data.labels.length > 50) {
                    realTimeChart.data.labels = realTimeChart.data.labels.slice(-50);
                    realTimeChart.data.datasets.forEach(dataset => {
                        dataset.data = dataset.data.slice(-50);
                    });
                }

                realTimeChart.update('none'); // Actualización más eficiente
                lastUpdate = data.last_update;
            }
        } catch (error) {
            console.error('Error al cargar datos en tiempo real:', error);
        }
    }

    // Inicializar la actualización en tiempo real
    function initRealTimeUpdates() {
        // Primera carga inmediata
        loadRealTimeData();
        
        // Configurar intervalo de actualización
        const updateInterval = setInterval(loadRealTimeData, 2000);
        
        // Limpiar intervalo cuando la página se oculta
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(updateInterval);
            } else {
                // Reanudar actualizaciones cuando la página está visible
                loadRealTimeData();
                setInterval(loadRealTimeData, 2000);
            }
        });
    }

    // Función para inicializar los gráficos
    function initCharts() {
        const realTimeCtx = document.getElementById('realTimeChart').getContext('2d');
        const historicalCtx = document.getElementById('historicalChart').getContext('2d');
        
        // Inicializar gráfico en tiempo real
        realTimeChart = new Chart(realTimeCtx, { 
            ...chartConfig,
            options: {
                ...chartConfig.options,
                animation: {
                    duration: 0
                }
            }
        });
        
        // Inicializar gráfico histórico
        historicalChart = new Chart(historicalCtx, {
            ...chartConfig,
            options: {
                ...chartConfig.options,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'hour',
                            displayFormats: {
                                hour: 'HH:mm',
                                day: 'MMM d',
                                week: 'MMM d'
                            }
                        }
                    },
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });

        // Cargar datos históricos iniciales
        loadHistoricalData();
        
        // Iniciar actualizaciones en tiempo real
        initRealTimeUpdates();
    }

    // Inicializar gráficos al cargar la página
    window.addEventListener('load', initCharts);
</script>
</body>
</html>