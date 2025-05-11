<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

require_once "conectar.php";

try {
    // Recolección de datos
    $id = $_POST['id'];
    $usuarioId = $_SESSION['usuario_id'];

    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $palabras_clave = isset($_POST['palabras_clave']) ? implode(',', $_POST['palabras_clave']) : '';
    $pais = trim($_POST['pais']);
    $provincia = trim($_POST['provincia']);
    $ciudad = trim($_POST['ciudad']);
    $fecha_grabacion = $_POST['fecha_grabacion'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];

    // Validación simple extra
    if (!$titulo || !$descripcion || !$fecha_grabacion) {
        throw new Exception("Faltan campos requeridos.");
    }

    // Consulta de actualización
    $sql = "UPDATE videos SET 
        titulo = :titulo,
        descripcion = :descripcion,
        palabras_clave = :palabras_clave,
        pais = :pais,
        provincia = :provincia,
        ciudad = :ciudad,
        fecha_grabacion = :fecha_grabacion,
        latitud = :latitud,
        longitud = :longitud
        WHERE id = :id AND usuario_id = :usuario_id";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':titulo' => $titulo,
        ':descripcion' => $descripcion,
        ':palabras_clave' => $palabras_clave,
        ':pais' => $pais,
        ':provincia' => $provincia,
        ':ciudad' => $ciudad,
        ':fecha_grabacion' => $fecha_grabacion,
        ':latitud' => $latitud,
        ':longitud' => $longitud,
        ':id' => $id,
        ':usuario_id' => $usuarioId
    ]);

    header("Location: ../pages/panel.php?editado=ok");
    exit;

} catch (Exception $e) {
    echo "<p>Error al editar el video: " . htmlspecialchars($e->getMessage()) . "</p>";
}
