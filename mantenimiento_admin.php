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
  <style>
    body {
      display: flex;
      flex-direction: column;
      margin: 0;
      padding: 0;
    }
    .navbar {
      background-color: #333;
      color: white;
      padding: 10px 20px;
    }
    .sidebar {
      width: 500px;
      height: 100vh;
      background-color: #f8f9fa;
      padding: 20px;
      box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.1);
      overflow-y: auto;
    }
    #map-container {
      display: flex;
      flex-grow: 1;
      overflow: hidden;
    }
    #map {
      flex-grow: 1;
      height: 100vh;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <?php
    session_start();

    if(isset($_SESSION['rol'])) {
        include 'conexion_db_admin.php';
        $dbcon = conexion_admin();
        $rol = $_SESSION['rol'];

        if($rol){
            $query = "SELECT nombre, apellidos FROM usuarios WHERE rol = '$rol'";
            $result = pg_query($dbcon, $query);

            if($result && pg_num_rows($result) > 0){
                $row = pg_fetch_assoc($result);
                $nombre_completo = $row['nombre'] . ' ' . $row['apellidos'];
                echo "<p>Bienvenido, $nombre_completo <a href='salir.php'>Cerrar Sesión</a></p>";
            } else {
                echo "<p>Bienvenido, $rol <a href='salir.php'>Cerrar Sesión</a></p>";
            }
        }
    } else {
        echo '<p>Debe Iniciar Sesión Para Acceder<br> <br><a href="inicio_admin.php">Iniciar Sesión</a></p>';
    }
    ?>
  </div>

  <div id="map-container">
    <div id="map"></div>
    <div class="sidebar">
      <?php if(isset($_SESSION['rol'])): ?>
        <div id="reporteForm">
          <h2>Reparar poste</h2>
          <form id="reportForm" action="reparar_poste.php" method="POST">
            <div class="mb-3">
              <label for="seriePoste" class="form-label">Número de Serie del Poste</label>
              <input type="text" class="form-control" id="seriePoste" name="seriePoste" required>
            </div>
            <button type="button" class="btn btn-primary" onclick="centrarEnPunto()">Centrar en Poste</button>
            <button type="button" class="btn btn-primary" onclick="repararPoste()">Reparar poste</button>
          </form>
          <div id="mensaje"></div>
        </div>
        <div class="mb-3"><br>
        <button type="button" class="btn btn-primary" onclick="window.location.reload();">Actualizar página</button>
            </div>

        <div id="tablaPostesMalos">
          <h2>Postes Malos</h2>
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Serie del Poste</th>
                <th>Fecha del Reporte</th>
                <th>Descripción</th>
              </tr>
            </thead>
            <tbody>
              <?php
              include 'conexion_db_reporte.php';
              $dbcon_reporte = conexion();
              $query = "SELECT * FROM reportes";
              $result = pg_query($dbcon_reporte, $query);
              if($result && pg_num_rows($result) > 0) {
                while($row = pg_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>".$row['id']."</td>";
                  echo "<td>".$row['serie']."</td>";
                  echo "<td>".$row['fecha_reporte']."</td>";
                  echo "<td>".$row['descrip']."</td>";
                  echo "</tr>";
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
  
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="SHA-512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiZ5V3ynxwA=="
        crossorigin=""></script>
  <script>
    document.getElementById('reportForm').addEventListener('submit', function(event) {
      event.preventDefault();

      var seriePoste = document.getElementById('seriePoste').value;

      fetch('reparar_poste.php?seriePoste=' + seriePoste)
      .then(response => response.text())
      .then(data => {
        document.getElementById('mensaje').innerHTML = data;
      })
      .catch(error => console.error('Error:', error));
    });

    <?php if($rol): ?>
    var mymap = L.map('map', {
        center: [3.228062, -76.510775],
        zoom: 16,
        minZoom: 16,
        maxZoom: 18
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mymap);


    var postes = L.tileLayer.wms('http://localhost:8080/geoserver/lumireport/wms?', {
        layers: 'lumireport:postes',
        attribution: 'Creacion de Mapa de SIG3-UV',
        format: 'image/png',
        transparent: true
    });


    var vias = L.tileLayer.wms('http://localhost:8080/geoserver/lumireport/wms?', {
        layers: 'lumireport:vias',
        attribution: 'Creacion de Mapa de SIG3-UV',
        format: 'image/png',
        transparent: true
    });

    var manzanas = L.tileLayer.wms('http://localhost:8080/geoserver/lumireport/wms?', {
        layers: 'lumireport:manzanas',
        attribution: 'Creacion de Mapa de SIG3-UV',
        format: 'image/png',
        transparent: true,
        opacity: 0.5
    });


    postes.addTo(mymap);
    vias.addTo(mymap);
    manzanas.addTo(mymap);
    

    var overlayMaps = {
        "Vías": vias,
        "Manzanas": manzanas,
        "Postes": postes
  
    };

    L.control.layers(null, overlayMaps).addTo(mymap);
    <?php endif; ?>


    function centrarEnPunto() {
    var seriePoste = document.getElementById('seriePoste').value;
    obtenerCoordenadasPoste(seriePoste);
}

var posteMarker = null; // Variable para mantener la referencia al marcador actual

function agregarMarcador(latitud, longitud, seriePoste) {
    // Eliminar el marcador anterior, si existe
    if (posteMarker !== null) {
        mymap.removeLayer(posteMarker);
    }

    // Crear un marcador con las coordenadas proporcionadas
    posteMarker = L.marker([latitud, longitud]).addTo(mymap);

    // Agregar un popup al marcador con la serie del poste
    posteMarker.bindPopup("<b>Serie del poste:</b> " + seriePoste).openPopup();
}

function obtenerCoordenadasPoste(seriePoste) {
    // Realizar la solicitud para obtener las coordenadas del poste
    fetch('obtener_coordenadas_poste.php?seriePoste=' + seriePoste)
    .then(response => response.json())
    .then(data => {
        var latitud = data.latitud;
        var longitud = data.longitud;

        if (latitud && longitud) {
            mymap.setView([latitud, longitud], 18); // Centrar el mapa en las coordenadas del poste

            // Agregar marcador del poste con la serie
            agregarMarcador(latitud, longitud, seriePoste);
        } else {
            alert("No se pudieron encontrar las coordenadas del poste.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Error al obtener las coordenadas del poste.");
    });
}

function centrarEnPunto() {
    var seriePoste = document.getElementById('seriePoste').value;
    obtenerCoordenadasPoste(seriePoste);
}
  </script>


<script>
    function repararPoste() {
      var seriePoste = document.getElementById('seriePoste').value;

      fetch('reparar_poste.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'seriePoste=' + encodeURIComponent(seriePoste)
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('mensaje').innerHTML = data;
      })
      .catch(error => console.error('Error:', error));
    }
  </script>
</body>
</html>
