<?php
// Desactivar la visualización de errores en pantalla para que no rompan el JSON
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json; charset=utf-8");
session_start();

// 1. Validar si la sesión existe
if (!isset($_SESSION["usuario"])) {
    echo json_encode([
        "exito" => false, 
        "mensaje" => "No hay sesión activa."
    ]);
    exit;
}

// 2. Manejar por si $_SESSION["usuario"] es un array o viene estructurado diferente
$u = $_SESSION["usuario"];

// Si está guardado como objeto o array asociativo
$id = is_array($u) ? ($u["id"] ?? "") : ($u->id ?? "");
$nombre = is_array($u) ? ($u["nombre"] ?? "Usuario") : ($u->name ?? "Usuario");
$email = is_array($u) ? ($u["email"] ?? "No especificado") : ($u->email ?? "No especificado");
$rol = is_array($u) ? ($u["rol"] ?? "Administrador") : ($u->rol ?? "Administrador");

echo json_encode([
    "exito" => true,
    "usuario" => [
        "id" => $id,
        "nombre" => $nombre,
        "email" => $email,
        "rol" => $rol
    ]
]);
exit;
?>