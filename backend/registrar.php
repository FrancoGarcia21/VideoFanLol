<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
require_once "conectar.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Encriptar
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $pais = trim($_POST["pais"]);

    try {
        if (!isset($conexion)) {
            throw new Exception("Error: no se estableció la conexión con la base de datos.");
        }

        // Verificar si el usuario ya existe
        $check = $conexion->prepare("SELECT id FROM usuarios WHERE username = :username");
        $check->bindParam(":username", $username);
        $check->execute();

        if ($check->rowCount() > 0) {
            echo "El nombre de usuario ya existe.";
            exit;
        }

        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (username, password, email, fecha_nacimiento, pais) 
                VALUES (:username, :password, :email, :fecha_nacimiento, :pais)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":fecha_nacimiento", $fecha_nacimiento);
        $stmt->bindParam(":pais", $pais);
        $stmt->execute();

        // Redirigir al login con mensaje
        header("Location: ../pages/login.php?registro=exitoso");
        exit;

    } catch (PDOException $e) {
        echo "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error general: " . $e->getMessage();
    }
}
?>
