<?php
function conexion(){
$host = 'localhost';
$port = '5432';
$base_datos = 'datos';
$usuario = 'postgres';
$pass = '12345';
$conexion = pg_connect("host=$host port=$port dbname=$base_datos user=$usuario password=$pass");
return($conexion);
}
?>