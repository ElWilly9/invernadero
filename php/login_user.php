<?php
session_start();
include 'conexion.php';

$user = $_POST['user'];
$password = $_POST['contrasena'];
$password = hash('sha512', $password);

// Validar el usuario
$validar_user = mysqli_query($con, "SELECT * FROM users WHERE user='$user'");

if (mysqli_num_rows($validar_user) > 0) {
    // Validar la contraseña
    $validar_pass = mysqli_query($con, "SELECT * FROM users WHERE contrasena='$password'");

    if (mysqli_num_rows($validar_pass) > 0) {
        $_SESSION['usuario'] = $user;
        echo '
        <script>
            window.location = "../bienvenido.php";
        </script>';
        exit;
    } else {
        // Contraseña incorrecta
        $_SESSION['error'] = "La contraseña es incorrecta, inténtelo nuevamente";
        echo '
        <script>
            window.location = "../index.php";
        </script>';
        exit;
    }
} else {
    // Usuario no encontrado
    $_SESSION['error'] = "Usuario no registrado en nuestra base de datos";
    echo '
    <script>
        window.location = "../index.php";
    </script>';
    exit;
}
?>