<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['super_pop' => false]);
    exit;
}

require_once "conectar.php";

try {
    $stmt = $conexion->prepare("SELECT super_pop FROM usuarios WHERE username = :username");
    $stmt->bindParam(":username", $_SESSION['username'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['super_pop' => $result && $result['super_pop'] == 1]);
} catch (Exception $e) {
    echo json_encode(['super_pop' => false]);
}
