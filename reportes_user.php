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
      background-color: #333; /* Color de fondo de la barra superior */
      color: white; /* Color del texto de la barra superior */
      padding: 10px 20px;
    }
    .sidebar {
      width: 300px;
      height: 100vh;
      background-color: #f8f9fa;
      padding: 20px;
      box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.1);
    }
    #map-container {
      display: flex;
      flex-grow: 1;
      overflow: hidden; /* Para evitar que el mapa se extienda más allá del contenedor */
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
        include 'conexion_db_user.php';
        $dbcon = conexion();
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
        echo '<p>Debe Iniciar Sesión Para Acceder<br> <br><a href="inicio_user.php">Iniciar Sesión</a></p>';
    }
    ?>
  </div>

  <div id="map-container">
    <div id="map"></div>
    <div class="sidebar">
      <?php if(isset($_SESSION['rol'])): ?>
        <div id="reporteForm">
          <h2>Ingresar Reporte</h2>
          <form id="reportForm">
            <div class="mb-3">
              <label for="seriePoste" class="form-label">Número de Serie del Poste</label>
              <input type="text" class="form-control" id="seriePoste" name="seriePoste" required>
            </div>
            <div class="mb-3">
            <button type="button" class="btn btn-primary" onclick="centrarEnPunto()">Acercar a</button>
            </div>
            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Reporte</button>
          </form>
          <div id="mensaje"></div>
        </div>

        <div class="mb-3"><br>
        <button type="button" class="btn btn-primary" onclick="window.location.reload();">Actualizar página</button>
            </div>
        
      <?php endif; ?>
    </div>
  </div>
  
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
          integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
          crossorigin=""></script>
  <script>
    document.getElementById('reportForm').addEventListener('submit', function(event) {
      event.preventDefault();

      var seriePoste = document.getElementById('seriePoste').value;
      var descripcion = document.getElementById('descripcion').value;

      var formData = new FormData();
      formData.append('seriePoste', seriePoste);
      formData.append('descripcion', descripcion);

      fetch('agregar_reporte.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('mensaje').innerHTML = data;
        // Aquí puedes agregar lógica adicional después de enviar el reporte
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
</body>
</html>
