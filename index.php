<?php

    session_start();
    
    if(isset($_SESSION['usuario'])){
        header("location: bienvenido.php");
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="formulario" >
        <h1>Inicio de Sesion</h1>
        <form method="POST" action="php/login_user.php">
            <div class="username">
                <input type="text" required name="user">
                <label>Nombre de usuario</label>
            </div>
            <div class="username">
                <input type="password" required name="contrasena">
                <label>Contraseña</label>
            </div>
            <input type="submit" value="Iniciar Sesion">
            <div class="registro">
                Quiero hacer el <a href="register.php">registro</a>    
            </div>
        </form>
    </div>
</body>
</html>