<?php
//  Funciones utilitarias para la aplicación


/**
 * Sanitizar datos de entrada
 * @param string $dato
 * @return string
 */
function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

/**
 * Redireccionar a una URL
 * @param string $url
 */
function redireccionar($url) {
    header("Location: $url");
    exit();
}

/**
 * Mostrar mensajes de error/éxito
 * @param string $mensaje
 * @param string $tipo (success, error, warning, info)
 */
function mostrarMensaje($mensaje, $tipo = 'info') {
    $clases = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $clase = $clases[$tipo] ?? $clases['info'];
    
    return "<div class='alert $clase'>$mensaje</div>";
}

/**
 * Verificar si el usuario está logueado
 * @return bool
 */
function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

/**
 * Obtener nombre completo del usuario
 * @return string
 */
function obtenerNombreUsuario() {
    if (isset($_SESSION['usuario_nombre']) && isset($_SESSION['usuario_apellido'])) {
        return $_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido'];
    }
    return 'Usuario';
}

/**
 * Validar formato de email
 * @param string $email
 * @return bool
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar fortaleza de contraseña
 * @param string $password
 * @return array [bool $valido, string $error]
 */
function validarPassword($password) {
    if (strlen($password) < 6) {
        return [false, "La contraseña debe tener al menos 6 caracteres"];
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return [false, "La contraseña debe contener al menos una letra mayúscula"];
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return [false, "La contraseña debe contener al menos un número"];
    }
    
    return [true, ""];
}

/**
 * Formatear fecha para mostrar
 * @param string $fecha
 * @return string
 */
function formatearFecha($fecha) {
    return date('d/m/Y H:i', strtotime($fecha));
}

/**
 * Generar token aleatorio
 * @param int $longitud
 * @return string
 */
function generarToken($longitud = 32) {
    return bin2hex(random_bytes($longitud));
}

/**
 * Debug function (solo en desarrollo)
 * @param mixed $data
 */
function debug($data) {
    if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost') {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}
?>