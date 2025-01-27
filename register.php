<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="icon" href="https://img.icons8.com/?size=100&id=80791&format=png&color=000000" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="formulario" >
        <h1>Registrarme</h1>
        <form action="php/conexion_register.php" method="POST">
            <div class="username">
                <input type="text" required name="user">
                <label>Nombre de usuari</label>
            </div>
            <div class="username">
                <input type="text" required name="nombre_user">
                <label>Nombre de completo</label>
            </div>
            <div class="username">
                <input type="text" required name="email_user">
                <label>correo electronico</label>
            </div>
            <div class="username">
                <input type="password" required name="pass_user">
                <label>Contraseña</label>
            </div>
            <input type="submit" value="Registrarse">
            <div class="registro">
                Ya tengo una <a href="../index.php">cuenta</a>    
            </div>
        </form>
    </div>
</body>
</html>