<?php
require_once "../backend/conectar.php";
session_start();

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    echo "<p class='error'>❌ ID de video no válido.</p>";
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

$usuario_id = $_SESSION["usuario_id"] ?? null;
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
$estadisticas = $stats_stmt->fetch(PDO::FETCH_ASSOC) ?: ["total_vistas" => 0, "likes" => 0, "dislikes" => 0];

// Sugerencias
$sugerencias = [];
$palabras = array_filter(array_map('trim', explode(',', $video["palabras_clave"])));
$lugar = trim($video["lugar"]);

$params = [':id' => $video_id];
$condiciones = [];

foreach ($palabras as $i => $palabra) {
    $clave = ":palabra$i";
    $condiciones[] = "palabras_clave LIKE $clave";
    $params[$clave] = "%$palabra%";
}

if (!empty($lugar)) {
    $condiciones[] = "lugar LIKE :lugar";
    $params[":lugar"] = "%$lugar%";
}

if ($condiciones) {
    $sql = "SELECT id, titulo, ruta_archivo FROM videos WHERE id != :id AND (" . implode(" OR ", $condiciones) . ") LIMIT 3";
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $sugerencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($sugerencias)) {
    $fallback = $conexion->prepare("SELECT id, titulo, ruta_archivo FROM videos WHERE id != :id ORDER BY RAND() LIMIT 3");
    $fallback->execute([":id" => $video_id]);
    $sugerencias = $fallback->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($video["titulo"]) ?> - VideoFanLOL</title>
  <link rel="icon" href="../assets/img/favicon.png" type="image/png">
  <link rel="stylesheet" href="../assets/css/estilos.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body>

<?php include("navbar.php"); ?>

<main class="container mt-20">
  <div class="grid-dos-columnas">
    
    <!-- 📹 Sección principal -->
    <section class="contenido-principal">
  <h2><?= htmlspecialchars($video["titulo"]) ?></h2>

  <video controls>
    <source src="../assets/uploads/<?= htmlspecialchars($video["ruta_archivo"]) ?>" type="video/mp4">
    Tu navegador no soporta el tag de video.
  </video>

  <!-- 🔘 Botones de voto -->
  <?php if (isset($_SESSION["usuario_id"])): ?>
  <div class="acciones-voto mt-10">
  <button class="btn votar-btn" data-video="<?= $video_id ?>" data-tipo="me_gusta">
    👍 Me gusta (<span class="contador" id="contador-likes"><?= $estadisticas["likes"] ?></span>)
  </button>
  <button class="btn botonCancelar votar-btn" data-video="<?= $video_id ?>" data-tipo="no_me_gusta">
    👎 No me gusta (<span class="contador" id="contador-dislikes"><?= $estadisticas["dislikes"] ?></span>)
  </button>
</div>

<?php else: ?>
  <p class="mt-10"><em>🔐 Iniciá sesión para votar.</em></p>
<?php endif; ?>



  <!-- 🔽 Detalles (cerrado por defecto) -->
  <details class="desplegable mt-20">
    <summary><strong>📝 Ver detalles del video</strong></summary>
    <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($video["descripcion"])) ?></p>
    <p><strong>Palabras clave:</strong> <?= htmlspecialchars($video["palabras_clave"]) ?></p>
    <p><strong>Lugar de grabación:</strong> <?= htmlspecialchars($video["lugar"]) ?></p>
    <p><strong>Fecha de grabación:</strong> <?= htmlspecialchars($video["fecha_grabacion"]) ?></p>
    <p><strong>Subido por:</strong> <?= htmlspecialchars($video["username"]) ?></p>
    <p><strong>👁️ Visualizaciones:</strong> <?= $estadisticas["total_vistas"] ?></p>
    
  </details>

  <!-- 🌍 Mapa (cerrado por defecto) -->
  <?php if (!empty($video["latitud"]) && !empty($video["longitud"])): ?>
    <details class="desplegable mt-20">
      <summary><strong>📍 Ver ubicación geográfica</strong></summary>
      <div id="mapa" data-lat="<?= $video["latitud"] ?>" data-lng="<?= $video["longitud"] ?>"></div>
    </details>
  <?php endif; ?>
</section>


    <!-- 🎯 Sugerencias -->
    <?php if (!empty($sugerencias)): ?>
      <aside class="sugerencias-lateral">
        <h3>🎯 Sugerencias</h3>
        <?php foreach ($sugerencias as $sugerido): ?>
          <article class="card-video">
            <a href="ver_video.php?id=<?= $sugerido['id'] ?>" class="card-link">
              <video width="100%" height="auto" preload="metadata">
                <source src="../assets/uploads/<?= htmlspecialchars($sugerido["ruta_archivo"]) ?>" type="video/mp4">
              </video>
              <h4><?= htmlspecialchars($sugerido["titulo"]) ?></h4>
            </a>
          </article>
        <?php endforeach; ?>
      </aside>
    <?php endif; ?>

  </div>
</main>

<footer class="footer">
  <p>&copy; 2025 VideoFanLOL - Todos los derechos reservados Franco Garcia</p>
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../assets/js/mapa_ver.js"></script>
<script src="../assets/js/votar.js"></script>
</body>
</html>
