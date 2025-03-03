<?php

include 'conexion.php';

if ($con) {
    echo "Conexion con base de datos exitosa! ";

    /*flujo de agua*/
    if (isset($_POST['flujo_agua'])) {
        $flujo_agua = $_POST['flujo_agua'];
        echo "El flujo de agua recibido es: " . $flujo_agua;
        
        $consumo = $_POST['flujo_total'];
        echo "El flujo de agua total recibido es: " . $consumo;

        // Obtener el último consumo_total registrado
        $query_ultimo_consumo = "SELECT consumo_total FROM flujo_agua ORDER BY id DESC LIMIT 1";
        $resultado_ultimo_consumo = mysqli_query($con, $query_ultimo_consumo);
        
        $consumo_total = $consumo; // Valor inicial
        
        if ($resultado_ultimo_consumo && mysqli_num_rows($resultado_ultimo_consumo) > 0) {
            $row = mysqli_fetch_assoc($resultado_ultimo_consumo);
            $ultimo_consumo_total = $row['consumo_total'];
            $consumo_total = $ultimo_consumo_total + $consumo;
        }

        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");
        $hora_actual = date("H:i:s");

        $consulta = "INSERT INTO flujo_agua(litros_min, consumo, consumo_total, fecha_registro) 
                     VALUES ('$flujo_agua', '$consumo', '$consumo_total', '$fecha_actual')";

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

        $humedad1 = $_POST['humedad1'];
        echo "Humedad recibida del sensor 1 es: " . $humedad1;
        $humedad2 = $_POST['humedad2'];
        echo "Humedad recibida del sensor 1 es: " . $humedad2;

        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");
        $hora_actual = date("H:i:s");

        $consulta = "INSERT INTO temp_hum_amb(temperatura_ambiente1, temperatura_ambiente2, humedad_ambiente1, humedad_ambiente2, fecha_registro, hora_registro, humedad1, humedad2) 
                     VALUES ('$temp1', '$temp2', '$hum1', '$hum2', '$fecha_actual', '$hora_actual', '$humedad1', '$humedad2')";

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