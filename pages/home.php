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

  <h1>Bienvenido a VideoFanLOL</h1>

  <?php if (isset($_SESSION["username"])): ?>
    <p class="alert">Hola, <?= htmlspecialchars($_SESSION["username"]) ?> 👋</p>
    <a href="../backend/logout.php">Cerrar sesión</a>
  <?php else: ?>
    <a href="register.php">Registrarse</a> |
    <a href="login.php">Iniciar sesión</a>
  <?php endif; ?>

</body>
</html>
