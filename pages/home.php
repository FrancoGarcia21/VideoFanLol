<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>

  <div class="container text-center mt-20">
    <h1>🎬 Bienvenido a VideoFanLOL</h1>

    <?php if (isset($_SESSION["username"])): ?>
      <p class="alert">Hola, <?= htmlspecialchars($_SESSION["username"]) ?> 👋</p>
      <a href="../backend/logout.php" class="btn btn-secundario">Cerrar sesión</a>
    <?php else: ?>
      <div class="grupo-links mt-20">
        <a href="register.php" class="btn btn-primary">Registrarse</a>
        <a href="login.php" class="btn btn-secundario">Iniciar sesión</a>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
