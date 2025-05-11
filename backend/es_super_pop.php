<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['super_pop' => false, 'motivo' => 'No logueado']);
    exit;
}

require_once "conectar.php";

try {
    // Obtener el ID del usuario
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE username = :username");
    $stmt->bindParam(":username", $_SESSION['username'], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['super_pop' => false, 'motivo' => 'Usuario no encontrado']);
        exit;
    }

    $usuario_id = $user['id'];

    // ✅ Calcular número de días con 100+ vistas
    $stmt = $conexion->prepare("
        SELECT DATE(fecha) AS dia, COUNT(*) AS total_vistas
        FROM vistas
        WHERE usuario_id = :usuario_id
        GROUP BY DATE(fecha)
        HAVING total_vistas >= 100
        ORDER BY dia DESC
    ");
    $stmt->bindParam(":usuario_id", $usuario_id);
    $stmt->execute();
    $dias_validos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $es_super_pop = count($dias_validos) >= 3;

    echo json_encode([
        'super_pop' => $es_super_pop,
        'dias_validos' => $dias_validos
    ]);

} catch (Exception $e) {
    echo json_encode([
        'super_pop' => false,
        'error' => $e->getMessage()
    ]);
}
