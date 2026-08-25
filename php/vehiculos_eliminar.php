<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — ELIMINAR VEHÍCULO
═══════════════════════════════════════════════════════════════════════════

  Recibe por POST: id.

  PROTECCIÓN: no deja eliminar un vehículo que ya tiene reservas
  asociadas (la relación reservas -> vehiculos tiene ON DELETE CASCADE,
  así que borrar el vehículo borraría también sus reservas y pagos).

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

$id = (int) ($_POST["id"] ?? 0);

if ($id <= 0) {
    echo json_encode(["exito" => false, "mensaje" => "Vehículo no válido."]);
    exit;
}

$verificar = $mysqli->prepare("SELECT id FROM reservas WHERE vehiculo_id = ? LIMIT 1");
$verificar->bind_param("i", $id);
$verificar->execute();
$verificar->store_result();

if ($verificar->num_rows > 0) {
    echo json_encode(["exito" => false, "mensaje" => "No se puede eliminar: el vehículo tiene reservas asociadas."]);
    $verificar->close();
    exit;
}
$verificar->close();

$eliminar = $mysqli->prepare("DELETE FROM vehiculos WHERE id = ?");
$eliminar->bind_param("i", $id);

if ($eliminar->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Vehículo eliminado."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo eliminar el vehículo."]);
}

$eliminar->close();
$mysqli->close();
