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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Video - VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
  <div class="container mt-20">
    <h2>✏️ Editar video</h2>

    <form class="formulario" action="../backend/editar_video.php" method="POST">
      <input type="hidden" name="id" value="<?= htmlspecialchars($video['id']) ?>">

      <label for="titulo">Título:</label><br>
      <input type="text" name="titulo" value="<?= htmlspecialchars($video['titulo']) ?>" required><br><br>

      <label for="descripcion">Descripción:</label><br>
      <textarea name="descripcion" rows="4" required><?= htmlspecialchars($video['descripcion']) ?></textarea><br><br>

      <label for="palabras_clave">Palabras clave:</label><br>
      <input type="text" name="palabras_clave" value="<?= htmlspecialchars($video['palabras_clave']) ?>" required><br><br>

      <label for="lugar">Lugar de grabación:</label><br>
      <input type="text" name="lugar" value="<?= htmlspecialchars($video['lugar']) ?>" required><br><br>

      <label for="fecha_grabacion">Fecha de grabación:</label><br>
      <input type="date" name="fecha_grabacion" value="<?= htmlspecialchars($video['fecha_grabacion']) ?>" required><br><br>

      <button type="submit">💾 Guardar cambios</button>
    </form>
  </div>
</body>
</html>
