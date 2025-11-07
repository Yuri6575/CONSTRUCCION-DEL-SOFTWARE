<?php
// controllers/procesar_login.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $conexion = getConexion();
    $stmt = $conexion->prepare("SELECT id, nombre, apellido, password FROM usuarios WHERE email = ? AND activo = TRUE");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            header("Location: dashboard.php");
            exit();
        }
    }
    
    // Error de credenciales
    header("Location: login.php?error=1");
    exit();
}
?>