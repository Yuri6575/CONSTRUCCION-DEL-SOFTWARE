<!-- views/registro.php -->
<form action="procesar_registro.php" method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="apellido" placeholder="Apellido" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <input type="password" name="password2" placeholder="Confirmar Contraseña" required>
    <button type="submit">Registrarse</button>
</form>