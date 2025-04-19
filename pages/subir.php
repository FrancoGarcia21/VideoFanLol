<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirige si no está logueado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include("navbar.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir Video - VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
  <div class="container mt-20">
    <h2>⬆️ Subir un nuevo video</h2>

    <form class="formulario" action="../backend/subir_video.php" method="POST" enctype="multipart/form-data">

      <label for="video">Archivo de video (.mp4, máx. 300MB):</label><br>
      <input type="file" name="video" accept="video/mp4" required><br><br>

      <label for="titulo">Título:</label><br>
      <input type="text" name="titulo" required><br><br>

      <label for="descripcion">Descripción:</label><br>
      <textarea name="descripcion" rows="4" required></textarea><br><br>

      <label for="palabras_clave">Palabras clave (separadas por comas, máx. 10):</label><br>
      <input type="text" name="palabras_clave" required><br><br>

      <label for="lugar">Lugar de grabación:</label><br>
      <input type="text" name="lugar" required><br><br>

      <label for="fecha_grabacion">Fecha de grabación:</label><br>
      <input type="date" name="fecha_grabacion" required><br><br>

      <button type="submit">📤 Subir video</button>
    </form>
  </div>
  <script src="/assets/js/validar_video.js"></script>
  

</body>
</html>
