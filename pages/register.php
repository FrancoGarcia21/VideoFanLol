<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../assets/img/favicon.png">

  <meta charset="UTF-8">
  <title>Registro - VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
<?php include("navbar.php"); ?>

  <div class="container mt-20">
    <h1 class="text-center">📝 Crear cuenta</h1>

    <form action="../backend/registrar.php" method="POST" class="formulario">

      <label for="username">Usuario</label>
      <input type="text" name="username" placeholder="Usuario" required>

      <label for="email">Email</label>
      <input type="email" name="email" placeholder="Email" required>

      <label for="password">Contraseña</label>
      <input type="password" name="password" placeholder="Contraseña" required>

      <label for="fecha_nacimiento">Fecha de nacimiento</label>
      <input type="date" name="fecha_nacimiento" required>

      <label for="pais">País</label>
      <input type="text" name="pais" placeholder="País" required>

      <button type="submit" class="btn btn-primary">Crear cuenta</button>
    </form>

  </div>

</body>
</html>
