<?php
session_start();

include_once 'conexion.php';

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

$SQL = "SELECT * FROM temp_hum_amb";
$consulta = mysqli_query($con, $SQL);
$temp = [];
$hum = [];

// Obtener los datos de la consulta
while ($resultado = mysqli_fetch_array($consulta)) {
    $temp[] = $resultado['temperatura_ambiente'];
    $hum[] = $resultado['humedad_ambiente'];
}

// Convertir los arrays a formato JSON para usarlos en JavaScript
$temp_json = json_encode($temp);
$hum_json = json_encode($hum);


// Simulación de datos - En un caso real, estos vendrían de tu base de datos o sensores
$channels = [
    ['id' => 1, 'temp' => 20.90, 'last_update' => '9m ago'],
    ['id' => 2, 'temp' => 18.99, 'last_update' => '9m ago'],
    ['id' => 3, 'temp' => 21.11, 'last_update' => '9m ago'],
    ['id' => 4, 'temp' => 20.37, 'last_update' => '9m ago'],
];

$humidity = 46;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo de Temperatura y Humedad</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <!-- Ajustar la ruta del CSS si es necesario -->
    <link rel="stylesheet" href="../assets/style2.css">
</head>

<body class="bg-gradient-to-br from-[#76c442] to-[#a2db4f]">
    <div class="header">
        <div class="dashboard-container">
            <h1 class="text-3xl font-bold">Monitoreo de Temperatura y Humedad</h1>
            <div class="mt-4">
                <a href="../bienvenido.php" class="back-button">← Volver al menú principal</a>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Canales de temperatura -->
            <?php foreach ($channels as $channel): ?>
            <div class="channel-card">
                <div class="flex items-center">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='%23DC2626'%3E%3Cpath d='M12 3C11.0572 3 10.2 3.85719 10.2 4.8V15.4C9.45 15.9 9 16.7 9 17.6C9 19.2 10.3431 20.5 12 20.5C13.6569 20.5 15 19.2 15 17.6C15 16.7 14.55 15.9 13.8 15.4V4.8C13.8 3.85719 12.9428 3 12 3Z'/%3E%3C/svg%3E" 
                     alt="Temperatura" class="w-6 h-6 mr-2">
                    <div>
                        <h3 class="font-medium">Channel <?php echo $channel['id']; ?></h3>
                        <p class="text-sm text-gray-500">Last update <?php echo $channel['last_update']; ?></p>
                    </div>
                </div>
                <div class="temperature <?php echo $channel['temp'] > 20 ? 'text-red-600' : 'text-blue-600'; ?>">
                    <?php echo $channel['temp']; ?> °C
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Temperature</h2>
                <canvas id="temperatureChart"></canvas>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Humidity</h2>
                <div class="humidity-gauge">
                    <canvas id="humidityGauge"></canvas>
                </div>
                <div class="text-center text-2xl font-bold"><?php echo $humidity; ?>%</div>
            </div>
        </div>
    </div>

    <script>
    // Gráfico de temperatura
    const tempCtx = document.getElementById('temperatureChart').getContext('2d');
    new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: ['temperatura_ambiente', 'humedad_ambiente'],
            datasets: [
                {
                    label: 'Datos de Temperatura,
                    data: [<?php echo $temp_json; ?>,
                    borderColor: '#2563EB',
                    tension: 0.4
                },
                {
                    label: 'Datos de Humedad',
                    data: [<?php echo $hum_json; ?>,
                    borderColor: '#059669',
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
                    beginAtZero: false,
                    min: 15,
                    max: 25
                }
            }
        }
    });

    // Medidor de humedad
    const humidityCtx = document.getElementById('humidityGauge').getContext('2d');
    new Chart(humidityCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [<?php echo $hum; ?>, 100 - <?php echo $hum; ?>],
                backgroundColor: ['#3B82F6', '#E5E7EB'],
                borderWidth: 0
            }]
        },
        options: {
            circumference: 180,
            rotation: -90,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    </script>
</body>
</html>