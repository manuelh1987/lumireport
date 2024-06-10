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
      width: 300px;
      height: 100vh;
      background-color: #f8f9fa;
      padding: 20px;
      box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.1);
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
    .btn-back {
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <a class="btn btn-primary btn-back" href="index.html">Regresar</a>
  </div>

  <div id="map-container">
    <div id="map"></div>
    <div class="sidebar">
      <h2>Consultar Manzana</h2>
      <form id="consultaManzanaForm">
        <div class="mb-3">
          <label for="numeroManzana" class="form-label">Número de Manzana</label>
          <input type="text" class="form-control" id="numeroManzana" name="numeroManzana" required>
        </div>
        <button type="submit" class="btn btn-primary">Consultar</button>
      </form>
      <div id="resultado"></div>
      <h3>Geoservicio</h3>
      <p>
        Enlace de datos:
        <br>
        <input type="text" class="form-control" value="http://localhost:8080/geoserver/lumireport/wms?" readonly>
      </p>
    </div>
  </div>
  
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
          integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
          crossorigin=""></script>
  <script>
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

    var currentMarker = null;

    document.getElementById('consultaManzanaForm').addEventListener('submit', function(event) {
      event.preventDefault();
      var numeroManzana = document.getElementById('numeroManzana').value;

      fetch('consulta_postes.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'numeroManzana=' + encodeURIComponent(numeroManzana)
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('resultado').innerHTML = data;

        // Obtener coordenadas del centroide
        fetch('obtener_centroide_manzana.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'numeroManzana=' + encodeURIComponent(numeroManzana)
        })
        .then(response => response.json())
        .then(data => {
          if (data.latitud && data.longitud) {
            mymap.setView([data.latitud, data.longitud], 18);

            // Eliminar el marcador anterior si existe
            if (currentMarker) {
              mymap.removeLayer(currentMarker);
            }

            // Agregar nuevo marcador
            currentMarker = L.marker([data.latitud, data.longitud]).addTo(mymap)
              .bindPopup('Centroide de la manzana ' + numeroManzana)
              .openPopup();
          } else if (data.error) {
            alert(data.error);
          }
        })
        .catch(error => console.error('Error al obtener el centroide:', error));
      })
      .catch(error => console.error('Error en la consulta de postes:', error));
    });
  </script>
</body>
</html>
