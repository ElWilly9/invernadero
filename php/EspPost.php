<?php

include 'conexion.php';

if ($con) {
    echo "Conexion con base de datos exitosa! ";

    /*humedad suelo*/
    if (isset($_POST['humedad1'])) {
        $humedad1 = $_POST['humedad1'];
        echo "Humedad recibida del sensor 1 es: " . $humedad1;
        $humedad2 = $_POST['humedad2'];
        echo "Humedad recibida del sensor 1 es: " . $humedad2;

        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");
        $hora_actual = date("H:i:s");

        $consulta = "INSERT INTO hum_suelo(humedad1, humedad2, fecha_registro, hora_registro) VALUES ('$humedad1', '$humedad2', '$fecha_actual', '$hora_actual')";

        $resultado = mysqli_query($con, $consulta);

        if ($resultado) {
            echo " Registro en base de datos OK! ";
        } else {
            echo " Falla en el registro en BD: " . mysqli_error($con);
        }
    } else {
        echo " No se recibió el dato de humedad del suelo. ";
    }

    /*flujo de agua*/
    if (isset($_POST['flujo_agua'])) {
        $flujo_agua = $_POST['flujo_agua'];
        echo "El flujo de agua recibido es: " . $flujo_agua;
        
        $flujo_acumulado = $_POST['flujo_total'];
        echo "El flujo de agua total recibido es: " . $flujo_acumulado;

        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");
        $hora_actual = date("H:i:s");

        $consulta = "INSERT INTO flujo_agua(litros_min, flujo_acumulado, fecha_registro, hora_registro) VALUES ('$flujo_agua', '$flujo_acumulado', '$fecha_actual', '$hora_actual')";

        $resultado = mysqli_query($con, $consulta);

        if ($resultado) {
            echo " Registro en base de datos OK! ";
        } else {
            echo " Falla en el registro en BD: " . mysqli_error($con);
        }
    } else {
        echo " No se recibió el dato del flujo de agua. ";
    }

    /*temperatura y humedad atmosferica*/
    if (isset($_POST['temp1'])) {
        $temp1 = $_POST['temp1'];
        echo "la temperatura atmosferica del sensor 1 es: " . $temp1;
        $temp2 = $_POST['temp2'];
        echo "la temperatura atmosferica del sensor 2 es: " . $temp2;
    }else{
        echo " No se recibió el dato de temperatura. ";
    }
    
    if (isset($_POST['hum1'])) {
        $hum1 = $_POST['hum1'];
        echo "la humedad atmosferica es: " . $hum1;
        $hum2 = $_POST['hum2'];
        echo "la humedad atmosferica es: " . $hum2;

        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");
        $hora_actual = date("H:i:s");

        $consulta = "INSERT INTO temp_hum_amb(temperatura_ambiente1, temperatura_ambiente2, humedad_ambiente1, humedad_ambiente2, fecha_registro, hora_registro) VALUES ('$temp1', '$temp2', '$hum1', '$hum2', '$fecha_actual', '$hora_actual')";

        $resultado = mysqli_query($con, $consulta);

        if ($resultado) {
            echo " Registro en base de datos OK! ";
        } else {
            echo " Falla en el registro en BD: " . mysqli_error($con);
        }
    } else {
        echo " No se recibió el dato de humedad. ";
    }

} else {
    echo "Falla! Conexión con base de datos ";
}
