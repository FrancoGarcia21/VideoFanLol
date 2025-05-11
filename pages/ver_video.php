<?php
require_once "../backend/conectar.php";
session_start();

$video = null; // 🔐 Inicializamos $video

// Validar que llegue un ID válido
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    echo "<p class='error'>❌ ID de video no válido.</p>";
    exit;
}

$video_id = intval($_GET["id"]);

// Buscar el video y su usuario
$sql = "SELECT v.*, u.username FROM videos v 
        JOIN usuarios u ON v.usuario_id = u.id
        WHERE v.id = :id";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(":id", $video_id);
$stmt->execute();
$video = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar que exista el video
if (!$video) {
    echo "<p class='error'>❌ Video no encontrado.</p>";
    exit;
}

// Registrar vista si hay usuario logueado
$usuario_id = $_SESSION["usuario_id"] ?? null;
$stmt = $conexion->prepare("INSERT INTO vistas (video_id, usuario_id) VALUES (:video_id, :usuario_id)");
$stmt->bindParam(":video_id", $video_id);
$stmt->bindParam(":usuario_id", $usuario_id);
$stmt->execute();

// Obtener estadísticas
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

// Fallback en caso de error
if (!$estadisticas) {
    $estadisticas = [
        "total_vistas" => 0,
        "likes" => 0,
        "dislikes" => 0
    ];
}

// 🔍 Obtener sugerencias basadas en palabras clave o lugar
$sugerencias = [];

// Extraer palabras clave como array
$palabras = array_filter(array_map('trim', explode(',', $video["palabras_clave"])));
$lugar = trim($video["lugar"]);

// Armar consulta dinámica
$sugerencia_sql = "
    SELECT id, titulo, ruta_archivo
    FROM videos
    WHERE id != :id
      AND (
";

$condiciones = [];
$params = [':id' => $video_id];

// Buscar coincidencias en palabras clave
foreach ($palabras as $i => $palabra) {
    $clave = ":palabra$i";
    $condiciones[] = "palabras_clave LIKE $clave";
    $params[$clave] = "%$palabra%";
}

// Buscar coincidencia por lugar
if (!empty($lugar)) {
    $condiciones[] = "lugar LIKE :lugar";
    $params[":lugar"] = "%$lugar%";
}

$sugerencia_sql .= implode(" OR ", $condiciones) . ")
LIMIT 3";

$sugerencia_stmt = $conexion->prepare($sugerencia_sql);
$sugerencia_stmt->execute($params);
$sugerencias = $sugerencia_stmt->fetchAll(PDO::FETCH_ASSOC);

// Si no se encontró nada, buscar cualquiera
if (empty($sugerencias)) {
    $fallback_stmt = $conexion->prepare("SELECT id, titulo, ruta_archivo FROM videos WHERE id != :id ORDER BY RAND() LIMIT 3");
    $fallback_stmt->execute([":id" => $video_id]);
    $sugerencias = $fallback_stmt->fetchAll(PDO::FETCH_ASSOC);
}



?>




<!DOCTYPE html>

<html lang="es">
<head>
<link rel="icon" type="image/png" href="../assets/img/favicon.png">

  <meta charset="UTF-8">
  <title><?= htmlspecialchars($video["titulo"]) ?> - VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">

  <!-- 🌍 Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <style>
    #mapa {
      height: 300px;
      margin-top: 20px;
      border: 2px solid #ccc;
      border-radius: 6px;
    }
  </style>
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

  <!-- 🌍 Mostrar mapa si hay coordenadas -->
  <?php if (!empty($video["latitud"]) && !empty($video["longitud"])): ?>
    <h3>📍 Ubicación del video:</h3>
    <div id="mapa"></div>

    <!-- 🌍 Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const mapa = L.map("mapa").setView([<?= $video["latitud"] ?>, <?= $video["longitud"] ?>], 10);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution: "© OpenStreetMap contributors"
        }).addTo(mapa);

        L.marker([<?= $video["latitud"] ?>, <?= $video["longitud"] ?>])
          .addTo(mapa)
          .bindPopup("Lugar de grabación")
          .openPopup();
      });
    </script>
  <?php endif; ?>

  <div class="mt-20">
    <strong>👁️ Visualizaciones:</strong> <?= $estadisticas["total_vistas"] ?><br>
    <strong>👍 Me gusta:</strong> <span id="contador-likes"><?= $estadisticas["likes"] ?></span> |
    <strong>👎 No me gusta:</strong> <span id="contador-dislikes"><?= $estadisticas["dislikes"] ?></span>
  </div>

  <?php if (isset($_SESSION["usuario_id"])): ?>
    <div class="mt-20">
      <button class="btn votar-btn" data-video="<?= $video_id ?>" data-tipo="me_gusta">👍 Me gusta</button>
      <button class="btn botonCancelar votar-btn" data-video="<?= $video_id ?>" data-tipo="no_me_gusta">👎 No me gusta</button>
    </div>
  <?php else: ?>
    <p class="mt-20"><em>🔐 Iniciá sesión para votar.</em></p>
  <?php endif; ?>


 <!-- Sugerencias dle video -->


  <?php if (!empty($sugerencias)): ?>
  <div class="mt-40">
    <h3>🎯 Sugerencias relacionadas</h3>
    <div class="grilla-videos">
      <?php foreach ($sugerencias as $sugerido): ?>
        <div class="card-video">
          <a href="ver_video.php?id=<?= $sugerido['id'] ?>" style="text-decoration: none; color: inherit;">
            <video width="100%" height="auto" preload="metadata">
              <source src="../assets/uploads/<?= htmlspecialchars($sugerido["ruta_archivo"]) ?>" type="video/mp4">
            </video>
            <h4><?= htmlspecialchars($sugerido["titulo"]) ?></h4>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

</div>

<script src="../assets/js/votar.js"></script>
<footer class="text-center mt-20">
  <p>&copy; 2025 VideoFanLOL - Todos los derechos reservados Franco Garcia</p>
</footer>
</body>
</html>
