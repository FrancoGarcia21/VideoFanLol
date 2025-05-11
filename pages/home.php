<?php
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
  <meta charset="UTF-8">
  <title>VideoFanLOL</title>
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>

<?php include("navbar.php"); ?>

<main class="container text-center mt-20">

  <?php if (isset($_GET['subida'])): ?>
    <section class="estado-subida">
      <?php if ($_GET['subida'] === 'ok'): ?>
        <p class="success">✅ Video subido correctamente.</p>
      <?php elseif ($_GET['subida'] === 'error'): ?>
        <p class="error">❌ Hubo un error al subir el video. Intenta de nuevo.</p>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <!-- 🔍 Buscador -->
  <section class="buscador-container">
    <h2>🔍 Buscar videos</h2>
    <form action="" method="POST" class="buscador-form">
      <input type="text" name="termino" placeholder="Buscar por usuario, palabra clave, lugar o fecha" class="buscador-input" value="<?= htmlspecialchars($terminoBuscado) ?>">
      <button type="submit" class="buscador-button">Buscar</button>
    </form>
  </section>

  <!-- 🎯 Resultados de búsqueda -->
  <?php if (!empty($videosBuscados)): ?>
    <section class="resultados-busqueda mt-20">
      <h2>🎯 Se encontraron <?= $cantidadResultados ?> video<?= $cantidadResultados != 1 ? 's' : '' ?> para "<?= htmlspecialchars($terminoBuscado) ?>"</h2>
      <div class="grilla-videos">
        <?php foreach ($videosBuscados as $video): ?>
          <div class="card-video">
            <a href="ver_video.php?id=<?= $video['id'] ?>" class="enlace-video">
              <video preload="metadata">
                <source src="../assets/uploads/<?= htmlspecialchars($video["ruta_archivo"]) ?>" type="video/mp4">
                Tu navegador no soporta la reproducción de video.
              </video>
              <h3><?= htmlspecialchars($video["titulo"]) ?></h3>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

  <?php elseif (isset($_POST['termino'])): ?>
    <section class="sin-resultados mt-20">
      <h2>❗ No se encontraron resultados para "<?= htmlspecialchars($terminoBuscado) ?>"</h2>
    </section>
  <?php endif; ?>

  <!-- 🎥 Últimos videos subidos -->
  <?php if (empty($videosBuscados)): ?>
    <section class="ultimos-videos mt-20">
      <h2>🎥 Últimos videos subidos</h2>
      <div class="grilla-videos">
        <?php foreach ($ultimosVideos as $video): ?>
          <div class="card-video">
            <a href="ver_video.php?id=<?= $video["id"] ?>" class="enlace-video">
              <video preload="metadata">
                <source src="../assets/uploads/<?= htmlspecialchars($video["ruta_archivo"]) ?>" type="video/mp4">
                Tu navegador no soporta la reproducción de video.
              </video>
              <h3><?= htmlspecialchars($video["titulo"]) ?></h3>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

</main>
<footer class="text-center mt-20">
  <p>&copy; 2025 VideoFanLOL - Todos los derechos reservados Franco Garcia</p>
</footer>
</body>
</html>
