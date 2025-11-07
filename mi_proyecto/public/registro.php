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
$datos = [
    'nombre' => '',
    'apellido' => '',
    'email' => '',
    'telefono' => ''
];
$errores = [];

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $errores['general'] = "Token de seguridad inválido";
    } else {
        // Definir reglas de validación
        $reglas = [
            'nombre' => ['required', 'min:2'],
            'apellido' => ['required', 'min:2'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'password_confirm' => ['required']
        ];
        
        // Validar formulario
        list($datosSanitizados, $erroresValidacion) = validarFormulario($_POST, $reglas);
        $errores = array_merge($errores, $erroresValidacion);
        
        // Validar contraseñas coincidan
        if (empty($errores['password']) && 
            $datosSanitizados['password'] !== $datosSanitizados['password_confirm']) {
            $errores['password_confirm'] = "Las contraseñas no coinciden";
        }
        
        // Validar fortaleza de contraseña
        if (empty($errores['password'])) {
            list($valido, $mensajeError) = validarPassword($datosSanitizados['password']);
            if (!$valido) {
                $errores['password'] = $mensajeError;
            }
        }
        
        // Si no hay errores, proceder con el registro
        if (empty($errores)) {
            $conexion = getConexion();
            
            // Verificar que el email no esté registrado
            $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $datosSanitizados['email']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            if ($resultado->num_rows > 0) {
                $errores['email'] = "Este email ya está registrado";
            } else {
                // Insertar nuevo usuario
                $hashPassword = password_hash($datosSanitizados['password'], PASSWORD_DEFAULT);
                $telefono = $datosSanitizados['telefono'] ?? null;
                
                $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, email, password, telefono) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", 
                    $datosSanitizados['nombre'],
                    $datosSanitizados['apellido'],
                    $datosSanitizados['email'],
                    $hashPassword,
                    $telefono
                );
                
                if ($stmt->execute()) {
                    // Log de seguridad
                    logSeguridad("Nuevo usuario registrado", "Email: " . $datosSanitizados['email']);
                    
                    // Redirigir al login con mensaje de éxito
                    redireccionar('login.php?registro=exitoso');
                } else {
                    $errores['general'] = "Error al crear la cuenta. Por favor, intente nuevamente.";
                }
            }
            
            $stmt->close();
            $conexion->close();
        }
        
        // Mantener datos en el formulario (excepto contraseñas)
        $datos = $datosSanitizados;
        unset($datos['password']);
        unset($datos['password_confirm']);
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
    <title>Registrarse - Mi Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .password-requirements {
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <h2 class="text-center mb-4">Crear Cuenta</h2>
            
            <?php if (isset($errores['general'])): ?>
                <div class="alert alert-danger"><?php echo $errores['general']; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>" 
                             id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos['nombre']); ?>" required>
                        <?php if (isset($errores['nombre'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['nombre']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" class="form-control <?php echo isset($errores['apellido']) ? 'is-invalid' : ''; ?>" 
                               id="apellido" name="apellido" value="<?php echo htmlspecialchars($datos['apellido']); ?>" required>
                        <?php if (isset($errores['apellido'])): ?>
                            <div class="invalid-feedback"><?php echo $errores['apellido']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control <?php echo isset($errores['email']) ? 'is-invalid' : ''; ?>" 
                           id="email" name="email" value="<?php echo htmlspecialchars($datos['email']); ?>" required>
                    <?php if (isset($errores['email'])): ?>
                        <div class="invalid-feedback"><?php echo $errores['email']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono (opcional):</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                           value="<?php echo htmlspecialchars($datos['telefono']); ?>">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control <?php echo isset($errores['password']) ? 'is-invalid' : ''; ?>" 
                           id="password" name="password" required>
                    <?php if (isset($errores['password'])): ?>
                        <div class="invalid-feedback"><?php echo $errores['password']; ?></div>
                    <?php endif; ?>
                    <div class="password-requirements">
                        La contraseña debe tener al menos 6 caracteres, incluyendo una mayúscula y un número.
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirmar Contraseña:</label>
                    <input type="password" class="form-control <?php echo isset($errores['password_confirm']) ? 'is-invalid' : ''; ?>" 
                           id="password_confirm" name="password_confirm" required>
                    <?php if (isset($errores['password_confirm'])): ?>
                        <div class="invalid-feedback"><?php echo $errores['password_confirm']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                <p><a href="index.php">← Volver al inicio</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>