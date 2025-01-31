<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agrovision - Registro</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a5632 0%, #0d3521 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .formulario {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 500px;
            position: relative;
            animation: aparecer 0.8s ease-out;
        }

        @keyframes aparecer {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .formulario::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #4CAF50 0%, #8BC34A 100%);
        }

        h1 {
            color: #2e7d32;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .username {
            position: relative;
            margin-bottom: 25px;
        }

        .username input {
            width: 100%;
            padding: 15px 20px 15px 45px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .username input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
        }

        .username label {
            position: absolute;
            left: 45px;
            top: 15px;
            color: #666;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .username input:focus ~ label,
        .username input:valid ~ label {
            top: -10px;
            left: 35px;
            font-size: 12px;
            background: white;
            padding: 0 5px;
            color: #4CAF50;
        }

        .fas {
            position: absolute;
            left: 15px;
            top: 15px;
            color: #666;
            font-size: 18px;
        }

        input[type="submit"] {
            background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }

        .registro {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .registro a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .registro a:hover {
            color: #388E3C;
        }

        @media (max-width: 480px) {
            .formulario {
                width: 90%;
                padding: 30px;
            }
            
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="formulario">
        <h1><i class="fas fa-seedling"></i>Nuevo Registro</h1>
        <form action="php/conexion_register.php" method="POST">
            <div class="username">
                <i class="fas fa-user-tag"></i>
                <input type="text" required name="user">
                <label>Nombre de usuario</label>
            </div>
            
            <div class="username">
                <i class="fas fa-id-card"></i>
                <input type="text" required name="nombre_user">
                <label>Nombre completo</label>
            </div>
            
            <div class="username">
                <i class="fas fa-envelope"></i>
                <input type="email" required name="email_user">
                <label>Correo electrónico</label>
            </div>
            
            <div class="username">
                <i class="fas fa-lock"></i>
                <input type="password" required name="pass_user">
                <label>Contraseña</label>
            </div>
            
            <input type="submit" value="Crear cuenta">
            
            <div class="registro">
                ¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a>
            </div>
        </form>
    </div>
</body>
</html>