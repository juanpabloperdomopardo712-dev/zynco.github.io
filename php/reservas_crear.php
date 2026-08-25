<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — CREAR RESERVA
═══════════════════════════════════════════════════════════════════════════

  Recibe por POST: cliente_id, vehiculo_id, fecha_entrega, fecha_devolucion,
  valor, estado.

  El formulario original apuntaba a "backend/registrar_reserva.php", un
  archivo que no existe en el proyecto — por eso al crear una reserva
  llegaba una página en blanco (error 404 sin salida JSON). Este archivo
  reemplaza ese destino y sí guarda la reserva en la base de datos.

  Requiere sesión iniciada (ver "sesion.php").
  Responde en JSON: { "exito": true/false, "mensaje": "..." }
═══════════════════════════════════════════════════════════════════════════*/

header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["exito" => false, "mensaje" => "Método no permitido."]);
    exit;
}

$cliente_id = trim($_POST["cliente_id"] ?? "");
$vehiculo_id = (int) ($_POST["vehiculo_id"] ?? 0);
$fecha_inicio = trim($_POST["fecha_entrega"] ?? "");
$fecha_fin = trim($_POST["fecha_devolucion"] ?? "");
$costo_total = 0.0;
$estado = "Pendiente";
$usuario_id = $_SESSION["usuario"]["id"] ?? "";

if ($cliente_id === "" || $vehiculo_id <= 0 || $fecha_inicio === "" || $fecha_fin === "") {
    echo json_encode(["exito" => false, "mensaje" => "Completa todos los campos obligatorios de la reserva."]);
    exit;
}

if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
    echo json_encode(["exito" => false, "mensaje" => "La fecha de devolución debe ser posterior a la fecha de entrega."]);
    exit;
}

if ($usuario_id === "") {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo identificar al usuario de la sesión."]);
    exit;
}

/*----------------------------------------------------------------------
    ¿EXISTE EL CLIENTE?
----------------------------------------------------------------------*/
$verificarCliente = $mysqli->prepare("SELECT id FROM clientes WHERE id = ?");
$verificarCliente->bind_param("s", $cliente_id);
$verificarCliente->execute();
$verificarCliente->store_result();
if ($verificarCliente->num_rows === 0) {
    echo json_encode(["exito" => false, "mensaje" => "El cliente seleccionado no existe."]);
    $verificarCliente->close();
    exit;
}
$verificarCliente->close();

/*----------------------------------------------------------------------
    ¿EXISTE EL VEHÍCULO?
----------------------------------------------------------------------*/
$verificarVehiculo = $mysqli->prepare("SELECT id, precio_dia, estado FROM vehiculos WHERE id = ?");
$verificarVehiculo->bind_param("i", $vehiculo_id);
$verificarVehiculo->execute();
$resultadoVehiculo = $verificarVehiculo->get_result();
$vehiculo = $resultadoVehiculo->fetch_assoc();
$verificarVehiculo->close();

if (!$vehiculo) {
    echo json_encode(["exito" => false, "mensaje" => "El vehículo seleccionado no existe."]);
    exit;
}

if (strtolower((string)$vehiculo["estado"]) !== "disponible") {
    echo json_encode(["exito" => false, "mensaje" => "El vehículo seleccionado no está disponible."]);
    exit;
}

$inicio = new DateTime($fecha_inicio);
$fin = new DateTime($fecha_fin);
$dias = (int)$inicio->diff($fin)->days;

if ($dias <= 0) {
    echo json_encode(["exito" => false, "mensaje" => "El rango de fechas debe tener al menos un día de alquiler."]);
    exit;
}

$precio_dia = (float)$vehiculo["precio_dia"];
$costo_total = round($dias * $precio_dia, 2);

/*----------------------------------------------------------------------
    INSERTAR LA RESERVA
----------------------------------------------------------------------*/
$insertar = $mysqli->prepare(
    "INSERT INTO reservas (cliente_id, vehiculo_id, usuario_id, fecha_inicio, fecha_fin, costo_total, estado)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$insertar->bind_param("sisssds", $cliente_id, $vehiculo_id, $usuario_id, $fecha_inicio, $fecha_fin, $costo_total, $estado);

if ($insertar->execute()) {
    $reservaId = $insertar->insert_id;

    // El vehículo queda marcado como "En reserva" hasta que el flujo
    // de alquiler/pago lo cambie nuevamente.
    $actualizarVehiculo = $mysqli->prepare("UPDATE vehiculos SET estado = 'Reservado' WHERE id = ?");
    $actualizarVehiculo->bind_param("i", $vehiculo_id);
    $actualizarVehiculo->execute();
    $actualizarVehiculo->close();

    echo json_encode([
        "exito" => true,
        "mensaje" => "Reserva creada. Total calculado automáticamente: $" . number_format($costo_total, 0, ',', '.'),
        "id" => $reservaId,
        "dias" => $dias,
        "precio_dia" => $precio_dia,
        "costo_total" => $costo_total
    ]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo guardar la reserva. Intenta de nuevo."]);
}

$insertar->close();
$mysqli->close();
