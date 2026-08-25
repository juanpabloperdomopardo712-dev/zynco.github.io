<?php
header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

$pagoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$pagoId) {
    http_response_code(422);
    echo json_encode(['exito' => false, 'mensaje' => 'Pago inválido.']);
    exit;
}
$consulta = $mysqli->prepare("SELECT p.id, p.monto, p.metodo_pago, p.estado AS estado_pago, p.fecha_pago, r.id AS reserva_id,
    c.nombre AS cliente_nombre, c.id AS cliente_documento,
    v.marca, v.modelo, v.placa,
    u.nombre AS usuario_nombre, u.rol AS usuario_rol, u.email AS usuario_email
    FROM pagos p
    INNER JOIN reservas r ON r.id = p.reserva_id
    INNER JOIN clientes c ON c.id = r.cliente_id
    INNER JOIN vehiculos v ON v.id = r.vehiculo_id
    LEFT JOIN usuarios u ON u.id = CAST(p.usuario_id AS CHAR)
    WHERE p.id = ? LIMIT 1");
$consulta->bind_param('i', $pagoId);
$consulta->execute();
$pago = $consulta->get_result()->fetch_assoc();
if (!$pago) {
    http_response_code(404);
    echo json_encode(['exito' => false, 'mensaje' => 'Comprobante no encontrado.']);
    exit;
}
$pago['id'] = (int) $pago['id'];
$pago['reserva_id'] = (int) $pago['reserva_id'];
$pago['monto'] = (float) $pago['monto'];
echo json_encode(['exito' => true, 'pago' => $pago]);
$consulta->close();
$mysqli->close();
