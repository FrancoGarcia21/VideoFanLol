<?php
require_once "../backend/conectar.php"; // Ajustar si cambia la ruta

$videos = [];
$terminoBuscado = '';

try {
    if (!empty($_POST['termino'] ?? '')) {
        $terminoBuscado = trim($_POST['termino']);
        $terminoLike = "%$terminoBuscado%";

        $sql = "
            SELECT id, titulo, ruta_archivo 
            FROM videos 
            WHERE usuario_nombre LIKE :termino 
               OR palabras_clave LIKE :termino 
               OR lugar LIKE :termino 
               OR fecha_subida LIKE :termino 
            ORDER BY fecha_subida DESC
            LIMIT 8
        ";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':termino', $terminoLike, PDO::PARAM_STR);
        $stmt->execute();
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Si no se busca nada, traigo los últimos 8 videos subidos
        $stmt = $conexion->prepare("
            SELECT id, titulo, ruta_archivo 
            FROM videos 
            ORDER BY fecha_subida DESC 
            LIMIT 8
        ");
        $stmt->execute();
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    echo "<p class='error'>Error al buscar videos: " . htmlspecialchars($e->getMessage()) . "</p>";
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

<main class="container text-center mt-20">
  <section>
    <h2 class="text-center">🔎 Resultados de búsqueda</h2>
    
    <?php if ($terminoBuscado): ?>
      <p>Mostrando resultados para: <strong>"<?= htmlspecialchars($terminoBuscado) ?>"</strong></p>
    <?php else: ?>
      <p>Últimos videos subidos</p>
    <?php endif; ?>

    <div class="grilla-videos mt-20">
      <?php if (!empty($videos)): ?>
        <?php foreach ($videos as $video): ?>
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
      <?php else: ?>
        <p>No se encontraron videos para ese término.</p>
      <?php endif; ?>
    </div>
  </section>
</main>

</body>
</html>
