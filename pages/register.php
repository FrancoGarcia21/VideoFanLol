<head>
  <meta charset="UTF-8">
  <title>VideoFanLOL</title>
  <link rel="stylesheet" href="../assets/css/estilos.css">
</head>

<form action="../backend/registrar.php" method="POST">
  <input type="text" name="username" placeholder="Usuario" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Contraseña" required>
  <input type="date" name="fecha_nacimiento" required>
  <input type="text" name="pais" placeholder="País" required>
  <button type="submit">Crear cuenta</button>
</form>
