<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — ELIMINAR USUARIO
═══════════════════════════════════════════════════════════════════════════

  Recibe por POST: id.

  PROTECCIÓN: no deja que una cuenta se elimine A SÍ MISMA mientras
  tiene la sesión abierta — si no, alguien podría quedarse sin ninguna
  cuenta con la que volver a entrar a Zynco.

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
    echo json_encode(["exito" => false, "mensaje" => "Usuario no válido."]);
    exit;
}

if ($id === (int) $_SESSION["usuario"]["id"]) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "No puedes eliminar tu propia cuenta mientras la tienes iniciada.",
    ]);
    exit;
}

$eliminar = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
$eliminar->bind_param("i", $id);

if ($eliminar->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Usuario eliminado."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo eliminar el usuario."]);
}

$eliminar->close();
$mysqli->close();
