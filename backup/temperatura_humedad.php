<?php
session_start();
include_once 'conexion.php';

if(!isset($_SESSION['usuario'])){
    echo '
        <script>
            alert("Por favor debes de iniciar sesion primero");
            window.location = "../index.php";
        </script>
    ';
    session_destroy();
    die();
}

// Consulta para obtener los datos
$SQL = "SELECT * FROM temp_hum_amb ORDER BY fecha_registro";
$consulta = mysqli_query($con, $SQL);

$temp = [];
$hum = [];
$fechas = [];
$datos_amchart = [];

while ($resultado = mysqli_fetch_array($consulta)) {
    // Datos para los gráficos de Chart.js
    $temp[] = floatval($resultado['temperatura_ambiente']);
    $hum[] = floatval($resultado['humedad_ambiente']);
    $fechas[] = date('H:i', strtotime($resultado['fecha_registro']));
    
    // Datos para AMCharts
    $datos_amchart[] = [
        "date" => $resultado['fecha_registro'],
        "value" => floatval($resultado['temperatura_ambiente'])
    ];
}

// Convertir a JSON para usar en JavaScript
$fecha_json = json_encode($fechas);
$temp_json = json_encode($temp);
$hum_json = json_encode($hum);
$amchart_json = json_encode($datos_amchart);

// Obtener valores actuales (último registro)
$temp_actual = !empty($temp) ? end($temp) : 0;
$hum_actual = !empty($hum) ? end($hum) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo de Temperatura y Humedad</title>
	<link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e2e8f0 0%, #edf2f7 100%);
        }
        .metric-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .temperature-gradient {
            background: linear-gradient(45deg, #3b82f6, #60a5fa);
        }
        .humidity-gradient {
            background: linear-gradient(45deg, #10b981, #34d399);
        }
        h1, h2 {
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="../bienvenido.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>Volver al menú principal
                    </a>
                </div>
                <div class="flex items-center">
                    <a href="php/cerrar_sesion.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800">Monitoreo Ambiental</h1>
            <p class="text-gray-600 mt-2">Datos en tiempo real de temperatura y humedad</p>
        </div>

        <!-- Current Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="metric-card temperature-gradient text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Temperatura Actual</p>
                        <p class="text-4xl font-bold mt-2"><?= $temp_actual ?>°C</p>
        			</div>
        			<i class="fas fa-thermometer-half text-4xl opacity-90"></i>
    			</div>
			</div>

			<div class="metric-card humidity-gradient text-white p-6">
    			<div class="flex items-center justify-between">
        			<div>
            			<p class="text-sm opacity-90">Humedad Actual</p>
            			<p class="text-4xl font-bold mt-2"><?= $hum_actual ?>%</p>
        			</div>
        			<i class="fas fa-tint text-4xl opacity-90"></i>
    			</div>
			</div>
        </div>

        <!-- Gráfico Principal -->
        <div class="metric-card p-6 mb-8">
    		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        		<div>
            		<h2 class="text-xl font-semibold text-gray-800 mb-4">Historial de Temperatura</h2>
            		<canvas id="historial"></canvas>
        		</div>
        		<div>
            		<h2 class="text-xl font-semibold text-gray-800 mb-4">Historial de Humedad</h2>
            		<canvas id="historial_hum"></canvas>
        		</div>
    		</div>
		</div>

        <!-- Gráficos Secundarios -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="metric-card p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Distribución de Temperatura</h2>
                <canvas id="tempChart"></canvas>
            </div>
            
            <div class="metric-card p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Distribución de Humedad</h2>
                <canvas id="humChart"></canvas>
            </div>
        </div>
    </div>

    <script>
    // Configuración mejorada del gráfico AMCharts
    am5.ready(function() {
        const root = am5.Root.new("chartdiv");
        root.setThemes([am5themes_Animated.new(root)]);

        const chart = root.container.children.push(am5xy.XYChart.new(root, {
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            pinchZoomX: true
        }));

        // Ejes
        const xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
            baseInterval: { timeUnit: "minute", count: 1 },
            renderer: am5xy.AxisRendererX.new(root, {
                stroke: am5.color(0x64748b)
            })
        }));

        const yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
            renderer: am5xy.AxisRendererY.new(root, {
                stroke: am5.color(0x64748b)
            })
        }));

        // Serie de Temperatura
        const tempSeries = chart.series.push(am5xy.LineSeries.new(root, {
            name: "Temperatura",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "value",
            valueXField: "date",
            stroke: am5.color(0x3b82f6),
            tooltip: am5.Tooltip.new(root, {
                labelText: "{valueY}°C"
            })
        }));

        tempSeries.data.setAll(<?= $amchart_json ?>);

        // Animaciones
        chart.appear(1000, 100);
        tempSeries.appear();
    });

    // Gráfico de Temperatura con Chart.js
    const tempCtx = document.getElementById('historial').getContext('2d');
	new Chart(tempCtx, {
    type: 'line',
    data: {
        labels: <?= $fecha_json ?>,
        datasets: [{
            label: 'Temperatura °C',
            data: <?= $temp_json ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        	}]
    		},
	});

// Gráfico de Humedad
	const humCtx = document.getElementById('historial_hum').getContext('2d');
	new Chart(humCtx, {
    	type: 'line',
    	data: {
     		labels: <?= $fecha_json ?>,
        	datasets: [{
            	label: 'Humedad %',
            	data: <?= $hum_json ?>,
            	borderColor: '#10b981',
            	backgroundColor: 'rgba(16, 185, 129, 0.1)',
            	tension: 0.4
        	}]
    	},
	// ... resto de las opciones
	});
    </script>
</body>
</html>