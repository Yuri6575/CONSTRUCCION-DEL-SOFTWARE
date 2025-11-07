<?php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mi_proyecto');
define('DB_CHARSET', 'utf8mb4');


// // Obtener conexión a la base de datos usando MySQLi
//  * @return mysqli

function getConexion() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset(DB_CHARSET);
    return $conn;
}
?>