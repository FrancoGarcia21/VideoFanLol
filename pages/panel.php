<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$datos = include(__DIR__ . '/../backend/obtener_videos_usuario.php');
$videos = $datos['videos'];
$esSuperPop = $datos['super_pop'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="icon" href="../assets/img/favicon.png" type="image/x-icon">
    <script defer src="../assets/js/panel.js"></script>
</head>
<body>

<header>
    <?php include("navbar.php"); ?>
</header>

<main class="container mt-20">
    <section>
        <h1 class="titulo-principal">📊 Panel de usuario</h1>
        <p class="estado-pop">
            <?= $esSuperPop ? "🌟 Sos un SUPER POP USUARIO" : "🤓 Aún no sos super pop usuario" ?>
        </p>
    </section>

    <section class="grilla-videos">
        <?php foreach ($videos as $video): ?>
            <article class="card-video">
                <video width="100%" height="auto" preload="metadata">
                    <source src="../assets/uploads/<?= htmlspecialchars($video['ruta_archivo']) ?>" type="video/mp4">
                    Tu navegador no soporta la reproducción de video.
                </video>
                <div class="info-video">
                    <h3><?= htmlspecialchars($video['titulo']) ?></h3>
                    <p><?= htmlspecialchars($video['descripcion']) ?></p>
                    <p><strong>Palabras clave:</strong> <?= htmlspecialchars($video['palabras_clave']) ?></p>
                    <p>👁 <?= $video['visualizaciones'] ?> vistas | 👍 <?= $video['me_gusta'] ?> | 👎 <?= $video['no_me_gusta'] ?></p>
                    <div class="acciones-video">
                        <a class="btn-editar" href="editar.php?id=<?= $video['id'] ?>">✏️ Editar</a>
                        <a class="btn-eliminar" href="../backend/eliminar_video.php?id=<?= $video['id'] ?>">🗑 Eliminar</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>

<footer class="text-center mt-20">
  <p>&copy; 2025 VideoFanLOL - Todos los derechos reservados</p>
</footer>

<!-- Modal personalizado -->
<div id="modalConfirmacion" class="modal hidden">
    <div class="modal-contenido">
        <p>¿Seguro que querés eliminar este video?</p>
        <div class="modal-botones">
            <button id="btnConfirmar" class="btn-confirmar">Eliminar</button>
            <button id="btnCancelar" class="btn-cancelar">Cancelar</button>
        </div>
    </div>
</div>

</body>
</html>
