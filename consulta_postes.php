<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_manzana = $_POST['numeroManzana'];

    // Conexión a la base de datos
    include 'conexion_db_datos.php';
    $dbcon = conexion_datos();

    // Consulta SQL para obtener el centroide de la manzana
    $query_centroide = "SELECT ST_AsText(ST_Centroid(geom)) AS centroide
                        FROM manzanas
                        WHERE nombre = $1";

    $result_centroide = pg_prepare($dbcon, "consulta_centroide", $query_centroide);
    $result_centroide = pg_execute($dbcon, "consulta_centroide", array($numero_manzana));

    if ($result_centroide) {
        $row_centroide = pg_fetch_assoc($result_centroide);
        $centroide = $row_centroide['centroide'];
        if ($centroide) {
            list($lng, $lat) = explode(' ', substr($centroide, 6, -1));
            echo "<script>mymap.setView([$lat, $lng], 18);</script>";
        }
    }

    // Consulta SQL para obtener los postes en la manzana
    $query_postes = "SELECT p.id_poste, p.serie, p.estado
                     FROM postes p
                     JOIN manzanas m ON ST_Intersects(p.geom, m.geom)
                     WHERE m.nombre = $1";

    $result_postes = pg_prepare($dbcon, "consulta_postes", $query_postes);
    $result_postes = pg_execute($dbcon, "consulta_postes", array($numero_manzana));

    if ($result_postes) {
        if (pg_num_rows($result_postes) > 0) {
            echo "<table class='table'>
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Serie</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = pg_fetch_assoc($result_postes)) {
                $estado = $row['estado'];
                $color = ($estado == 'bueno') ? 'blue' : 'red';
                echo "<tr>
                        <td><div style='width: 15px; height: 15px; border-radius: 50%; background-color: $color;'></div></td>
                        <td>{$row['id_poste']}</td>
                        <td>{$row['serie']}</td>
                        <td>{$estado}</td>
                      </tr>";
            }
            echo "</tbody>
                  </table>";
        } else {
            echo "<p style='color: black;'>La manzana solicitada no existe</p>";
        }
    } else {
        echo "<p>Error en la consulta.</p>";
    }

    pg_close($dbcon);
}
?>
