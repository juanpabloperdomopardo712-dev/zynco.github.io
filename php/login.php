<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — INICIO DE SESIÓN CONTRA LA BASE DE DATOS
═══════════════════════════════════════════════════════════════════════════*/

header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["exito" => false, "mensaje" => "Método no permitido."]);
    exit;
}

$usuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";

if ($usuario === "" || $password === "") {
    echo json_encode(["exito" => false, "mensaje" => "Escribe usuario y contraseña."]);
    exit;
}

/*----------------------------------------------------------------------
    BUSCAR AL USUARIO (por nombre O por correo, sin importar
    mayúsculas/minúsculas)
----------------------------------------------------------------------*/

$buscar = $mysqli->prepare(
    "SELECT id, nombre, email, password, rol
       FROM usuarios
      WHERE LOWER(nombre) = LOWER(?) OR LOWER(email) = LOWER(?)
      LIMIT 1"
);
$buscar->bind_param("ss", $usuario, $usuario);
$buscar->execute();
$resultado = $buscar->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(["exito" => false, "mensaje" => "Usuario no encontrado."]);
    $buscar->close();
    exit;
}

$fila = $resultado->fetch_assoc();
$buscar->close();

/*----------------------------------------------------------------------
    VERIFICAR LA CONTRASEÑA
----------------------------------------------------------------------*/

$claveValida = password_verify($password, $fila["password"]);

if (!$claveValida && hash_equals($fila["password"], $password)) {
    $claveValida = true;

    $nuevaClave = password_hash($password, PASSWORD_DEFAULT);
    $actualizar = $mysqli->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $actualizar->bind_param("si", $nuevaClave, $fila["id"]);
    $actualizar->execute();
    $actualizar->close();
}

if (!$claveValida) {
    echo json_encode(["exito" => false, "mensaje" => "Contraseña incorrecta."]);
    exit;
}

/*----------------------------------------------------------------------
    LOGIN CORRECTO: guardar la sesión con todos los datos necesarios
----------------------------------------------------------------------*/

$_SESSION["usuario"] = [
    "id" => $fila["id"],
    "nombre" => $fila["nombre"],
    "email" => $fila["email"],
    "rol" => $fila["rol"],
];

echo json_encode([
    "exito" => true,
    "nombre" => $fila["nombre"],
    "rol" => $fila["rol"],
]);

$mysqli->close();
?>