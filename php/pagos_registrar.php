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

$reservaId = filter_input(INPUT_POST, 'reserva_id', FILTER_VALIDATE_INT);
$pagoId = filter_input(INPUT_POST, 'pago_id', FILTER_VALIDATE_INT);
$monto = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
$metodo = trim($_POST['metodo_pago'] ?? '');
$metodosValidos = ['Tarjeta', 'Transferencia', 'Efectivo'];

if (!$reservaId || !$monto || $monto <= 0 || !in_array($metodo, $metodosValidos, true)) {
    http_response_code(422);
    echo json_encode(['exito' => false, 'mensaje' => 'Revisa el valor y el método de pago.']);
    exit;
}

try {
    $mysqli->begin_transaction();

    $reserva = $mysqli->prepare("SELECT id FROM reservas WHERE id = ? FOR UPDATE");
    $reserva->bind_param('i', $reservaId);
    $reserva->execute();
    if ($reserva->get_result()->num_rows === 0) {
        throw new RuntimeException('La reserva seleccionada ya no existe.');
    }
    $reserva->close();

    // La base actual usa identificadores de usuario VARCHAR; pagos guarda el documento numérico.
    $usuarioId = (string) $_SESSION['usuario']['id'];
    if ($pagoId) {
        // Al pulsar el botón de una fila existente, se confirma/actualiza ese pago.
        $pago = $mysqli->prepare("SELECT id FROM pagos WHERE id = ? AND reserva_id = ? FOR UPDATE");
        $pago->bind_param('ii', $pagoId, $reservaId);
        $pago->execute();
        if ($pago->get_result()->num_rows === 0) {
            throw new RuntimeException('El pago seleccionado no corresponde a la reserva.');
        }
        $pago->close();
        $actualizarPago = $mysqli->prepare("UPDATE pagos SET usuario_id = ?, monto = ?, metodo_pago = ?, estado = 'Pagado', fecha_pago = CURRENT_TIMESTAMP WHERE id = ?");
        $actualizarPago->bind_param('sdsi', $usuarioId, $monto, $metodo, $pagoId);
        $actualizarPago->execute();
        $actualizarPago->close();
    } else {
        $existente = $mysqli->prepare("SELECT id FROM pagos WHERE reserva_id = ? LIMIT 1");
        $existente->bind_param('i', $reservaId);
        $existente->execute();
        if ($existente->get_result()->num_rows > 0) {
            throw new RuntimeException('Esta reserva ya tiene un pago registrado. Usa el botón de acciones de ese pago.');
        }
        $existente->close();
        $insertar = $mysqli->prepare("INSERT INTO pagos (reserva_id, usuario_id, monto, metodo_pago, estado) VALUES (?, ?, ?, ?, 'Pagado')");
        $insertar->bind_param('isds', $reservaId, $usuarioId, $monto, $metodo);
        $insertar->execute();
        $pagoId = $mysqli->insert_id;
        $insertar->close();
    }

    $mysqli->commit();

    echo json_encode(['exito' => true, 'mensaje' => 'Pago registrado correctamente.', 'pagoId' => $pagoId]);
} catch (Throwable $e) {
    $mysqli->rollback();
    http_response_code(422);
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}
$mysqli->close();
