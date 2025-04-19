<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once "../backend/conectar.php";

$ultimosVideos = [];

try {
    $stmt = $conexion->prepare("SELECT id, titulo, descripcion, ruta_archivo FROM videos ORDER BY fecha_subida DESC LIMIT 5");
    $stmt->execute();
    $ultimosVideos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "<p class='error'>Error al cargar los videos: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>

  <?php include("navbar.php"); ?>

  <div class="container text-center mt-20">

    <?php
    if (isset($_GET['subida'])) {
        if ($_GET['subida'] === 'ok') {
            echo '<p class="success">✅ Video subido correctamente.</p>';
        } elseif ($_GET['subida'] === 'error') {
            echo '<p class="error">❌ Hubo un error al subir el video. Intenta de nuevo.</p>';
        }
    }
    ?>

    <h2 class="text-center mt-20">🎥 Últimos videos subidos</h2>

    <div class="grilla-videos">
      <?php foreach ($ultimosVideos as $video): ?>
        <div class="card-video">
          <a href="ver_video.php?id=<?= $video["id"] ?>" style="text-decoration: none; color: inherit;">
            <video width="100%" height="auto" preload="metadata">
              <source src="../assets/uploads/<?= htmlspecialchars($video["ruta_archivo"]) ?>" type="video/mp4">
              Tu navegador no soporta la reproducción de video.
            </video>
            <h3><?= htmlspecialchars($video["titulo"]) ?></h3>
          </a>
        </div>
      <?php endforeach; ?>
    </div>

  </div>

</body>
</html>
