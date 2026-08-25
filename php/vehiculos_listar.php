<?php
header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

$resultado = $mysqli->query("SELECT id, placa, marca, modelo, anio, color, tipo, precio_dia, estado, imagen FROM vehiculos ORDER BY marca ASC, modelo ASC");

$vehiculos = [];
while ($fila = $resultado->fetch_assoc()) {
    $fila["precio_dia"] = (float) $fila["precio_dia"];
    $vehiculos[] = $fila;
}

echo json_encode(["exito" => true, "vehiculos" => $vehiculos]);

$mysqli->close();
?>