<head>
  <meta charset="UTF-8">
  <title>VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>


<form action="../backend/login.php" method="POST">
  <input type="text" name="username" placeholder="Usuario" required>
  <input type="password" name="password" placeholder="Contraseña" required>
  <button type="submit">Iniciar sesión</button>
</form>

<?php
if (isset($_GET["error"]) && $_GET["error"] === "credenciales") {
    echo "<p style='color:red;'>Usuario o contraseña incorrectos</p>";
}
if (isset($_GET["registro"]) && $_GET["registro"] === "exitoso") {
    echo "<p style='color:green;'>¡Registro exitoso! Ahora podés iniciar sesión.</p>";
}
?>
