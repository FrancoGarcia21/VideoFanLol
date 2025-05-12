<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../backend/conectar.php';

$usuario_id = $_SESSION["usuario_id"];
$terminoBuscado = $_GET['termino'] ?? '';
$videos = [];
$esSuperPop = false;

try {
    $termino = '%' . trim($terminoBuscado) . '%';

    $sql = "
        SELECT 
            v.id,
            v.titulo,
            v.descripcion,
            v.palabras_clave,
            v.ruta_archivo,
            IFNULL(vs.total_vistas, 0) AS vistas,
            IFNULL(vm.me_gusta, 0) AS me_gusta,
            IFNULL(vm.no_me_gusta, 0) AS no_me_gusta
        FROM videos v
        LEFT JOIN (
            SELECT video_id, COUNT(*) AS total_vistas
            FROM vistas
            GROUP BY video_id
        ) AS vs ON vs.video_id = v.id
        LEFT JOIN (
            SELECT 
                video_id,
                SUM(tipo = 'me_gusta') AS me_gusta,
                SUM(tipo = 'no_me_gusta') AS no_me_gusta
            FROM votos
            GROUP BY video_id
        ) AS vm ON vm.video_id = v.id
        WHERE v.usuario_id = :usuario_id";

    if (!empty($terminoBuscado)) {
        $sql .= " AND (
            v.titulo LIKE :termino1 OR
            v.palabras_clave LIKE :termino2 OR
            v.lugar LIKE :termino3 OR
            v.fecha_subida LIKE :termino4
        )";
    }

    $sql .= " ORDER BY v.fecha_subida DESC LIMIT 8";


    $stmt = $conexion->prepare($sql);

    $params = [':usuario_id' => $usuario_id];

    if (!empty($terminoBuscado)) {
        $params[':termino1'] = $termino;
        $params[':termino2'] = $termino;
        $params[':termino3'] = $termino;
        $params[':termino4'] = $termino;
    }

    $stmt->execute($params);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificamos si es super pop usuario usando la vista
    $stmtPop = $conexion->prepare("SELECT 1 FROM super_pop_usuarios WHERE usuario_id = :usuario_id LIMIT 1");
    $stmtPop->execute([':usuario_id' => $usuario_id]);
    $esSuperPop = $stmtPop->fetchColumn() !== false;

} catch (Exception $e) {
    echo "<p class='error'>Error al obtener videos: " . htmlspecialchars($e->getMessage()) . "</p>";
}
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

    <!-- 🔍 Buscador -->
    <section class="buscador-container">
        <form class="buscador-form" method="GET" action="panel.php">
            <input type="text" name="termino" class="buscador-input" placeholder="Buscar entre tus videos" value="<?= htmlspecialchars($terminoBuscado) ?>">
            <button type="submit" class="buscador-button">Buscar</button>
            <?php if ($terminoBuscado): ?>
                <a href="panel.php" class="btn-reset">🧹 Limpiar</a>
            <?php endif; ?>
        </form>
    </section>

    <!-- 🎥 Videos -->
    <section class="grilla-videos">
        <?php if (count($videos) === 0): ?>
            <p class="sin-resultados">No se encontro videos<?= htmlspecialchars($terminoBuscado) ?>.</p>
        <?php else: ?>
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
                        <p>
                            👁 <?= $video['vistas'] ?> |
                            👍 <?= $video['me_gusta'] ?> |
                            👎 <?= $video['no_me_gusta'] ?>
                        </p>
                        <div class="acciones-video">
                            <a class="btn-editar" href="editar.php?id=<?= $video['id'] ?>">✏️ Editar</a>
                            <a class="btn-eliminar" href="../backend/eliminar_video.php?id=<?= $video['id'] ?>">🗑 Eliminar</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<footer class="text-center mt-20">
    <p>&copy; 2025 VideoFanLOL - Todos los derechos reservados Franco Garcia</p>
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
