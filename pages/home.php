<?php
include_once("../init.php");


// 📌 Cargar resultados desde archivo backend
$archivo = realpath(__DIR__ . '/../backend/buscar_videos.php');
$datosBusqueda = $archivo && file_exists($archivo) ? include($archivo) : null;

if (!is_array($datosBusqueda)) {
    $datosBusqueda = [
        'terminoBuscado' => '',
        'cantidadResultados' => 0,
        'videosBuscados' => [],
        'ultimosVideos' => []
    ];
}


$terminoBuscado = $datosBusqueda['terminoBuscado'];
$cantidadResultados = $datosBusqueda['cantidadResultados'];
$videosBuscados = $datosBusqueda['videosBuscados'];
$ultimosVideos = $datosBusqueda['ultimosVideos'];
?>



<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../assets/img/favicon.png">

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
