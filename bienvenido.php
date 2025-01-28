<?php

    session_start();

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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <style>
        body {
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg, #76c442, #a2db4f);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2rem;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .panel-central {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 40px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
        }

        .box {
            background-color: #007bff;
            color: white !important;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            font-family: 'Montserrat', Arial, sans-serif;
        }

        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .box:nth-child(1) { background-color: #007bff; }
        .box:nth-child(2) { background-color: #28a745; }
        .box:nth-child(3) { background-color: #ffc107; }
        .box:nth-child(4) { background-color: #dc3545; }

        .logout-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }

        .footer img {
            width: 80px;
            height: auto;
            margin-top: 20px;
        }

    </style>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <a href="php/cerrar_sesion.php" class="logout-button">Cerrar Sesión</a>
    <div class="header">
        <h1>Bienvenido AgroVision, el sistema de monitoreo del invernadero Unal</h1>
    </div>
    <div class="panel-central">
        <a href="temperatura_humedad.php" class="box">Temperatura y Humedad</a>
        <a href="velocidad_viento.php" class="box">Velocidad del viento</a>
        <a href="dash/index.html" class="box">Opción 3</a>
        <a href="opcion4.php" class="box">Opción 4</a>
    </div>
    <div class="footer">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <canvas id="myChart"></canvas>
    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
            datasets: [{
                label: 'Temperatura',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
        });
        </script>
        <img src="https://unal.edu.co/fileadmin/templates/images/logo_unal_black.png" alt="Logo UNAL">
    </div>
</body>
</html>
