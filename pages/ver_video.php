<?php
require_once "../backend/conectar.php";
session_start();

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    echo "<p class='error'>❌ Video no válido.</p>";
    exit;
}

$video_id = intval($_GET["id"]);

$sql = "SELECT v.*, u.username FROM videos v 
        JOIN usuarios u ON v.usuario_id = u.id
        WHERE v.id = :id";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(":id", $video_id);
$stmt->execute();
$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    echo "<p class='error'>❌ Video no encontrado.</p>";
    exit;
}

$usuario_id = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : null;
$stmt = $conexion->prepare("INSERT INTO vistas (video_id, usuario_id) VALUES (:video_id, :usuario_id)");
$stmt->bindParam(":video_id", $video_id);
$stmt->bindParam(":usuario_id", $usuario_id);
$stmt->execute();

$stats_sql = "
    SELECT 
        (SELECT COUNT(*) FROM vistas WHERE video_id = :id) AS total_vistas,
        (SELECT COUNT(*) FROM votos WHERE video_id = :id AND tipo = 'me_gusta') AS likes,
        (SELECT COUNT(*) FROM votos WHERE video_id = :id AND tipo = 'no_me_gusta') AS dislikes
";
$stats_stmt = $conexion->prepare($stats_sql);
$stats_stmt->bindParam(":id", $video_id);
$stats_stmt->execute();
$estadisticas = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($video["titulo"]) ?> - VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="container mt-20">
  <h2><?= htmlspecialchars($video["titulo"]) ?></h2>

  <video width="640" height="360" controls>
    <source src="../assets/uploads/<?= htmlspecialchars($video["ruta_archivo"]) ?>" type="video/mp4">
    Tu navegador no soporta el tag de video.
  </video>

  <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($video["descripcion"])) ?></p>
  <p><strong>Palabras clave:</strong> <?= htmlspecialchars($video["palabras_clave"]) ?></p>
  <p><strong>Lugar de grabación:</strong> <?= htmlspecialchars($video["lugar"]) ?></p>
  <p><strong>Fecha de grabación:</strong> <?= htmlspecialchars($video["fecha_grabacion"]) ?></p>
  <p><strong>Subido por:</strong> <?= htmlspecialchars($video["username"]) ?></p>

  <div class="mt-20">
    <strong>👁️ Visualizaciones:</strong> <?= $estadisticas["total_vistas"] ?><br>
    <strong>👍 Me gusta:</strong> <?= $estadisticas["likes"] ?> |
    <strong>👎 No me gusta:</strong> <?= $estadisticas["dislikes"] ?>
  </div>

  <?php if (isset($_SESSION["usuario_id"])): ?>
    <div class="mt-20">
      <form action="../backend/me_gusta.php" method="POST" class="inline-form">
        <input type="hidden" name="video_id" value="<?= $video_id ?>">
        <input type="hidden" name="tipo" value="me_gusta">
        <button type="submit" class="btn">👍 Me gusta</button>
      </form>

      <form action="../backend/me_gusta.php" method="POST" class="inline-form">
        <input type="hidden" name="video_id" value="<?= $video_id ?>">
        <input type="hidden" name="tipo" value="no_me_gusta">
        <button type="submit" class="btn botonCancelar">👎 No me gusta</button>
      </form>
    </div>
  <?php else: ?>
    <p class="mt-20"><em>🔐 Iniciá sesión para votar.</em></p>
  <?php endif; ?>
</div>

</body>
</html>