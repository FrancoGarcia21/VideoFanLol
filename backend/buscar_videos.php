<?php
require_once "conectar.php";

$ultimosVideos = [];
$videosBuscados = [];
$cantidadResultados = 0;
$terminoBuscado = '';

try {
    if (isset($_POST['termino']) && !empty(trim($_POST['termino']))) {
        $terminoBuscado = trim($_POST['termino']);
        $termino = '%' . $terminoBuscado . '%';

        $sql = "SELECT videos.id, videos.titulo, videos.ruta_archivo 
                FROM videos
                INNER JOIN usuarios ON videos.usuario_id = usuarios.id
                WHERE videos.titulo LIKE :termino
                   OR usuarios.username LIKE :termino 
                   OR videos.palabras_clave LIKE :termino 
                   OR videos.lugar LIKE :termino 
                   OR videos.fecha_subida LIKE :termino
                ORDER BY videos.fecha_subida DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
        $stmt->execute();
        $videosBuscados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cantidadResultados = count($videosBuscados);
    } else {
        $stmt = $conexion->prepare("SELECT id, titulo, descripcion, ruta_archivo FROM videos ORDER BY fecha_subida DESC LIMIT 5");
        $stmt->execute();
        $ultimosVideos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    echo "<p class='error'>Error al cargar los videos: " . htmlspecialchars($e->getMessage()) . "</p>";
}

return [
    'terminoBuscado' => $terminoBuscado,
    'cantidadResultados' => $cantidadResultados,
    'videosBuscados' => $videosBuscados,
    'ultimosVideos' => $ultimosVideos
];
