<?php
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
$idOriginal = trim($_POST['id_original'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');

if ($id === '' || $nombre === '') {
    http_response_code(422);
    echo json_encode(['exito' => false, 'mensaje' => 'El documento y el nombre son obligatorios.']);
    exit;
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['exito' => false, 'mensaje' => 'El correo electrónico no es válido.']);
    exit;
}

try {
    if ($idOriginal !== '') {
        // El documento identifica al cliente: no se permite cambiarlo durante una edición.
        if ($id !== $idOriginal) throw new RuntimeException('El documento no se puede modificar.');
        $consulta = $mysqli->prepare("UPDATE clientes SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?");
        $consulta->bind_param('sssss', $nombre, $email, $telefono, $direccion, $id);
        $consulta->execute();
        if ($consulta->affected_rows === 0) {
            $existe = $mysqli->prepare("SELECT id FROM clientes WHERE id = ?");
            $existe->bind_param('s', $id);
            $existe->execute();
            if ($existe->get_result()->num_rows === 0) throw new RuntimeException('Cliente no encontrado.');
            $existe->close();
        }
        $consulta->close();
        $mensaje = 'Cliente actualizado correctamente.';
    } else {
        $consulta = $mysqli->prepare("INSERT INTO clientes (id, nombre, email, telefono, direccion) VALUES (?, ?, ?, ?, ?)");
        $consulta->bind_param('sssss', $id, $nombre, $email, $telefono, $direccion);
        $consulta->execute();
        $consulta->close();
        $mensaje = 'Cliente registrado correctamente.';
    }
    echo json_encode(['exito' => true, 'mensaje' => $mensaje]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}
$mysqli->close();
