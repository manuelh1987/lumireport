<?php
// Conexión a la base de datos
include 'conexion_db_reporte.php';
$dbcon = conexion();

// Verificar si se ha enviado el número de serie del poste
if (isset($_GET['seriePoste'])) {
    $seriePoste = $_GET['seriePoste'];

    // Consulta para obtener las coordenadas del poste
    $query = "SELECT x_long, y_lat FROM postes WHERE serie = '$seriePoste'";
    $result = pg_query($dbcon, $query);

    // Verificar si se encontraron las coordenadas del poste
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $latitud = $row['y_lat'];
        $longitud = $row['x_long'];

        // Devolver las coordenadas como JSON
        echo json_encode(array("latitud" => $latitud, "longitud" => $longitud));
    } else {
        echo json_encode(array("error" => "No se encontraron datos para el poste con la serie proporcionada."));
    }
} else {
    echo json_encode(array("error" => "No se proporcionó el número de serie del poste."));
}
?>
