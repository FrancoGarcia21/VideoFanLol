<?php
require_once "../backend/conectar.php"; // adaptalo si tu ruta cambia

if (isset($_POST['termino'])) {
    $termino = trim($_POST['termino']);
    $termino = "%$termino%"; // Para usar LIKE

    // Buscamos en usuario, palabra clave, lugar y fecha de subida
    $sql = "SELECT id, titulo, ruta_archivo 
            FROM videos 
            WHERE usuario_nombre LIKE :termino 
               OR palabras_clave LIKE :termino 
               OR lugar LIKE :termino 
               OR fecha_subida LIKE :termino 
            ORDER BY fecha_subida DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $videos = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resultados de búsqueda</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="container text-center mt-20">
  <h2 class="text-center">🔎 Resultados de búsqueda</h2>

  <div class="grilla-videos">
    <?php if (count($videos) > 0): ?>
      <?php foreach ($videos as $video): ?>
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
    <?php else: ?>
      <p>No se encontraron videos para ese término.</p>
    <?php endif; ?>
  </div>

</div>

</body>
</html>
