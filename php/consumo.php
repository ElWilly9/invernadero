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
    <title>Monitoreo de Consumo de Agua</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<style>
.header {
    background: linear-gradient(to right,rgb(75, 116, 230),rgb(102, 194, 236));
    color: white;
    padding: 1rem;
    margin-bottom: 2rem;
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
<body class="bg-gradient-to-br from-[#76c442] to-[#a2db4f] min-h-screen">
    <div class="header">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">Monitoreo de Consumo de Agua</h1>
            <div class="mt-4">
                <a href="../bienvenido.php" class="back-button">← Volver al menú principal</a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4">
        <!-- Métricas principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="metric-card">
                <div class="flow-indicator mb-4">
                    <div class="water-wave"></div>
                </div>
                <h3 class="text-lg font-semibold text-center text-gray-800">Flujo Actual</h3>
                <p class="text-3xl font-bold text-center text-blue-600"><?php echo $current_flow; ?> L/min</p>
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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Consumo por Hora</h3>
                <canvas id="hourlyChart"></canvas>
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
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($hourly_data, 'hora')); ?>,
            datasets: [{
                label: 'Consumo de Agua (L)',
                data: <?php echo json_encode(array_column($hourly_data, 'consumo')); ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Litros'
                    }
                }
            }
        }
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