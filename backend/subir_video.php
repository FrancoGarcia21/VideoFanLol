<?php
// Mostrar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "conectar.php";
session_start();

// Verifica que el usuario esté logueado
if (!isset($_SESSION['username'])) {
    header("Location: ../pages/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (!isset($conexion)) {
            throw new Exception("Error de conexión a la base de datos.");
        }

        // Datos del formulario
        $titulo = trim($_POST["titulo"]);
        $descripcion = trim($_POST["descripcion"]);
        $palabras_clave = trim($_POST["palabras_clave"]);
        $lugar = trim($_POST["lugar"]);
        $fecha_grabacion = $_POST["fecha_grabacion"];
        $fecha_subida = date("Y-m-d H:i:s");

        // Validar archivo
        if (!isset($_FILES["video"]) || $_FILES["video"]["error"] !== 0) {
            throw new Exception("Error al subir el archivo.");
        }

        $archivo = $_FILES["video"];
        $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

        if ($ext !== "mp4") {
            throw new Exception("Solo se permiten archivos .mp4");
        }

        if ($archivo["size"] > 314572800) { // 300 MB
            throw new Exception("El archivo supera el tamaño máximo permitido de 300MB.");
        }

        // Crear carpeta si no existe
        $carpeta_destino = "../assets/uploads/";
        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0755, true);
        }

        // Nombre único del archivo
        $nombre_archivo = uniqid("video_") . ".mp4";
        $ruta_completa = $carpeta_destino . $nombre_archivo;

        // Mover archivo
        if (!move_uploaded_file($archivo["tmp_name"], $ruta_completa)) {
            throw new Exception("No se pudo mover el archivo subido.");
        }

        // Convertir tamaño a MB
        $tamanio_mb = round($archivo["size"] / 1048576, 2); // 1 MB = 1048576 bytes

        // Obtener ID del usuario
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE username = :username");
        $stmt->bindParam(":username", $_SESSION["username"]);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if (!$usuario) {
            throw new Exception("Usuario no encontrado.");
        }

        $usuario_id = $usuario["id"];

        // Insertar en la tabla videos (sin duración)
        $sql = "INSERT INTO videos (
                    usuario_id, titulo, descripcion, palabras_clave, lugar,
                    fecha_grabacion, fecha_subida, ruta_archivo, tamanio_mb
                ) VALUES (
                    :usuario_id, :titulo, :descripcion, :palabras_clave, :lugar,
                    :fecha_grabacion, :fecha_subida, :ruta_archivo, :tamanio_mb
                )";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":palabras_clave", $palabras_clave);
        $stmt->bindParam(":lugar", $lugar);
        $stmt->bindParam(":fecha_grabacion", $fecha_grabacion);
        $stmt->bindParam(":fecha_subida", $fecha_subida);
        $stmt->bindParam(":ruta_archivo", $nombre_archivo);
        $stmt->bindParam(":tamanio_mb", $tamanio_mb);
        $stmt->execute();

        header("Location: ../pages/home.php?subida=ok");
        exit;

    } catch (Exception $e) {
        echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
