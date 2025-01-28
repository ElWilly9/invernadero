<?php

include 'conexion.php';

if ($con) {
    echo "Conexion con base de datos exitosa! ";

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
} else {
    echo "Falla! Conexión con base de datos ";
}
