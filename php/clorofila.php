<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroVision</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .chlorophyll-bg {
            background: linear-gradient(135deg, #e6f4ea 20%, #f8fcf3 80%);
        }
        .chlorophyll-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(46, 125, 50, 0.1);
        }
        .chlorophyll-gradient {
            background: linear-gradient(135deg, #2e7d32 0%, #66bb6a 100%);
        }
        .leaf-marker {
            background: url('https://img.icons8.com/ios-filled/50/228B22/leaf.png') no-repeat center;
            background-size: contain;
            width: 40px;
            height: 40px;
        }
    </style>
</head>
<body class="chlorophyll-bg min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Parte izquierda -->
            <div class="flex items-center space-x-4">
                <a href="../bienvenido.php" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al menú principal
                </a>
            </div>

            <!-- Parte derecha -->
            <div class="flex items-center space-x-4">
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
            <h1 class="text-4xl font-bold text-gray-800">Monitoreo de la clorofila en las plantas</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Nivel Actual (SPAD)</p>
                        <p class="text-4xl font-bold text-green-800 mt-2">42.6</p>
                    </div>
                    <i class="fas fa-seedling text-3xl text-green-600"></i>
                </div>
            </div>
            
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Estado de Salud</p>
                        <p class="text-2xl font-bold text-green-800 mt-2">Óptimo</p>
                        <span class="text-sm text-green-600">Rango ideal: 35-50 SPAD</span>
                    </div>
                    <i class="fas fa-heartbeat text-3xl text-red-400"></i>
                </div>
            </div>
            
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Variación Diaria</p>
                        <p class="text-2xl font-bold text-green-800 mt-2">+2.4%</p>
                        <span class="text-sm text-green-600">vs. ayer</span>
                    </div>
                    <i class="fas fa-chart-line text-3xl text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Gráfico Principal -->
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-green-800 mb-4">Tendencia de Clorofila (SPAD)</h2>
                <canvas id="mainChart"></canvas>
            </div>

            <!-- Tabla de Valores -->
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-green-800 mb-4">Últimas Mediciones</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-green-800 border-b">
                                <th class="pb-3">Hora</th>
                                <th class="pb-3">Valor SPAD</th>
                                <th class="pb-3">Estado</th>
                                <th class="pb-3">Zona</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="py-3">08:00</td>
                                <td class="text-green-700 font-medium">42.6</td>
                                <td><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Óptimo</span></td>
                                <td>Zona A</td>
                            </tr>
                            <!-- Más filas... -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Comparación de Especies -->
            <div class="chlorophyll-card rounded-xl p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-green-800 mb-4">Comparación por Especie</h2>
                <canvas id="speciesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Datos ficticios
        const timeLabels = ['6:00', '8:00', '10:00', '12:00', '14:00', '16:00', '18:00'];
        const spadData = [38.2, 42.6, 45.1, 47.3, 46.8, 44.2, 41.5];
        
        // Gráfico principal
        const ctx = document.getElementById('mainChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: timeLabels,
                datasets: [{
                    label: 'Nivel de Clorofila (SPAD)',
                    data: spadData,
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46, 125, 50, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        title: { text: 'Unidades SPAD', display: true }
                    }
                }
            }
        });

        // Mapa Leaflet
        const map = L.map('map').setView([4.710989, -74.072092], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        // Marcadores ficticios
        L.marker([4.710989, -74.072092], { icon: L.divIcon({ className: 'leaf-marker' }) }).addTo(map)
            .bindPopup('Zona A: 42.6 SPAD');

        // Gráfico de comparación
        const speciesCtx = document.getElementById('speciesChart').getContext('2d');
        new Chart(speciesCtx, {
            type: 'bar',
            data: {
                labels: ['Maíz', 'Trigo', 'Arroz', 'Soja'],
                datasets: [{
                    label: 'Nivel Promedio SPAD',
                    data: [45.2, 38.7, 42.1, 40.5],
                    backgroundColor: ['#2e7d32', '#66bb6a', '#a5d6a7', '#c8e6c9']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { title: { text: 'SPAD', display: true } }
                }
            }
        });
    </script>
</body>
</html>