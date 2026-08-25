<?php
/**
 * ZYNCO — Activar / Desactivar cliente
 *
 * La tabla "clientes" sí tiene una columna "estado" (varchar, por defecto
 * 'Activo'), así que este endpoint alterna entre 'Activo' e 'Inactivo'
 * en lugar de simplemente rechazar la solicitud.
 *
 * Recibe por POST: id
 * Responde en JSON: { "exito": true/false, "mensaje": "...", "estado": "..." }
 */

header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido.']);
    exit;
}

$id = trim($_POST['id'] ?? '');

if ($id === '') {
    http_response_code(422);
    echo json_encode(['exito' => false, 'mensaje' => 'Falta el documento del cliente.']);
    exit;
}

try {
    $consultar = $mysqli->prepare("SELECT estado FROM clientes WHERE id = ?");
    $consultar->bind_param('s', $id);
    $consultar->execute();
    $resultado = $consultar->get_result();

    if ($resultado->num_rows === 0) {
        throw new RuntimeException('Cliente no encontrado.');
    }

    $estadoActual = $resultado->fetch_assoc()['estado'];
    $consultar->close();

    $nuevoEstado = strtolower(trim($estadoActual)) === 'activo' ? 'Inactivo' : 'Activo';

    $actualizar = $mysqli->prepare("UPDATE clientes SET estado = ? WHERE id = ?");
    $actualizar->bind_param('ss', $nuevoEstado, $id);
    $actualizar->execute();
    $actualizar->close();

    echo json_encode([
        'exito' => true,
        'mensaje' => $nuevoEstado === 'Activo' ? 'Cliente activado correctamente.' : 'Cliente desactivado correctamente.',
        'estado' => $nuevoEstado,
    ]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}

$mysqli->close();
