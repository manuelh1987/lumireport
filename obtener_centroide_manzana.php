<?php
// Conexión a la base de datos
include 'conexion_db_datos.php';
$dbcon = conexion_datos();

if (isset($_POST['numeroManzana'])) {
    $numeroManzana = $_POST['numeroManzana'];

    // Consulta SQL para obtener las coordenadas del centroide de la geometría de la manzana
    $query = "SELECT ST_X(ST_Centroid(geom)) AS longitud, ST_Y(ST_Centroid(geom)) AS latitud FROM manzanas WHERE nombre = $1";

    $result = pg_prepare($dbcon, "consulta_centroide", $query);
    $result = pg_execute($dbcon, "consulta_centroide", array($numeroManzana));

    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $latitud = $row['latitud'];
        $longitud = $row['longitud'];
        echo json_encode(array("latitud" => $latitud, "longitud" => $longitud));
    } else {
        echo json_encode(array("error" => "La manzana solicitada no existe"));
    }
} else {
    echo json_encode(array("error" => "Número de manzana no proporcionado"));
}

// Cerrar conexión a la base de datos
pg_close($dbcon);
?>
