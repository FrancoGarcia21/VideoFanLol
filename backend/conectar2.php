<?php
$host = "localhost";
$dbname = "videoFanLOL";
$username = "root";
$password = ""; // En XAMPP normalmente no hay contraseña

try {
    // Crear conexión PDO y asignarla a $conexion
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
