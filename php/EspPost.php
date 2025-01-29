<?php

include 'conexion.php';

if ($con) {
    echo "Conexion con base de datos exitosa! ";

    /*humedad suelo*/
    if (isset($_POST['humedad'])) {
        $humedad = $_POST['humedad'];
        echo "Humedad recibida: " . $humedad;

        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");

        $consulta = "INSERT INTO hum_suelo(humedad, fecha_registro) VALUES ('$humedad', '$fecha_actual')";

        $resultado = mysqli_query($con, $consulta);

        if ($resultado) {
            echo " Registro en base de datos OK! ";
        } else {
            echo " Falla en el registro en BD: " . mysqli_error($con);
        }
    } else {
        echo " No se recibió el dato de humedad. ";
    }

    /*flujo de agua*/
    if (isset($_POST['flujo_agua'])) {
        $flujo_agua = $_POST['flujo_agua'];
        echo "El flujo de agua recibido es: " . $flujo_agua;

        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");

        $consulta = "INSERT INTO flujo_agua(Litros-seg, fecha_registro) VALUES ('$flujo_agua', '$fecha_actual')";

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
    if (isset($_POST['temp'])) {
        $temp = $_POST['temp'];
        echo "la temperatura atmosferica es: " . $temp;
    }else{
        echo " No se recibió el dato de temperatura. ";
    }
    
    if (isset($_POST['hum'])) {
        $hum = $_POST['hum'];
        echo "la humedad atmosferica es: " . $hum;
        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");

        $consulta = "INSERT INTO temp_hum_amb(temperatura_ambiente, humedad_ambiente, fecha_registro) VALUES ('$hum', '$temp', '$fecha_actual')";

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
