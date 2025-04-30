<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "conectar.php";

$usuarioId = $_SESSION['usuario_id'];
$videoId = $_GET['id'] ?? null;

if ($videoId) {
    // Verificamos que el video sea del usuario
    $stmt = $conexion->prepare("SELECT ruta_archivo FROM videos WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->bindParam(":id", $videoId);
    $stmt->bindParam(":usuario_id", $usuarioId);
    $stmt->execute();
    $video = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($video) {
        // 🗑 Eliminar archivo físico
        $ruta = "../assets/uploads/" . $video['ruta_archivo'];
        if (file_exists($ruta)) {
            unlink($ruta);
        }

        // 🗑 Eliminar vistas
        $delVistas = $conexion->prepare("DELETE FROM vistas WHERE video_id = :id");
        $delVistas->bindParam(":id", $videoId);
        $delVistas->execute();

        // 🗑 Eliminar votos
        $delVotos = $conexion->prepare("DELETE FROM votos WHERE video_id = :id");
        $delVotos->bindParam(":id", $videoId);
        $delVotos->execute();

        // 🗑 Eliminar sugerencias donde aparece
        $delSug1 = $conexion->prepare("DELETE FROM sugerencias WHERE video_origen_id = :id OR video_sugerido_id = :id");
        $delSug1->bindParam(":id", $videoId);
        $delSug1->execute();

        // 🗑 Eliminar video final
        $del = $conexion->prepare("DELETE FROM videos WHERE id = :id AND usuario_id = :usuario_id");
        $del->bindParam(":id", $videoId);
        $del->bindParam(":usuario_id", $usuarioId);
        $del->execute();
    }
}

header("Location: ../pages/panel.php?eliminado=ok");
exit;
