<?php
// Iniciar sesión segura
require_once '../includes/seguridad.php';
iniciarSesionSegura();

// Incluir configuraciones y funciones
require_once '../config/database.php';
require_once '../includes/funciones.php';

// Si el usuario ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    redireccionar('dashboard.php');
}

// Procesar mensajes
$mensaje = '';
if (isset($_GET['registro'])) {
    $mensaje = mostrarMensaje("¡Registro exitoso! Por favor inicia sesión.", "success");
} elseif (isset($_GET['error'])) {
    $mensaje = mostrarMensaje("Error en el acceso. Verifica tus credenciales.", "error");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Proyecto - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Mi Proyecto</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="login.php">Iniciar Sesión</a>
                <a class="nav-link" href="registro.php">Registrarse</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">Bienvenido a Mi Proyecto</h1>
            <p class="lead mb-4">Una aplicación web moderna desarrollada con PHP y MySQL</p>
            <div class="mt-4">
                <a href="registro.php" class="btn btn-light btn-lg me-2">Comenzar Ahora</a>
                <a href="login.php" class="btn btn-outline-light btn-lg">Iniciar Sesión</a>
            </div>
        </div>
    </section>

    <!-- Mensajes -->
    <div class="container mt-4">
        <?php echo $mensaje; ?>
    </div>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">🚀 Rápido</h5>
                            <p class="card-text">Desarrollado con las últimas tecnologías para máximo rendimiento.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">🔒 Seguro</h5>
                            <p class="card-text">Implementa las mejores prácticas de seguridad web.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">💾 Persistente</h5>
                            <p class="card-text">Almacena tus datos de forma segura en MySQL.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 Proyecto. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>