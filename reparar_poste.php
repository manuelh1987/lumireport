<?php
session_start();

if(isset($_SESSION['rol'])) {
    include 'conexion_db_reporte.php';
    $dbcon_reporte = conexion();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $seriePoste = $_POST['seriePoste'];

        // Eliminar la entrada correspondiente en la tabla reportes
        $query_delete = "DELETE FROM reportes WHERE serie = '$seriePoste'";
        $result_delete = pg_query($dbcon_reporte, $query_delete);

        if ($result_delete) {
            if (pg_affected_rows($result_delete) > 0) {
                echo "La entrada del reporte para el poste con número de serie $seriePoste ha sido eliminada correctamente.";

                // Actualizar el estado del poste en la tabla de postes a "bueno"
                $query_update = "UPDATE postes SET estado = 'bueno' WHERE serie = '$seriePoste'";
                $result_update = pg_query($dbcon_reporte, $query_update);

                if ($result_update) {
                    echo "El estado del poste con número de serie $seriePoste ha sido actualizado a bueno.";
                } else {
                    echo "Error al actualizar el estado del poste en la tabla de postes.";
                }
            } else {
                echo "No se encontró ninguna entrada para el poste con número de serie $seriePoste en la tabla de reportes.";
            }
        } else {
            echo "Error al eliminar la entrada del reporte.";
        }
    }
}
?>
