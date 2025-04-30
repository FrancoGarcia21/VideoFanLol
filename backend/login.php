<?php
session_start();
require_once "conectar.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    try {
        // 🔍 1. Buscar usuario
        $sql = "SELECT id, password, fecha_ultimo_acceso FROM usuarios WHERE username = :username";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // 🔐 2. Verificar contraseña
            if (password_verify($password, $usuario["password"])) {

                // 🧠 3. Guardar datos en sesión
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["username"] = $username;
                $_SESSION["ultimo_acceso_anterior"] = $usuario["fecha_ultimo_acceso"]; // ✅ CLAVE

                // 🕒 4. Actualizar último acceso
                $update = $conexion->prepare("UPDATE usuarios SET fecha_ultimo_acceso = NOW() WHERE id = :id");
                $update->bindParam(":id", $usuario["id"]);
                $update->execute();

                // 🔁 5. Redirigir
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
        echo "Error al iniciar sesión: " . htmlspecialchars($e->getMessage());
    }
}
?>
