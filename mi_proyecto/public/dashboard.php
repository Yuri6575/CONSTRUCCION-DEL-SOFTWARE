<?php
// Iniciar sesión segura y verificar autenticación
require_once '../includes/seguridad.php';
iniciarSesionSegura();
verificarAutenticacion();
verificarInactividad();

// Incluir configuraciones y funciones
require_once '../config/database.php';
require_once '../includes/funciones.php';

// Obtener información del usuario
$conexion = getConexion();
$stmt = $conexion->prepare("SELECT nombre, apellido, email, telefono, fecha_registro FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

// Obtener estadísticas (ejemplo)
$totalUsuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$usuariosHoy = $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE DATE(fecha_registro) = CURDATE()")->fetch_assoc()['total'];

$conexion->close();

// Procesar mensajes
$mensaje = '';
if (isset($_GET['actualizado'])) {
    $mensaje = mostrarMensaje("Perfil actualizado correctamente", "success");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mi Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #343a40;
            color: white;
            height: 100vh;
            position: fixed;
        }
        .sidebar .nav-link {
            color: white;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <h4 class="text-center mb-4">Mi Proyecto