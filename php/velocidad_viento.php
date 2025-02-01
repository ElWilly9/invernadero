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

$SQL = "SELECT * 
        FROM viento 
        ORDER BY fecha_registro DESC 
        LIMIT 100";
$consulta = mysqli_query($con, $SQL);

$vel_viento = [];
$dir_viento = [];
$fechas = [];

if($consulta && mysqli_num_rows($consulta) > 0){
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        $vel_viento[] = $resultado['velocidad_viento'];
        $dir_viento[] = $resultado['direccion_viento'];
        $fechas[] = $resultado['fecha_registro'];
    }
    
    // Invertir para orden cronológico correcto
    $vel_viento = array_reverse($vel_viento);
    $dir_viento = array_reverse($dir_viento);
    $fechas = array_reverse($fechas);
    
    $vel_actual = end($vel_viento);
    $dir_actual = end($dir_viento);
} else {
    $vel_actual = 0;
    $dir_actual = 0;
}

// Convertir los arrays a formato JSON para usarlos en JavaScript
$vel_viento_json = json_encode($vel_viento);
$dir_viento_json = json_encode($dir_viento);
$fechas_json = json_encode($fechas);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
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
            <h1 class="text-4xl font-bold text-gray-800">Monitoreo de la velocidad y dirección del viento</h1>
        </div>

        <div class="dashboard-container">
            <!-- Gráficos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Velocidad del Viento Actual</p>
                            <p class="text-3xl font-bold text-blue-600"><?= $vel_actual ?> km/h</p>
                        </div>
                        <i class="fas fa-wind text-4xl text-blue-400"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center">
                        <div>
                        <p class="text-sm text-gray-600">Dirección del Viento Actual</p>
                        <p class="text-3xl font-bold text-green-600"><?= $dir_actual ?></p>
                        </div>
                        <i class="fas fa-compass text-4xl text-green-400"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Velocidad del Viento</h2>
                    <canvas id="speedChart"></canvas>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Dirección del Viento</h2>
                    <canvas id="directionChart"></canvas>
                </div>
            </div>

            <!-- Tabla de datos -->
            <div class="bg-white p-4 rounded-lg shadow mt-6">
                <h2 class="text-xl font-bold mb-4">Registro de Velocidad y Dirección del Viento</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2">Fecha</th>
                            <th class="py-2">Velocidad (km/h)</th>
                            <th class="py-2">Dirección</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Mostrar los datos en la tabla
                        for ($i = 0; $i < count($fechas); $i++) {
                            echo "<tr>
                                    <td class='border px-4 py-2'>{$fechas[$i]}</td>
                                    <td class='border px-4 py-2'>{$vel_viento[$i]}</td>
                                    <td class='border px-4 py-2'>{$dir_viento[$i]}</td>
                                  </tr>";
                        }
                        ?>
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
                labels: <?= $fechas_json ?>,
                datasets: [{
                    label: 'Velocidad del Viento (km/h)',
                    data: <?= $vel_viento_json ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
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
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de dirección del viento
        
        <?php
        $SQL = "SELECT direccion_viento, 
        COUNT(*) as cantidad, 
        AVG(velocidad_viento) as velocidad_promedio 
        FROM viento 
        GROUP BY direccion_viento";
        $consulta2 = mysqli_query($con, $SQL);

        $vel_promedio = [];
        $direccion_viento = [];

        if($consulta && mysqli_num_rows($consulta) > 0){
        while ($resultado = mysqli_fetch_assoc($consulta)) {
        $vel_promedio[] = $resultado['velocidad_promedio'];
        $direccion_viento[] = $resultado['direccion_viento'];      
        }}
    
    // Invertir para orden cronológico correcto
       $vel_promedio = array_reverse($vel_promedio);
       $direccion_viento = array_reverse($direccion_viento);

// Convertir los arrays a formato JSON para usarlos en JavaScript
        $vel_promedio_json = json_encode($vel_promedio);
        $direccion_viento_json = json_encode($direccion_viento);
        ?>
        
        const directionCtx = document.getElementById('directionChart').getContext('2d');
        new Chart(directionCtx, {
            type: 'bar',
            data: {
                labels: <?= $direccion_viento_json ?>,
                datasets: [{
                    label: 'Dirección del Viento',
                    data: <?= $vel_promedio_json ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
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
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>