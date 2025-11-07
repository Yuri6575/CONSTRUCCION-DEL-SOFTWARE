<?php
// controllers/procesar_registro.php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    
    // Validaciones
    $errores = [];
    
    if (empty($nombre)) $errores[] = "Nombre requerido";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Email inválido";
    if ($password !== $password2) $errores[] = "Contraseñas no coinciden";
    if (strlen($password) < 6) $errores[] = "Contraseña muy corta";
    
    if (empty($errores)) {
        $conexion = getConexion();
        
        // Verificar email único
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $errores[] = "Email ya registrado";
        } else {
            // Insertar usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $apellido, $email, $hash);
            
            if ($stmt->execute()) {
                // Redirigir al login
                header("Location: login.php?registro=exitoso");
                exit();
            }
        }
    }
}
?>