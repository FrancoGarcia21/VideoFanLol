<?php
// 📌 1. Conexión y búsqueda
require_once "../backend/conectar.php";

$ultimosVideos = [];
$videosBuscados = [];
$cantidadResultados = 0;
$terminoBuscado = '';

try {
    if (isset($_POST['termino']) && !empty(trim($_POST['termino']))) {
        $terminoBuscado = trim($_POST['termino']);
        $termino = '%' . $terminoBuscado . '%';

        $sql = "SELECT videos.id, videos.titulo, videos.ruta_archivo 
        FROM videos
        INNER JOIN usuarios ON videos.usuario_id = usuarios.id
        WHERE usuarios.username LIKE :termino 
           OR videos.palabras_clave LIKE :termino 
           OR videos.lugar LIKE :termino 
           OR videos.fecha_subida LIKE :termino
        ORDER BY videos.fecha_subida DESC";


        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
        $stmt->execute();
        $videosBuscados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cantidadResultados = count($videosBuscados);
    } else {
        $stmt = $conexion->prepare("SELECT id, titulo, descripcion, ruta_archivo FROM videos ORDER BY fecha_subida DESC LIMIT 5");
        $stmt->execute();
        $ultimosVideos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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

  <!-- 🔍 Buscador -->
  <div class="buscador-container">
    <h2 class="text-center">🔍 Buscar videos</h2>
    <form action="" method="POST" class="buscador-form">
      <input type="text" name="termino" placeholder="Buscar por usuario, palabra clave, lugar o fecha" class="buscador-input" value="<?= htmlspecialchars($terminoBuscado) ?>">
      <button type="submit" class="buscador-button">
        Buscar
      </button>
    </form>
  </div>

  <!-- 🎯 Resultados de búsqueda -->
  <?php if (!empty($videosBuscados)): ?>
    <h2 class="text-center mt-20">🎯 Se encontraron <?= $cantidadResultados ?> video<?= $cantidadResultados != 1 ? 's' : '' ?> para "<?= htmlspecialchars($terminoBuscado) ?>"</h2>
    <div class="grilla-videos">
      <?php foreach ($videosBuscados as $video): ?>
        <div class="card-video">
          <a href="ver_video.php?id=<?= $video['id'] ?>" style="text-decoration: none; color: inherit;">
            <video width="100%" height="auto" preload="metadata">
              <source src="../assets/uploads/<?= htmlspecialchars($video["ruta_archivo"]) ?>" type="video/mp4">
              Tu navegador no soporta la reproducción de video.
            </video>
            <h3><?= htmlspecialchars($video["titulo"]) ?></h3>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  
  <?php elseif (isset($_POST['termino'])): ?>
    <h2 class="text-center mt-20">❗ No se encontraron resultados para "<?= htmlspecialchars($terminoBuscado) ?>"</h2>
  <?php endif; ?>

  <!-- 🎥 Últimos videos subidos -->
  <?php if (empty($videosBuscados)): ?>
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
  <?php endif; ?>

</div>

</body>
</html>
