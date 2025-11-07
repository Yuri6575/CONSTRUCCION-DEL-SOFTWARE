<?php
// mi_proyecto/public/index.php
echo "<h1>¡Mi Proyecto PHP está funcionando!</h1>";
echo "<p>Si puedes ver este mensaje, tu entorno está configurado correctamente.</p>";

// Probar conexión a MySQL
try {
    $conexion = new mysqli("localhost", "root", "");
    if ($conexion->connect_error) {
        echo "<p style='color: red;'>Error de conexión MySQL: " . $conexion->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✓ Conexión MySQL exitosa</p>";
        $conexion->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Mostrar información de PHP
echo "<h2>Información del Servidor:</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li>Directorio raíz: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "</ul>";
?>