<?php
session_start();
include_once 'conexion.php';

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

// Obtener últimos 100 registros
$SQL = "SELECT temperatura_ambiente, humedad_ambiente, fecha_registro 
        FROM temp_hum_amb 
        ORDER BY fecha_registro DESC 
        LIMIT 100";
$consulta = mysqli_query($con, $SQL);

$temp = [];
$hum = [];
$fechas = [];

if($consulta && mysqli_num_rows($consulta) > 0){
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        $temp[] = $resultado['temperatura_ambiente'];
        $hum[] = $resultado['humedad_ambiente'];
        $fechas[] = $resultado['fecha_registro'];
    }
    
    // Invertir para orden cronológico correcto
    $temp = array_reverse($temp);
    $hum = array_reverse($hum);
    $fechas = array_reverse($fechas);
    
    $temp_actual = end($temp);
    $hum_actual = end($hum);
} else {
    $temp_actual = 0;
    $hum_actual = 0;
}

// Convertir a JSON para JavaScript
$fechas_json = json_encode($fechas);
$temp_json = json_encode($temp);
$hum_json = json_encode($hum);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard en Tiempo Real - DHT11</title>
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
                <div class="text-xl font-bold text-gray-800">Monitor DHT11</div>
                <a href="cerrar_sesion.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Tarjetas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="glass-card p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Temperatura Actual</p>
                        <p class="text-3xl font-bold text-blue-600 rounded p-2" id="current-temp"><?= $temp_actual ?>°C</p>
                    </div>
                    <i class="fas fa-thermometer-half text-4xl text-blue-400"></i>
                </div>
            </div>

            <div class="glass-card p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Humedad Actual</p>
                        <p class="text-3xl font-bold text-green-600 rounded p-2" id="current-hum"><?= $hum_actual ?>%</p>
                    </div>
                    <i class="fas fa-tint text-4xl text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Gráfica de Temperatura -->
        <div class="glass-card p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Temperatura</h3>
                <select id="tempChartType" class="px-4 py-2 border rounded-lg">
                    <option value="line">Gráfico de Líneas</option>
                    <option value="bar">Gráfico de Barras</option>
                </select>
            </div>
            <div class="chart-container">
                <canvas id="tempChart"></canvas>
            </div>
        </div>

        <!-- Gráfica de Humedad -->
        <div class="glass-card p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Humedad</h3>
                <select id="humChartType" class="px-4 py-2 border rounded-lg">
                    <option value="line">Gráfico de Líneas</option>
                    <option value="bar">Gráfico de Barras</option>
                </select>
            </div>
            <div class="chart-container">
                <canvas id="humChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        let tempChart, humChart;
        let lastUpdate = '<?= end($fechas) ?>';

        // Configuración común para los gráficos
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 750
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'minute',
                        displayFormats: {
                            minute: 'HH:mm'
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        };

        // Configuración específica para barras
        const barOptions = {
            datasets: {
                bar: {
                    barThickness: 20,
                    maxBarThickness: 30,
                    borderWidth: 2
                }
            }
        };

        // Configuración para el gráfico de temperatura
        const tempConfig = {
            type: 'line',
            data: {
                labels: <?= $fechas_json ?>,
                datasets: [{
                    label: 'Temperatura °C',
                    data: <?= $temp_json ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Temperatura (°C)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        };

        // Configuración para el gráfico de humedad
        const humConfig = {
            type: 'line',
            data: {
                labels: <?= $fechas_json ?>,
                datasets: [{
                    label: 'Humedad %',
                    data: <?= $hum_json ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    y: {
                        ...commonOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Humedad (%)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        };

        // Inicializar gráficos
        function initCharts() {
            const tempCtx = document.getElementById('tempChart').getContext('2d');
            const humCtx = document.getElementById('humChart').getContext('2d');
            
            tempChart = new Chart(tempCtx, tempConfig);
            humChart = new Chart(humCtx, humConfig);
        }

        // Función para animar la actualización de valores
        function animateValue(element) {
            element.classList.add('value-update');
            setTimeout(() => {
                element.classList.remove('value-update');
            }, 1000);
        }

        // Actualizar datos
        async function updateData() {
            try {
                const response = await fetch(`fetch_data.php?last_update=${lastUpdate}`);
                const data = await response.json();

                if(data.newData) {
                    // Actualizar valores actuales con animación
                    const tempElement = document.getElementById('current-temp');
                    const humElement = document.getElementById('current-hum');
                    
                    tempElement.textContent = data.current.temp + '°C';
                    humElement.textContent = data.current.hum + '%';
                    
                    animateValue(tempElement);
                    animateValue(humElement);

                    // Actualizar gráficos
                    tempChart.data.labels.push(...data.newDates);
                    tempChart.data.datasets[0].data.push(...data.newTemp);
                    if(tempChart.data.labels.length > 50) {
                        tempChart.data.labels.splice(0, tempChart.data.labels.length - 50);
                        tempChart.data.datasets[0].data.splice(0, tempChart.data.datasets[0].data.length - 50);
                    }
                    tempChart.update('none'); // Actualización sin animación para suavidad

                    humChart.data.labels.push(...data.newDates);
                    humChart.data.datasets[0].data.push(...data.newHum);
                    if(humChart.data.labels.length > 50) {
                        humChart.data.labels.splice(0, humChart.data.labels.length - 50);
                        humChart.data.datasets[0].data.splice(0, humChart.data.datasets[0].data.length - 50);
                    }
                    humChart.update('none'); // Actualización sin animación para suavidad

                    lastUpdate = data.last_update;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Cambiar tipo de gráfico de temperatura
        document.getElementById('tempChartType').addEventListener('change', (e) => {
            if(tempChart) {
                tempChart.destroy();
                tempConfig.type = e.target.value;
                if(e.target.value === 'bar') {
                    tempConfig.options = {...tempConfig.options, ...barOptions};
                }
                tempChart = new Chart(document.getElementById('tempChart').getContext('2d'), tempConfig);
            }
        });

        // Cambiar tipo de gráfico de humedad
        document.getElementById('humChartType').addEventListener('change', (e) => {
            if(humChart) {
                humChart.destroy();
                humConfig.type = e.target.value;
                if(e.target.value === 'bar') {
                    humConfig.options = {...humConfig.options, ...barOptions};
                }
                humChart = new Chart(document.getElementById('humChart').getContext('2d'), humConfig);
            }
        });

        // Inicializar y actualizar cada 2 segundos
        initCharts();
        setInterval(updateData, 2000);
    </script>
</body>
</html>