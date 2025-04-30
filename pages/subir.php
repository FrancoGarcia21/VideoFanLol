<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include("navbar.php");
?>

<style>
  #mapa {
    height: 300px !important;
    margin: 10px 0;
    border: 2px solid #333;
    border-radius: 6px;
  }
</style>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir Video - VideoFanLOL</title>
  
  <link rel="stylesheet" href="../assets/css/estilos.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const mapa = L.map('mapa').setView([-40, -64], 4);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(mapa);

    let marcador;

    mapa.on("click", function (e) {
      const { lat, lng } = e.latlng;

      if (marcador) {
        marcador.setLatLng([lat, lng]);
      } else {
        marcador = L.marker([lat, lng]).addTo(mapa);
      }

      document.getElementById("latitud").value = lat;
      document.getElementById("longitud").value = lng;
    });
  });
</script>
</head>
<body>
  <div class="container mt-20">
    <h2>⬆️ Subir un nuevo video</h2>

    <form class="formulario" action="../backend/subir_video.php" method="POST" enctype="multipart/form-data">

      <label for="video">Archivo de video (.mp4, máx. 300MB):</label><br>
      <input type="file" name="video" accept="video/mp4" required><br><br>

      <label for="titulo">Título:</label><br>
      <input type="text" name="titulo" required><br><br>

      <label for="descripcion">Descripción:</label><br>
      <textarea name="descripcion" rows="4" required></textarea><br><br>

      <label for="palabras_clave">Palabras clave (separadas por comas, máx. 10):</label><br>
      <input type="text" name="palabras_clave" required><br><br>

      <label for="lugar">Lugar de grabación:</label><br>
      <input type="text" name="lugar" required><br><br>

      <label for="fecha_grabacion">Fecha de grabación:</label><br>
      <input type="date" name="fecha_grabacion" required><br><br>

      <!-- 🌍 Mapa Leaflet -->
      <label>Ubicación geográfica (haz clic en el mapa para seleccionar):</label>
      
      <div id="mapa" style="position: relative;"></div>


      <!-- Coordenadas seleccionadas -->
      <input type="hidden" name="latitud" id="latitud">
      <input type="hidden" name="longitud" id="longitud">

      <br>
      <button type="submit">📤 Subir video</button>
    </form>
  </div>

  <!-- Scripts -->
  <script src="../assets/js/ui-mensajes.js"></script>
  <script src="../assets/js/validar_video.js"></script>

</body>
</html>
