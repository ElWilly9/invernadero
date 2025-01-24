<?php

    session_start();
    
    include 'conexion.php';

    $user = $_POST['user'];
    $password = $_POST['contrasena'];
    $password = hash('sha512', $password);

    $validar_user = mysqli_query($con, "SELECT * FROM users WHERE user='$user'");

    if(mysqli_num_rows($validar_user)>0){
        
        $validar_pass = mysqli_query($con, "SELECT * FROM users WHERE contrasena='$password'");

        if(mysqli_num_rows($validar_pass)>0){
            $_SESSION['usuario'] = $user;
            echo '
            <script>
                window.location = "../bienvenido.php";
            </script>
        ';
        exit;
        }else{
            echo '
            <script>
                alert("La contraseaña es incorrecta, intentelo nuevamente");
                window.location = "../index.php";
            </script>
        ';
        exit;
        }
    }else{
        echo '
            <script>
                alert("Usuario no registrado en nuestra base de datos");
                window.location = "../index.php";
            </script>
        ';
        exit;
    }

?>