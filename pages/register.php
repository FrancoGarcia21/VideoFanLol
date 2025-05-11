<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<script src="../assets/js/validacion_registro.js"></script>


<div id="customAlert" class="custom-alert hidden">
  <div class="custom-alert-box">
    <span id="customAlertIcon">⚠️</span>
    <h2 id="customAlertTitle">Alerta</h2>
    <p id="customAlertMessage">Mensaje aquí</p>
    <button id="customAlertClose">Aceptar</button>
  </div>
</div>


<body>

<?php include("navbar.php"); ?>

<main>
  <section class="container-registro mt-20">
    <h1 class="text-center">📝 Crear cuenta</h1>

    <form action="../backend/registrar.php" method="POST" class="formulario">

      <label for="username">Usuario</label>
      <input type="text" id="username" name="username" placeholder="Usuario" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Email" required>

      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" placeholder="Contraseña" required>

      <label for="fecha_nacimiento">Fecha de nacimiento</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

      <label for="pais">País</label>
      <input type="text" id="pais" name="pais" placeholder="País" required>

      <button type="submit" class="btn btn-primary">Crear cuenta</button>
    </form>
  </section>
</main>

<footer class="text-center mt-20">
  <p>&copy; 2025 VideoFanLOL - Todos los derechos reservados</p>
</footer>

</body>
</html>

