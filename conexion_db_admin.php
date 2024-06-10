<?php
function conexion_admin(){
$host = 'localhost';
$port = '5432';
$base_datos = 'validar_admin';
$usuario = 'postgres';
$pass = '12345';
$conexion = pg_connect("host=$host port=$port dbname=$base_datos user=$usuario password=$pass");
return($conexion);
}
?>