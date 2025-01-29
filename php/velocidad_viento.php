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

// Emulación de datos
$speed = [12.5, 15.3, 10.8, 14.2, 13.7, 11.9, 16.4, 14.8, 12.1, 13.5, 15.0, 14.3, 12.7, 13.9, 15.6, 14.1, 13.2, 12.8, 14.5, 15.1, 13.4, 12.6, 14.0, 15.2];
$direction = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];

// Convertir los arrays a formato JSON para usarlos en JavaScript
$speed_json = json_encode($speed);
$direction_json = json_encode($direction);

// Simulación de datos para los canales
$channels = [
    ['id' => 1, 'speed' => 12.5, 'direction' => 'N', 'last_update' => '9m ago'],
    ['id' => 2, 'speed' => 15.3, 'direction' => 'NE', 'last_update' => '9m ago'],
    ['id' => 3, 'speed' => 10.8, 'direction' => 'E', 'last_update' => '9m ago'],
    ['id' => 4, 'speed' => 14.2, 'direction' => 'SE', 'last_update' => '9m ago'],
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        }
        .header {
            text-align: center;
            padding: 20px 0;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .channel-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .channel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        h1, h2 {
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>

<body>
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
            <h1 class="text-4xl font-bold text-gray-800">Monitoreo de la velocidad y dirección del viento</h1>
    </div>

    <div class="dashboard-container">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Canales de velocidad del viento -->
            <?php foreach ($channels as $channel): ?>
            <div class="channel-card p-4">
                <div class="flex items-center">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='%2300BFFF'%3E%3Cpath d='M12 3C11.0572 3 10.2 3.85719 10.2 4.8V15.4C9.45 15.9 9 16.7 9 17.6C9 19.2 10.3431 20.5 12 20.5C13.6569 20.5 15 19.2 15 17.6C15 16.7 14.55 15.9 13.8 15.4V4.8C13.8 3.85719 12.9428 3 12 3Z'/%3E%3C/svg%3E" 
                     alt="Velocidad del Viento" class="w-6 h-6 mr-2">
                    <div>
                        <h3 class="font-medium">Canal <?php echo $channel['id']; ?></h3>
                        <p class="text-sm text-gray-500">Última actualización <?php echo $channel['last_update']; ?></p>
                    </div>
                </div>
                <div class="speed <?php echo $channel['speed'] > 10 ? 'text-red-600' : 'text-blue-600'; ?> text-2xl font-bold">
                    <?php echo $channel['speed']; ?> km/h
                </div>
                <div class="direction text-gray-500">
                    Dirección: <?php echo $channel['direction']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Velocidad del Viento</h2>
                <canvas id="speedChart"></canvas>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Distribución de Velocidad del Viento</h2>
                <canvas id="speedDistributionChart"></canvas>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Dirección y Velocidad del Viento</h2>
                <canvas id="windDirectionChart"></canvas>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Tabla de Velocidad del Viento</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2">Canal</th>
                            <th class="py-2">Velocidad (km/h)</th>
                            <th class="py-2">Dirección</th>
                            <th class="py-2">Última Actualización</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($channels as $channel): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $channel['id']; ?></td>
                            <td class="border px-4 py-2"><?php echo $channel['speed']; ?></td>
                            <td class="border px-4 py-2"><?php echo $channel['direction']; ?></td>
                            <td class="border px-4 py-2"><?php echo $channel['last_update']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Gráfico de velocidad del viento
    const speedCtx = document.getElementById('speedChart').getContext('2d');
    new Chart(speedCtx, {
        type: 'line',
        data: {
            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
            datasets: [
                {
                    label: 'Velocidad del Viento',
                    data: <?php echo $speed_json; ?>,
                    borderColor: '#2563EB',
                    tension: 0.4
                }
            ]
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

    // Gráfico de distribución de velocidad del viento
    const speedDistributionCtx = document.getElementById('speedDistributionChart').getContext('2d');
    new Chart(speedDistributionCtx, {
        type: 'bar',
        data: {
            labels: ['0-5 km/h', '5-10 km/h', '10-15 km/h', '15-20 km/h', '20-25 km/h'],
            datasets: [{
                label: 'Frecuencia',
                data: [2, 5, 8, 6, 3], // Datos de ejemplo
                backgroundColor: '#34D399'
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
                        text: 'Frecuencia'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Rango de Velocidad'
                    }
                }
            }
        }
    });

    // Gráfico de dirección y velocidad del viento
    const windDirectionCtx = document.getElementById('windDirectionChart').getContext('2d');
    new Chart(windDirectionCtx, {
        type: 'radar',
        data: {
            labels: <?php echo $direction_json; ?>,
            datasets: [{
                label: 'Velocidad del Viento',
                data: <?php echo $speed_json; ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
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
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 25,
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        }
    });
    </script>
</body>
</html>