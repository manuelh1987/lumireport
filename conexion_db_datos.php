<?php
function conexion_datos() {
    $host = 'localhost';
    $port = '5432';
    $dbname = 'datos';
    $user = 'postgres';
    $password = '12345';

    $dbcon = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    if (!$dbcon) {
        echo "Error: No se pudo conectar a la base de datos.\n";
        exit;
    }
    return $dbcon;
}
?>
