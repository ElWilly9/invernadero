<?php

include 'conexion.php';

if ($con) {
    echo "Conexion con base de datos exitosa! ";

    /*clorofila*/
    if (isset($_POST['clorofila'])) {
        $clorofila = $_POST['clorofila'];
        echo "La clorofila registrada es: " . $clorofila;

        if($clorofila > 0){
        date_default_timezone_set('America/Bogota');
        $fecha_actual = date("Y-m-d H:i:s");
        $hora_actual = date("H:i:s");

        $consulta = "INSERT INTO clorofila(valor_clorofila1, fecha_registro, hora_registro) 
                     VALUES ('$clorofila', '$fecha_actual', '$hora_actual')";

        $resultado = mysqli_query($con, $consulta);

        if ($resultado) {
            echo " Registro en base de datos OK! ";
        } else {
            echo " Falla en el registro en BD: " . mysqli_error($con);
        }
        }else{
            echo "El valor de clorofila es 0, no se registro en la base de datos";
        }
    } else {
        echo " No se recibió el dato de clorofila. ";
    }

} else {
    echo "Falla! Conexión con base de datos ";
}
