<?php
session_start();
require_once "conectar.php";

header('Content-Type: application/json');

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["success" => false, "message" => "No autenticado"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $video_id = $_POST["video_id"] ?? null;
    $tipo = $_POST["tipo"] ?? null;
    $usuario_id = $_SESSION["usuario_id"];

    if (!$video_id || !in_array($tipo, ["me_gusta", "no_me_gusta"])) {
        echo json_encode(["success" => false, "message" => "Datos inválidos"]);
        exit;
    }

    try {
        // Verificar si ya votó
        $check = $conexion->prepare("SELECT id FROM votos WHERE usuario_id = :usuario_id AND video_id = :video_id");
        $check->execute([
            ":usuario_id" => $usuario_id,
            ":video_id" => $video_id
        ]);

        if ($check->rowCount() > 0) {
            // Actualiza el voto
            $update = $conexion->prepare("UPDATE votos SET tipo = :tipo WHERE usuario_id = :usuario_id AND video_id = :video_id");
            $update->execute([
                ":tipo" => $tipo,
                ":usuario_id" => $usuario_id,
                ":video_id" => $video_id
            ]);
        } else {
            // Inserta nuevo voto
            $insert = $conexion->prepare("INSERT INTO votos (usuario_id, video_id, tipo) VALUES (:usuario_id, :video_id, :tipo)");
            $insert->execute([
                ":usuario_id" => $usuario_id,
                ":video_id" => $video_id,
                ":tipo" => $tipo
            ]);
        }

        // Contadores actualizados
        $stats = $conexion->prepare("
            SELECT 
                (SELECT COUNT(*) FROM votos WHERE video_id = :video_id AND tipo = 'me_gusta') AS likes,
                (SELECT COUNT(*) FROM votos WHERE video_id = :video_id AND tipo = 'no_me_gusta') AS dislikes
        ");
        $stats->execute([":video_id" => $video_id]);
        $result = $stats->fetch(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "likes" => $result["likes"], "dislikes" => $result["dislikes"]]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error en la base de datos"]);
    }
}
?>
