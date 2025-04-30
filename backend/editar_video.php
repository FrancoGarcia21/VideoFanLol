<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "conectar.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuarioId = $_SESSION['usuario_id'];
    $videoId = $_POST["id"];

    $stmt = $conexion->prepare("SELECT id FROM videos WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->bindParam(":id", $videoId);
    $stmt->bindParam(":usuario_id", $usuarioId);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo "No tenés permiso para editar este video.";
        exit;
    }

    $titulo = trim($_POST["titulo"]);
    $descripcion = trim($_POST["descripcion"]);
    $palabras = trim($_POST["palabras_clave"]);
    $lugar = trim($_POST["lugar"]);
    $fechaGrabacion = $_POST["fecha_grabacion"];

    $update = $conexion->prepare("
        UPDATE videos SET 
            titulo = :titulo,
            descripcion = :descripcion,
            palabras_clave = :palabras,
            lugar = :lugar,
            fecha_grabacion = :fecha
        WHERE id = :id AND usuario_id = :usuario_id
    ");
    $update->execute([
        ":titulo" => $titulo,
        ":descripcion" => $descripcion,
        ":palabras" => $palabras,
        ":lugar" => $lugar,
        ":fecha" => $fechaGrabacion,
        ":id" => $videoId,
        ":usuario_id" => $usuarioId
    ]);

    header("Location: ../pages/panel.php?editado=ok");
    exit;
}
