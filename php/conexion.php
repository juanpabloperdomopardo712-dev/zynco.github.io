<?php
// Datos de configuración de XAMPP por defecto
$servidor = "localhost";
$usuario = "root";
$contrasena = ""; // En XAMPP, la contraseña del root suele estar vacía
$base_datos = "zynco_db"; // El nombre de la BD que creamos

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Comprobar que funcione
if ($conexion->connect_error) {
    die("La conexión falló: " . $conexion->connect_error);
}
// Si todo sale bien, este archivo se conecta en silencio
?>