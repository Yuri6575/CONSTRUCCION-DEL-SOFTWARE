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

// Variables para el formulario
$email = '';
$error = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = "Token de seguridad inválido";
    } else {
        // Sanitizar datos
        $email = sanitizar($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validaciones básicas
        if (empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios";
        } elseif (!validarEmail($email)) {
            $error = "El formato del email no es válido";
        } else {
            // Verificar credenciales en la base de datos
            $conexion = getConexion();
            $stmt = $conexion->prepare("SELECT id, nombre, apellido, email, password FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            if ($resultado->num_rows === 1) {
                $usuario = $resultado->fetch_assoc();
                
                // Verificar contraseña
                if (password_verify($password, $usuario['password'])) {
                    // Iniciar sesión
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_apellido'] = $usuario['apellido'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['ultimo_acceso'] = time();
                    
                    // Log de seguridad
                    logSeguridad("Inicio de sesión exitoso");
                    
                    // Redirigir a la página solicitada o al dashboard
                    $redirect = $_SESSION['redirect_url'] ?? 'dashboard.php';
                    unset($_SESSION['redirect_url']);
                    redireccionar($redirect);
                } else {
                    $error = "Credenciales incorrectas";
                    logSeguridad("Intento de inicio de sesión fallido", "Email: $email");
                }
            } else {
                $error = "Credenciales incorrectas";
                logSeguridad("Intento de inicio de sesión fallido", "Email: $email - Usuario no encontrado");
            }
            
            $stmt->close();
            $conexion->close();
        }
    }
}

// Generar token CSRF
$csrf_token = generarTokenCSRF();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Mi Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">Iniciar Sesión</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['expirado'])): ?>
                <div class="alert alert-warning">Tu sesión ha expirado. Por favor inicia sesión nuevamente.</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['registro'])): ?>
                <div class="alert alert-success">¡Registro exitoso! Ahora puedes iniciar sesión.</div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                <p><a href="index.php">← Volver al inicio</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>