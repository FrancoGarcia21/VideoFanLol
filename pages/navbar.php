<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>



<nav class="navbar">
    <span class="navbar-title">🎬 VideoFanLOL</span>

    <?php if (isset($_SESSION['username'])): ?>
        <div class="user-info">
    <span>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?></span>

    <?php
    if (!empty($_SESSION['ultimo_acceso_anterior'])) {
        $ultimoAcceso = new DateTime($_SESSION['ultimo_acceso_anterior']);
        $hoy = new DateTime();
        $diferencia = $hoy->diff($ultimoAcceso)->days;

        echo "<span class='ultimo-acceso'>👋 Bienvenido nuevamente después de $diferencia día" . ($diferencia !== 1 ? 's' : '') . ".</span>";
        echo "<span class='ultimo-acceso'>Último acceso: " . $ultimoAcceso->format('d/m/Y H:i') . "</span>";
    }
    ?>
</div>


<a href="home.php" class="navbar-link">🏠 Inicio / Buscar</a>
<a href="subir.php" class="navbar-link">⬆️ Subir video</a>
<a href="panel.php" class="navbar-link">📊 Mi panel</a>
<a href="../backend/logout.php" class="navbar-link">🔓 Cerrar sesión</a>

    <?php else: ?>
        <a href="home.php" class="navbar-link">🏠 Inicio / Buscar</a>
        <a href="login.php" class="navbar-link">🔐 Iniciar sesión</a>
        <a href="register.php" class="navbar-link">📝 Registrarse</a>
    <?php endif; ?>
</nav>
