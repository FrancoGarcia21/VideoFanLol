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
</head>
<body>
<?php include("navbar.php"); ?>

<div class="container mt-20">
    <h1 class="titulo-principal">📊 Panel de usuario</h1>

    <p class="estado-pop">
        <?= $esSuperPop ? "🌟 Sos un SUPER POP USUARIO" : "🤓 Aún no sos super pop usuario" ?>
    </p>

    <div class="grilla-videos">
        <?php foreach ($videos as $video): ?>
            <div class="card-video">
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
                        <a class="btn-editar" href="editar_video.php?id=<?= $video['id'] ?>">✏️ Editar</a>
                        <a class="btn-eliminar" href="eliminar_video.php?id=<?= $video['id'] ?>" onclick="return confirm('¿Seguro que querés eliminar este video?')">🗑 Eliminar</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
