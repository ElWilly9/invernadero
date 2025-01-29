<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Monitoreo de Energía</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Dashboard de Monitoreo de Energía</h1>
        <div class="row">
            <div class="col-md-12">
                <canvas id="consumoChart"></canvas>
            </div>
        </div>
    </div>

    <?php
    // Datos de prueba (simulando una base de datos)
    $data = [
        ["fecha" => "2023-10-01 12:00:00", "consumo" => 15.5, "voltaje" => 220, "amperaje" => 5.2],
        ["fecha" => "2023-10-02 12:00:00", "consumo" => 20.3, "voltaje" => 218, "amperaje" => 5.5],
        ["fecha" => "2023-10-03 12:00:00", "consumo" => 18.7, "voltaje" => 219, "amperaje" => 5.3],
        ["fecha" => "2023-10-04 12:00:00", "consumo" => 22.1, "voltaje" => 221, "amperaje" => 5.6],
    ];
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Obtener los datos de PHP
            const data = <?php echo json_encode($data); ?>;

            // Preparar los datos para Chart.js
            const labels = data.map(item => item.fecha); // Fechas como strings
            const consumo = data.map(item => item.consumo); // Valores de consumo
            const voltaje = data.map(item => item.voltaje); // Valores de voltaje
            const amperaje = data.map(item => item.amperaje); // Valores de amperaje

            // Configurar la gráfica
            const ctx = document.getElementById('consumoChart').getContext('2d');
            const consumoChart = new Chart(ctx, {
                type: 'line', // Tipo de gráfica
                data: {
                    labels: labels, // Eje X (fechas)
                    datasets: [
                        {
                            label: 'Consumo (kWh)', // Leyenda para consumo
                            data: consumo, // Datos de consumo
                            borderColor: 'rgba(75, 192, 192, 1)', // Color de la línea
                            backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color de relleno
                            borderWidth: 1, // Ancho de la línea
                            yAxisID: 'y' // Eje Y para consumo
                        },
                        {
                            label: 'Voltaje (V)', // Leyenda para voltaje
                            data: voltaje, // Datos de voltaje
                            borderColor: 'rgba(255, 99, 132, 1)', // Color de la línea
                            backgroundColor: 'rgba(255, 99, 132, 0.2)', // Color de relleno
                            borderWidth: 1, // Ancho de la línea
                            yAxisID: 'y1' // Eje Y para voltaje
                        },
                        {
                            label: 'Amperaje (A)', // Leyenda para amperaje
                            data: amperaje, // Datos de amperaje
                            borderColor: 'rgba(54, 162, 235, 1)', // Color de la línea
                            backgroundColor: 'rgba(54, 162, 235, 0.2)', // Color de relleno
                            borderWidth: 1, // Ancho de la línea
                            yAxisID: 'y2' // Eje Y para amperaje
                        }
                    ]
                },
                options: {
                    scales: {
                        x: {
                            type: 'time', // Eje X es de tipo tiempo
                            time: {
                                unit: 'day', // Unidad de tiempo (día)
                                tooltipFormat: 'YYYY-MM-DD HH:mm:ss', // Formato del tooltip
                                displayFormats: {
                                    day: 'YYYY-MM-DD' // Formato de visualización
                                }
                            },
                            title: {
                                display: true,
                                text: 'Fecha' // Título del eje X
                            }
                        },
                        y: {
                            type: 'linear', // Eje Y lineal para consumo
                            display: true,
                            position: 'left', // Posición a la izquierda
                            title: {
                                display: true,
                                text: 'Consumo (kWh)' // Título del eje Y
                            }
                        },
                        y1: {
                            type: 'linear', // Eje Y lineal para voltaje
                            display: true,
                            position: 'right', // Posición a la derecha
                            title: {
                                display: true,
                                text: 'Voltaje (V)' // Título del eje Y
                            },
                            grid: {
                                drawOnChartArea: false // No dibujar líneas de grid para este eje
                            }
                        },
                        y2: {
                            type: 'linear', // Eje Y lineal para amperaje
                            display: true,
                            position: 'right', // Posición a la derecha
                            title: {
                                display: true,
                                text: 'Amperaje (A)' // Título del eje Y
                            },
                            grid: {
                                drawOnChartArea: false // No dibujar líneas de grid para este eje
                            }
                        }
                    },
                    responsive: true, // Gráfica responsive
                    plugins: {
                        legend: {
                            display: true, // Mostrar leyenda
                            position: 'top' // Posición de la leyenda
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>