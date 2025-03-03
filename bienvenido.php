<?php
session_start();
if(!isset($_SESSION['usuario'])){
    echo '
        <script>
            alert("Por favor debes de iniciar sesion primero");
            window.location = "index.php";
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
    <title>AgroVision</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos generales */
        body {
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg, #76c442, #a2db4f, #4b74e6, #66c2ec);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Prevenir iconos flotantes */
        body > i {
            display: none !important;
        }

        /* Animación del degradado */
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 40px;
            margin-top: 80px;
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            margin: 0;
            background: linear-gradient(to right, #76c442, #a2db4f);
            padding: 10px;
            border-radius: 8px;
        }

        .header p {
            font-size: 1.2rem;
            color: #e0e0e0;
            margin-top: 10px;
        }

        /* Panel de tarjetas */
        .panel-central {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            width: 90%;
            max-width: 1200px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            align-items: stretch;
            position: relative;
            z-index: 1;
        }

        .box {
            background-color: #fff;
            color: #333;
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #e0e0e0;
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            background-color: #f0f0f0;
        }

        .box i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            margin-right: 30px;
            margin: center;
            display: block;
            position: relative;
            z-index: 1;
        }

        .box h2 {
            font-size: 1.5rem;
            margin: 0;
            color: #333;
            position: relative;
            z-index: 1;
        }

        .box p {
            font-size: 1rem;
            color: #666;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        /* Colores personalizados para los iconos */
        .fa-thermometer-half { color: #ff4500; }
        .fa-wind { color: #87ceeb; }
        .fa-tint { color: #1e90ff; }
        .fa-leaf { color: #2d8a39; }

        /* Botón de cerrar sesión */
        .logout-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s;
            z-index: 1000;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        /* Pie de página */
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        .footer img {
            width: 80px;
            height: auto;
            margin-top: 20px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            .panel-central {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            .box {
                padding: 20px;
            }
            .box i {
                font-size: 2rem;
            }
            .box h2 {
                font-size: 1.rem;
            }
            .box p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <a href="php/cerrar_sesion.php" class="logout-button">Cerrar Sesión</a>
    <div class="header">
        <h1>Bienvenido a AgroVision</h1>
        <p>El sistema de monitoreo del invernadero Unal</p>
    </div>
    <div class="panel-central">
        <a href="php/temperatura_humedad.php" class="box">
            <i class="fas fa-thermometer-half"></i>
            <h2>Temperatura y Humedad</h2>
            <p>Monitoreo en tiempo real de las condiciones climáticas.</p>
        </a>
        <a href="php/velocidad_viento.php" class="box">
            <i class="fas fa-wind"></i>
            <h2>Velocidad del Viento</h2>
            <p>Control de la velocidad y dirección del viento.</p>
        </a>
        <a href="php/consumo.php" class="box">
            <i class="fas fa-tint"></i>
            <h2>Consumo de Agua</h2>
            <p>Seguimiento del uso de agua en el invernadero.</p>
        </a>
        <a href="php/clorofila.php" class="box">
            <i class="fas fa-leaf"></i>
            <h2>Clorofila</h2>
            <p>Monitoreo de la clorofila en las plantas.</p>
        </a>
    </div>
    <div class="footer">
        <img src="img/logo.png" alt="Logo">
        <p>&copy; 2025 AgroVision. Todos los derechos reservados.</p>
    </div>
</body>
</html>