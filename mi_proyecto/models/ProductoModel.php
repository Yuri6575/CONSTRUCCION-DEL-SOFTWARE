<?php
class ProductoModel {
    public static function crear($nombre, $descripcion, $precio, $stock, $categoria_id) {
        $conexion = getConexion();
        $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $categoria_id);
        return $stmt->execute();
    }
    
    public static function listar() {
        $conexion = getConexion();
        $resultado = $conexion->query("SELECT * FROM productos ORDER BY nombre");
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    public static function actualizar($id, $nombre, $descripcion, $precio, $stock, $categoria_id) {
        $conexion = getConexion();
        $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria_id=? WHERE id=?");
        $stmt->bind_param("ssdiii", $nombre, $descripcion, $precio, $stock, $categoria_id, $id);
        return $stmt->execute();
    }
    
    public static function eliminar($id) {
        $conexion = getConexion();
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>