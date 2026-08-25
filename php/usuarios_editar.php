<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — EDITAR USUARIO
═══════════════════════════════════════════════════════════════════════════

  Recibe por POST: id, nombre, correo, rol, y opcionalmente password
  (si se deja vacía, la contraseña actual NO se toca — así no hace
  falta escribirla de nuevo solo para corregir, por ejemplo, el correo).

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

$id = trim($_POST["id"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$rol = trim($_POST["rol"] ?? "");
$password = $_POST["password"] ?? "";
$telefono = trim($_POST["telefono"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");

if ($id === "" || $nombre === "" || $correo === "" || $rol === "") {
    echo json_encode(["exito" => false, "mensaje" => "Faltan datos para editar el usuario."]);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["exito" => false, "mensaje" => "El correo no es válido."]);
    exit;
}

/*----------------------------------------------------------------------
    ¿ESE CORREO YA LO USA OTRO USUARIO? (excluyendo la misma persona
    que se está editando, o el UNIQUE de "email" rechazaría hasta que
    alguien se guarde a sí mismo sin cambiar nada)
----------------------------------------------------------------------*/

$verificar = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
$verificar->bind_param("si", $correo, $id);
$verificar->execute();
$verificar->store_result();

if ($verificar->num_rows > 0) {
    echo json_encode(["exito" => false, "mensaje" => "Ese correo ya lo usa otro usuario."]);
    $verificar->close();
    exit;
}
$verificar->close();

/*----------------------------------------------------------------------
    ACTUALIZAR — con o sin cambio de contraseña
----------------------------------------------------------------------*/

if ($password !== "") {
    if (strlen($password) < 4) {
        echo json_encode(["exito" => false, "mensaje" => "La contraseña debe tener al menos 4 caracteres."]);
        exit;
    }

    $passwordCifrada = password_hash($password, PASSWORD_DEFAULT);

    $actualizar = $mysqli->prepare(
        "UPDATE usuarios
            SET nombre = ?, email = ?, rol = ?, password = ?,
                telefono = ?, direccion = ?
          WHERE id = ?"
    );
    $actualizar->bind_param(
        "ssssssi",
        $nombre,
        $correo,
        $rol,
        $passwordCifrada,
        $telefono,
        $direccion,
        $id
    );
} else {
    $actualizar = $mysqli->prepare(
        "UPDATE usuarios
            SET nombre = ?, email = ?, rol = ?,
                telefono = ?, direccion = ?
          WHERE id = ?"
    );
    $actualizar->bind_param("ssssss", $nombre, $correo, $rol, $telefono, $direccion, $id);
}

if ($actualizar->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Usuario actualizado correctamente."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo actualizar el usuario."]);
}

$actualizar->close();
$mysqli->close();