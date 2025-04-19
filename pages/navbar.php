<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav style="background-color: #333; padding: 10px; display: flex; align-items: center; flex-wrap: wrap;">
    <span style="color: #fff; font-weight: bold; margin-right: 30px; font-size: 20px;">
        🎬 VideoFanLOL
    </span>

    <?php if (isset($_SESSION['username'])): ?>
        <div style="display: flex; flex-direction: column; color: #fff; margin-right: 30px;">
            <span>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?></span>

            <?php
            require_once "../backend/conectar.php";
            $username = $_SESSION['username'];
            $stmt = $conexion->prepare("SELECT fecha_ultimo_acceso FROM usuarios WHERE username = :username");
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $usuario = $stmt->fetch();

            if ($usuario && $usuario["fecha_ultimo_acceso"]) {
                $ultimoAcceso = new DateTime($usuario["fecha_ultimo_acceso"]);
                $hoy = new DateTime();
                $diferencia = $hoy->diff($ultimoAcceso)->days;

                echo "<span style='font-size: 0.85em; color: #bbb;'>Último acceso: " .
                    $ultimoAcceso->format('d/m/Y H:i') . " ($diferencia días atrás)</span>";
            }
            ?>
        </div>

        <a href="home.php" style="color: #fff; margin-right: 15px; text-decoration: none;">🏠 Inicio / Buscar</a>
        <a href="subir.php" style="color: #fff; margin-right: 15px; text-decoration: none;">⬆️ Subir video</a>
        <a href="../backend/logout.php" style="color: #fff; text-decoration: none;">🔓 Cerrar sesión</a>
    <?php else: ?>
        <a href="home.php" style="color: #fff; margin-right: 15px; text-decoration: none;">🏠 Inicio / Buscar</a>
        <a href="login.php" style="color: #fff; margin-right: 15px; text-decoration: none;">🔐 Iniciar sesión</a>
        <a href="register.php" style="color: #fff; text-decoration: none;">📝 Registrarse</a>
    <?php endif; ?>
</nav>
