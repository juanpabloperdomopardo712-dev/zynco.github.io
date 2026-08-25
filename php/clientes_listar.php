<?php
header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

$resultado = $mysqli->query("SELECT id, nombre, email, telefono, direccion, fecha_registro, estado FROM clientes ORDER BY nombre ASC");
$clientes = [];
while ($fila = $resultado->fetch_assoc()) {
    $clientes[] = $fila;
}
echo json_encode(['exito' => true, 'clientes' => $clientes]);
$mysqli->close();