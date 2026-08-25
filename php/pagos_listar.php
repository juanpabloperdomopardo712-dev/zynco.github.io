<?php
header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";
$resultado = $mysqli->query("SELECT p.id, p.monto, p.metodo_pago, p.estado AS estado_pago, p.fecha_pago, r.id AS reserva_id,
    c.nombre AS cliente_nombre, c.id AS cliente_documento,
    u.nombre AS registrado_por
    FROM pagos p
    INNER JOIN reservas r ON r.id = p.reserva_id
    INNER JOIN clientes c ON c.id = r.cliente_id
    LEFT JOIN usuarios u ON u.id = CAST(p.usuario_id AS CHAR)
    ORDER BY p.fecha_pago DESC, p.id DESC");

$pagos = [];
while ($fila = $resultado->fetch_assoc()) {
    $fila['id'] = (int) $fila['id'];
    $fila['reserva_id'] = (int) $fila['reserva_id'];
    $fila['monto'] = (float) $fila['monto'];
    $pagos[] = $fila;
}
echo json_encode(['exito' => true, 'pagos' => $pagos]);
$mysqli->close();
