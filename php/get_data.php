<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

include_once 'conexion.php';

while (true) {
    $SQL = "SELECT cl.valor_clorofila1, cl.fecha_registro 
            FROM clorofila cl 
            ORDER BY cl.fecha_registro DESC LIMIT 50";
    
    $consulta = mysqli_query($con, $SQL);
    $valor_clorofila = [];
    $fechas = [];
    
    if ($consulta && mysqli_num_rows($consulta) > 0) {
        while ($resultado = mysqli_fetch_assoc($consulta)) {
            $fecha_formateada = date('Y-m-d', strtotime($resultado['fecha_registro']));
            $valor_clorofila[] = $resultado['valor_clorofila1'];
            $fechas[] = $fecha_formateada;
        }
        
        // Invertir para orden cronológico correcto
        $valor_clorofila = array_reverse($valor_clorofila);
        $fechas = array_reverse($fechas);
        
        echo "data: " . json_encode(['fechas' => $fechas, 'valores' => $valor_clorofila]) . "\n\n";
    }
    
    // Limpiar el buffer de salida y enviarlo
    ob_flush();
    flush();
    
    // Esperar 5 segundos antes de la siguiente consulta
    sleep(5);
}