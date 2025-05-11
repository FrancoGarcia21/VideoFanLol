<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/conectar.php";
include("navbar.php");

// Obtener datos del video
$videoId = $_GET['id'] ?? null;
$usuarioId = $_SESSION['usuario_id'];

$stmt = $conexion->prepare("SELECT * FROM videos WHERE id = :id AND usuario_id = :usuario_id");
$stmt->bindParam(":id", $videoId);
$stmt->bindParam(":usuario_id", $usuarioId);
$stmt->execute();
$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    echo "<p class='error'>Video no encontrado o no te pertenece.</p>";
    exit;
}

$palabrasDisponibles = ["naturaleza", "animales", "ciudad", "danza", "montaña", "humor", "deporte", "viaje", "ciencia", "arte"];
$palabrasMarcadas = array_map('trim', explode(',', $video['palabras_clave']));
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Video - VideoFanLOL</title>
  <link rel="icon" href="../assets/img/favicon.png" type="image/png">
  <link rel="stylesheet" href="../assets/css/estilos.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

<main class="container mt-20">
  <section class="formulario-container">
    <h2 class="text-center">✏️ Editar video</h2>

    <form class="formulario" action="../backend/editar_video.php" method="POST">
      <input type="hidden" name="id" value="<?= htmlspecialchars($video['id']) ?>">


      <label for="titulo">📌 Título:</label>
      <input type="text" name="titulo" value="<?= htmlspecialchars($video['titulo']) ?>" required>
      <br>
      <br>

      <label for="descripcion">📝 Descripción:</label>
      <br>
      <br>
      <textarea name="descripcion" rows="4" required><?= htmlspecialchars($video['descripcion']) ?></textarea>
      <br>
<br>
      <label>🏷️ Palabras clave (máx. 10):</label>
      <div class="checkboxes-palabras">
        <?php foreach ($palabrasDisponibles as $palabra): ?>
          <label>
            <input type="checkbox" name="palabras_clave[]" value="<?= $palabra ?>"
              <?= in_array($palabra, $palabrasMarcadas) ? 'checked' : '' ?>>
            <?= ucfirst($palabra) ?>
          </label>
        <?php endforeach; ?>
      </div>

      <label for="pais">🌎 País:</label>
      <input type="text" name="pais" value="<?= htmlspecialchars($video['pais'] ?? '') ?>" required>
      <br>
      <br>

      <label for="provincia">🗺️ Provincia:</label>
      <input type="text" name="provincia" value="<?= htmlspecialchars($video['provincia'] ?? '') ?>" required>
      <br>
      <br>

      <label for="ciudad">🏙️ Ciudad:</label>
      <input type="text" name="ciudad" value="<?= htmlspecialchars($video['ciudad'] ?? '') ?>" required>
      <br>
      <br>
      <label for="fecha_grabacion">📅 Fecha de grabación:</label>
      <input type="date" name="fecha_grabacion" value="<?= htmlspecialchars($video['fecha_grabacion']) ?>" required>
      <br>
      <br>
      <label>📍 Ubicación geográfica (clic en el mapa para actualizar):</label>
      <div id="mapa"></div>

      <input type="hidden" name="latitud" id="latitud" value="<?= htmlspecialchars($video['latitud']) ?>">
      <input type="hidden" name="longitud" id="longitud" value="<?= htmlspecialchars($video['longitud']) ?>">

      <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
    </form>
  </section>
</main>

<footer class="text-center mt-20">
  <p>&copy; 2025 VideoFanLOL - Todos los derechos reservados Franco Garcia</p>
</footer>


<script src="../assets/js/mapa_video.js"></script>
</body>
</html>
