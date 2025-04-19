<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
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

  </div>

</body>
</html>
