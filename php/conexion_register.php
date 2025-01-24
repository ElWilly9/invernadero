<?php

    include 'conexion.php';

    $user = $_POST['user'];
    $nombre_user = $_POST['nombre_user'];
    $email_user = $_POST['email_user'];
    $pass_user = $_POST['pass_user'];

    //encriptar contraseña
    $pass_user = hash('sha512', $pass_user);

    $query = "INSERT INTO users(user, nombre, correo, contrasena) 
                VALUES('$user', '$nombre_user', '$email_user', '$pass_user')";

    //verificar credenciales duplicadas
    //usuarios
    $verificar_user = mysqli_query($con, "SELECT * FROM users WHERE user='$user'");

    if(mysqli_num_rows($verificar_user)>0){
        echo '
            <script>
                alert("El usuario ya se encuentra registrado, intenta con otro");
                window.location = "../register.php";
            </script>
        ';
        exit();
    }

    //correos
    $verificar_mail = mysqli_query($con, "SELECT * FROM users WHERE correo='$email_user'");

    if(mysqli_num_rows($verificar_mail)>0){
        echo '
            <script>
                alert("El correo ya se encuentra registrado, intenta con otro");
                window.location = "../register.php";
            </script>
        ';
        exit();
    }

    //ejecutar
    $ejecutar = mysqli_query($con, $query);

    if($ejecutar){
        echo '
            <script>
                alert("Usuario creado exitosamente");
                window.location = "../index.php";
            </script>
        ';
    }else{
        echo '
            <script>
                alert("Algo salio mal, intentelo nuevamente");
                window.location = "register.php";
            </script>
        ';
    }

    mysqli_close($con);

?>