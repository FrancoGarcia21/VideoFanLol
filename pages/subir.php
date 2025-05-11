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

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir Video - VideoFanLOL</title>
  
   <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link rel="stylesheet" href="../assets/css/estilos.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<script>
document.addEventListener("DOMContentLoaded", async function () {
  const estadoDiv = document.getElementById("estado-superpop");

  try {
    const response = await fetch("/VideoFanLol/backend/es_super_pop.php");
    const data = await response.json();

    if (data.super_pop === true) {
      estadoDiv.textContent = "🌟 ¡Sos un usuario super pop! Podés subir videos de hasta 10 minutos y 500MB.";
      estadoDiv.classList.add("ok");
    } else {
      estadoDiv.textContent = "👤 Usuario estándar. Límite: 5 minutos y 300MB.";
    }
  } catch (e) {
    estadoDiv.textContent = "⚠️ No se pudo verificar el estado super pop.";
  }
});
</script>

<body>

<main class="container mt-20">
  <section class="formulario-container">
    <h2 class="text-center">⬆️ Subir un nuevo video</h2>
    <div id="estado-superpop" class="mensaje-superpop"></div>


    <form class="formulario" action="../backend/subir_video.php" method="POST" enctype="multipart/form-data">

      <label for="video">🎥 Archivo de video</label>
      <br>
      <input type="file" name="video" accept="video/mp4" required>
      <br>

      <label for="titulo">📌 Título:</label>
      <br>
      <input type="text" name="titulo" required>
      <br>

      <div>
      <label for="descripcion">📝 Descripción:</label>
      <br>
      <textarea name="descripcion" rows="4" required></textarea>
      </div>
      <br>

      <label>🏷️ Palabras clave (máx. 10):</label>
      <div class="checkboxes-palabras">
        <?php
        $palabras = ["naturaleza", "animales", "ciudad", "danza", "montaña", "humor", "deporte", "viaje", "ciencia", "arte"];
        foreach ($palabras as $palabra): ?>
          <label><input type="checkbox" name="palabras_clave[]" value="<?= $palabra ?>"> <?= ucfirst($palabra) ?></label>
        <?php endforeach; ?>
      </div>
      <br>
      <div>
      <label for="pais">🌎 País:</label>
      
      <input type="text" name="pais" required>
      

      <label for="provincia">🗺️ Provincia:</label>
    
      <input type="text" name="provincia" required>
    

      <label for="ciudad">🏙️ Ciudad:</label>
      
      <input type="text" name="ciudad" required>
      </div>
      <br>
      
      <div>
      <label for="fecha_grabacion">📅 Fecha de grabación:</label>
    
      <input type="date" name="fecha_grabacion" required>
      </div>
      <br>

      <label>📍 Ubicación geográfica (clic en el mapa):</label>
      <div id="mapa"></div>

      <input type="hidden" name="latitud" id="latitud">
      <input type="hidden" name="longitud" id="longitud">

      <button type="submit" class="btn btn-primary">📤 Subir video</button>
    </form>
  </section>
</main>
<footer class="text-center mt-20">
  <p>&copy; 2025 VideoFanLOL - Todos los derechos reservados Franco Garcia</p>
</footer>
<script src="../assets/js/mapa_video.js"></script>
<script src="../assets/js/ui-mensajes.js"></script>
<script src="../assets/js/validar_video.js"></script>
</body>
</html>
