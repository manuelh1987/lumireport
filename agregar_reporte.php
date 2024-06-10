<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['seriePoste']) && isset($_POST['descripcion'])) {
        include 'conexion_db_reporte.php';
        $dbcon = conexion();

        $seriePoste = $_POST['seriePoste'];
        $descripcion = $_POST['descripcion'];

        // Verificar si ya existe un reporte para este poste
        $query_verificar = "SELECT * FROM reportes WHERE serie = '$seriePoste'";
        $result_verificar = pg_query($dbcon, $query_verificar);

        if ($result_verificar) {
            if (pg_num_rows($result_verificar) == 0) {
                // No hay reporte para este poste, actualizar el estado a "malo" solo si existe en la tabla de postes
                $query_existencia = "SELECT * FROM postes WHERE serie = '$seriePoste'";
                $result_existencia = pg_query($dbcon, $query_existencia);

                if ($result_existencia && pg_num_rows($result_existencia) > 0) {
                    // El poste existe, actualizar el estado y agregar el reporte
                    $query_actualizar = "UPDATE postes SET estado = 'malo', descrip = '$descripcion' WHERE serie = '$seriePoste'";
                    $result_actualizar = pg_query($dbcon, $query_actualizar);

                    if ($result_actualizar) {
                        // Insertar el reporte en la tabla de reportes
                        $query_insertar = "INSERT INTO reportes (serie, descrip) VALUES ('$seriePoste', '$descripcion')";
                        $result_insertar = pg_query($dbcon, $query_insertar);

                        if ($result_insertar) {
                            echo "El reporte se ha enviado correctamente y el estado del poste ha sido actualizado a malo.";
                        } else {
                            echo "Error al insertar el reporte en la base de datos.";
                        }
                    } else {
                        echo "Error al actualizar el estado del poste.";
                    }
                } else {
                    // El poste no existe en la tabla de postes
                    echo "El número de serie del poste no existe en la base de datos.";
                }
            } else {
                // Ya hay un reporte para este poste
                echo "Ya se ha realizado un reporte para este poste.";
            }
        } else {
            echo "Error al ejecutar la consulta.";
        }
    } else {
        echo "Debe proporcionar tanto el número de serie del poste como la descripción.";
    }
} else {
    echo "El formulario no se ha enviado correctamente.";
}
?>
