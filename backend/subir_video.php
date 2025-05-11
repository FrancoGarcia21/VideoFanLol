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
        $palabras_clave_array = $_POST['palabras_clave'] ?? [];
        $palabras_clave = implode(',', array_map('trim', $palabras_clave_array));

        // Armar lugar a partir de país, provincia y ciudad
        $pais = trim($_POST["pais"]);
        $provincia = trim($_POST["provincia"]);
        $ciudad = trim($_POST["ciudad"]);
        $lugar = "$pais, $provincia, $ciudad";

        $fecha_grabacion = $_POST["fecha_grabacion"];
        $fecha_subida = date("Y-m-d H:i:s");
        $latitud = $_POST["latitud"] ?? null;
        $longitud = $_POST["longitud"] ?? null;

        // Validar archivo
        if (!isset($_FILES["video"]) || $_FILES["video"]["error"] !== 0) {
            throw new Exception("Error al subir el archivo.");
        }

        $archivo = $_FILES["video"];
        $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

        if ($ext !== "mp4") {
            throw new Exception("Solo se permiten archivos .mp4");
        }

        // Obtener ID del usuario
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE username = :username");
        $stmt->bindParam(":username", $_SESSION["username"]);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if (!$usuario) {
            throw new Exception("Usuario no encontrado.");
        }

        $usuario_id = $usuario["id"];

        // ✅ Verificar si el usuario es super_pop
        $stmt = $conexion->prepare("
            SELECT COUNT(*) AS dias_validos FROM (
                SELECT DATE(fecha) AS dia, COUNT(*) AS total_vistas
                FROM vistas
                WHERE usuario_id = :usuario_id
                GROUP BY DATE(fecha)
                HAVING total_vistas >= 100
                ORDER BY dia DESC
                LIMIT 3
            ) AS sub
        ");
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $es_super_pop = $resultado && $resultado["dias_validos"] >= 3;

        // ✅ Validar tamaño según tipo de usuario
        $limite_bytes = $es_super_pop ? 524288000 : 314572800; // 500MB : 300MB
        if ($archivo["size"] > $limite_bytes) {
            throw new Exception("El archivo supera el tamaño máximo permitido de " . ($limite_bytes / 1048576) . "MB.");
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
        $tamanio_mb = round($archivo["size"] / 1048576, 2);

        // Insertar en la base de datos
        $sql = "INSERT INTO videos (
                    usuario_id, titulo, descripcion, palabras_clave, lugar,
                    fecha_grabacion, fecha_subida, ruta_archivo, tamanio_mb,
                    latitud, longitud
                ) VALUES (
                    :usuario_id, :titulo, :descripcion, :palabras_clave, :lugar,
                    :fecha_grabacion, :fecha_subida, :ruta_archivo, :tamanio_mb,
                    :latitud, :longitud
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
        $stmt->bindParam(":latitud", $latitud);
        $stmt->bindParam(":longitud", $longitud);
        $stmt->execute();

        header("Location: ../pages/home.php?subida=ok");
        exit;

    } catch (Exception $e) {
        echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
