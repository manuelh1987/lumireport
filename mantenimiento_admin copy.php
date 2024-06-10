<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#0E1428">
  <link rel="icon" type="image/png" href="./images/favicon.png">
  <title>LUMIREPORT - SISTEMA DE REPORTE DE LUMINARIAS</title>
  <link rel="stylesheet" href="./css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="./css/swiper-bundle.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="

   crossorigin=""/>
</head>
  
<body>


<?php
include 'conexion_db_admin.php'; // Se incluye el archivo de conexión a la base de datos
$dbcon = conexion(); // se crea una variable con la función definida anteriormente
session_start(); // se inicia una sesión

$rol = $_SESSION['rol'];

if($rol){
    echo "<p> Bienvenido, $rol <a href='salir.php'>Cerrar Sesion</a></p>";

    // Mostrar el mapa solo si el usuario ha iniciado sesión
    echo '<div id="mapid" style="width: 800px; height: 550px;"></div>';
} else {
    // Si no se ha iniciado sesión, mostrar un mensaje de error
    echo '<p>Debe Iniciar Sesion Para Acceder<br> <br><a href="inicio_user.php">Iniciar Sesion</a></p>';
}
?>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"

integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="

   crossorigin=""></script>
 
<script src="GeoJson/lines.js"

></script>
   
<div id="mapid" style="responsive">

</div>



<script>
var mymap = L.map('mapid').setView([3.228062, -76.510775], 16);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mymap);

var Comunas = L.tileLayer.wms('http://ws-idesc.cali.gov.co:8081/geoserver/wms?', {
    layers: 'idesc:mc_comunas',
    attribution: 'Creacion de Mapa de SIG3-UV',
    format: 'image/png',
    transparent: true
});

Comunas.addTo(mymap);
</script>


</body>
</html>
