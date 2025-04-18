<?php
session_start(); // Iniciar sesión
require_once "conectar.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    try {
        $sql = "SELECT id, password FROM usuarios WHERE username = :username";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $usuario["password"])) {
                // Guardar en sesión
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["username"] = $username;

                // Actualizar último acceso
                $update = $conexion->prepare("UPDATE usuarios SET fecha_ultimo_acceso = NOW() WHERE id = :id");
                $update->bindParam(":id", $usuario["id"]);
                $update->execute();

                header("Location: ../pages/home.php");
                exit;
            } else {
                header("Location: ../pages/login.php?error=credenciales");
                exit;
            }
        } else {
            header("Location: ../pages/login.php?error=credenciales");
            exit;
        }

    } catch (PDOException $e) {
        echo "Error al iniciar sesión: " . $e->getMessage();
    }
}
?>
