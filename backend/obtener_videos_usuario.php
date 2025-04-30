<?php
require_once "conectar.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioId = $_SESSION['usuario_id'];

try {
    // 🔹 Obtener videos del usuario con stats
    $stmt = $conexion->prepare("
        SELECT v.id, v.titulo, v.descripcion, v.palabras_clave, v.ruta_archivo,
            IFNULL(COUNT(DISTINCT vi.id), 0) AS visualizaciones,
            IFNULL(SUM(CASE WHEN vo.tipo = 'me_gusta' THEN 1 ELSE 0 END), 0) AS me_gusta,
            IFNULL(SUM(CASE WHEN vo.tipo = 'no_me_gusta' THEN 1 ELSE 0 END), 0) AS no_me_gusta
        FROM videos v
        LEFT JOIN vistas vi ON v.id = vi.video_id
        LEFT JOIN votos vo ON v.id = vo.video_id
        WHERE v.usuario_id = :usuario_id
        GROUP BY v.id
        ORDER BY v.fecha_subida DESC
    ");
    $stmt->bindParam(":usuario_id", $usuarioId);
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 🔹 Verificar si es super pop usuario (100 vistas en total últimos 3 días)
    $stmt2 = $conexion->prepare("
        SELECT COUNT(*) as total_vistas
        FROM vistas vi
        INNER JOIN videos v ON vi.video_id = v.id
        WHERE v.usuario_id = :usuario_id AND vi.fecha >= NOW() - INTERVAL 3 DAY
    ");
    $stmt2->bindParam(":usuario_id", $usuarioId);
    $stmt2->execute();
    $vistasRecientes = $stmt2->fetch(PDO::FETCH_ASSOC);

    $esSuperPop = $vistasRecientes && $vistasRecientes['total_vistas'] >= 100;

    return [
        'videos' => $videos,
        'super_pop' => $esSuperPop
    ];
} catch (Exception $e) {
    return [
        'videos' => [],
        'super_pop' => false
    ];
}
