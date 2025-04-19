<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión - VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>

  <div class="container mt-20">
    <h1 class="text-center">🔐 Iniciar sesión</h1>

    <form action="../backend/login.php" method="POST" class="formulario">
      <label for="username">Usuario</label>
      <input type="text" name="username" placeholder="Usuario" required>

      <label for="password">Contraseña</label>
      <input type="password" name="password" placeholder="Contraseña" required>

      <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>

    <?php if (isset($_GET["error"]) && $_GET["error"] === "credenciales"): ?>
      <p class="error mt-20">❌ Usuario o contraseña incorrectos</p>
    <?php endif; ?>

    <?php if (isset($_GET["registro"]) && $_GET["registro"] === "exitoso"): ?>
      <p class="success mt-20">✅ ¡Registro exitoso! Ahora podés iniciar sesión.</p>
    <?php endif; ?>
  </div>

</body>
</html>
