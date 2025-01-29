<?php
session_start();
include_once 'conexion.php';

// Datos emulados
$speed = [12.5, 15.3, 10.8, 14.2, 13.7, 11.9, 16.4, 14.8, 12.1, 13.5, 15.0, 14.3];
$speed_json = json_encode($speed);

// Datos actuales emulados
$current_data = [
    'speed' => 15.3,
    'direction' => 45,
    'gust' => 22.1
];

// Datos de las últimas horas
$hourly_data = [
    ['hour' => '09:00', 'speed' => 12.5, 'direction' => 'NE', 'gust' => 18.2],
    ['hour' => '10:00', 'speed' => 15.3, 'direction' => 'N', 'gust' => 20.1],
    ['hour' => '11:00', 'speed' => 10.8, 'direction' => 'NW', 'gust' => 16.4],
    ['hour' => '12:00', 'speed' => 14.2, 'direction' => 'N', 'gust' => 19.8],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo de Velocidad del Viento</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <style>
        .wind-direction {
            width: 200px;
            height: 200px;
            border: 3px solid #e5e7eb;
            border-radius: 50%;
            position: relative;
            margin: 20px auto;
        }
        .arrow {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 80%;
            height: 4px;
            background: #3b82f6;
            transform: translate(-50%, -50%) rotate(<?php echo $current_data['direction']; ?>deg);
            transform-origin: center;
        }
        .arrow::before {
            content: '';
            position: absolute;
            right: -10px;
            top: -8px;
            border-left: 20px solid #3b82f6;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Monitoreo de Velocidad del Viento</h1>
            <a href="../bienvenido.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                ← Volver al menú
            </a>
        </div>

        <!-- Tarjetas principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Velocidad actual -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Velocidad Actual</h3>
                <p class="text-4xl font-bold text-blue-600"><?php echo $current_data['speed']; ?> km/h</p>
                <p class="text-sm text-gray-500 mt-2">Actualizado hace 5 min</p>
            </div>

            <!-- Dirección del viento -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Dirección del Viento</h3>
                <div class="wind-direction">
                    <div class="arrow"></div>
                </div>
                <p class="text-center text-gray-600">45° NE</p>
            </div>

            <!-- Ráfaga máxima -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Ráfaga Máxima</h3>
                <p class="text-4xl font-bold text-red-500"><?php echo $current_data['gust']; ?> km/h</p>
                <p class="text-sm text-gray-500 mt-2">En la última hora</p>
            </div>
        </div>

        <!-- Gráficos y tabla -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Gráfico -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Velocidad del Viento - Últimas 12 horas</h3>
                <canvas id="windChart" height="300"></canvas>
            </div>

            <!-- Tabla de datos -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Registro de Mediciones</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Velocidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dirección</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ráfaga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($hourly_data as $data): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo $data['hour']; ?></td>
                                <td class="px-6 py-4"><?php echo $data['speed']; ?> km/h</td>
                                <td class="px-6 py-4"><?php echo $data['direction']; ?></td>
                                <td class="px-6 py-4"><?php echo $data['gust']; ?> km/h</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuración del gráfico
        const ctx = document.getElementById('windChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['6:00', '7:00', '8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
                datasets: [{
                    label: 'Velocidad del Viento (km/h)',
                    data: <?php echo $speed_json; ?>,
                    borderColor: '#3b82f6',
                    tension: 0.4,
                    fill: false
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
                            text: 'Velocidad (km/h)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>