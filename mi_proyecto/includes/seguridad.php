<?php
//  Funciones de seguridad para la aplicación

// Iniciar sesión segura
function iniciarSesionSegura() {
    // Configurar parámetros de sesión seguros
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
    
    // Regenerar ID de sesión periódicamente
    if (!isset($_SESSION['ultima_regeneracion'])) {
        $_SESSION['ultima_regeneracion'] = time();
    } elseif (time() - $_SESSION['ultima_regeneracion'] > 1800) { // 30 minutos
        session_regenerate_id(true);
        $_SESSION['ultima_regeneracion'] = time();
    }
}

/**
 * Verificar si el usuario está autenticado
 * @param bool $redireccionar Si es true, redirige al login
 * @return bool
 */
function verificarAutenticacion($redireccionar = true) {
    if (!isset($_SESSION['usuario_id'])) {
        if ($redireccionar) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            redireccionar('login.php');
        }
        return false;
    }
    return true;
}

/**
 * Verificar tiempo de inactividad
 * @param int $tiempoLimite Segundos de inactividad permitidos
 */
function verificarInactividad($tiempoLimite = 1800) { // 30 minutos por defecto
    if (isset($_SESSION['ultimo_acceso'])) {
        $tiempoInactivo = time() - $_SESSION['ultimo_acceso'];
        if ($tiempoInactivo > $tiempoLimite) {
            cerrarSesion();
            redireccionar('login.php?expirado=1');
        }
    }
    $_SESSION['ultimo_acceso'] = time();
}

/**
 * Cerrar sesión de forma segura
 */
function cerrarSesion() {
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Destruir cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruir sesión
    session_destroy();
}

/**
 * Generar token CSRF
 * @return string
 */
function generarTokenCSRF() {
    if (empty($_SESSION['token_csrf'])) {
        $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token_csrf'];
}

/**
 * Validar token CSRF
 * @param string $token
 * @return bool
 */
function validarTokenCSRF($token) {
    if (empty($_SESSION['token_csrf']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['token_csrf'], $token);
}

/**
 * Prevenir inyección XSS
 * @param mixed $data
 * @return mixed
 */
function prevenirXSS($data) {
    if (is_array($data)) {
        return array_map('prevenirXSS', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Validar y sanitizar entrada de formulario
 * @param array $datos
 * @param array $reglas
 * @return array [array $datosSanitizados, array $errores]
 */
function validarFormulario($datos, $reglas) {
    $sanitizados = [];
    $errores = [];
    
    foreach ($reglas as $campo => $regla) {
        $valor = $datos[$campo] ?? '';
        
        // Sanitizar
        $valorSanitizado = prevenirXSS(trim($valor));
        $sanitizados[$campo] = $valorSanitizado;
        
        // Validar según reglas
        if (in_array('required', $regla) && empty($valorSanitizado)) {
            $errores[$campo] = "El campo $campo es obligatorio";
            continue;
        }
        
        if (!empty($valorSanitizado)) {
            if (in_array('email', $regla) && !validarEmail($valorSanitizado)) {
                $errores[$campo] = "El formato del email no es válido";
            }
            
            if (in_array('numero', $regla) && !is_numeric($valorSanitizado)) {
                $errores[$campo] = "El campo $campo debe ser un número";
            }
            
            // Validar longitud mínima
            foreach ($regla as $r) {
                if (strpos($r, 'min:') === 0) {
                    $min = (int) substr($r, 4);
                    if (strlen($valorSanitizado) < $min) {
                        $errores[$campo] = "El campo $campo debe tener al menos $min caracteres";
                    }
                }
            }
        }
    }
    
    return [$sanitizados, $errores];
}

/**
 * Log de actividades de seguridad
 * @param string $accion
 * @param string $detalles
 */
function logSeguridad($accion, $detalles = '') {
    $archivoLog = __DIR__ . '/../logs/seguridad.log';
    $fecha = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP desconocida';
    $usuario = $_SESSION['usuario_id'] ?? 'Anónimo';
    
    $mensaje = "[$fecha] [$ip] [Usuario: $usuario] $accion";
    if (!empty($detalles)) {
        $mensaje .= " - $detalles";
    }
    $mensaje .= PHP_EOL;
    
    // Crear directorio de logs si no existe
    $directorioLogs = dirname($archivoLog);
    if (!is_dir($directorioLogs)) {
        mkdir($directorioLogs, 0755, true);
    }
    
    file_put_contents($archivoLog, $mensaje, FILE_APPEND | LOCK_EX);
}
?>