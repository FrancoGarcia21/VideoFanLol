<?php
$host = "db";                    // 🔁 El nombre del servicio del contenedor MySQL
$dbname = "videoFanLOL";
$username = "videofan";         // 🧑 Usuario definido en docker-compose.yml
$password = "fan123";           // 🔐 Contraseña definida también

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
