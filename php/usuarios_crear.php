<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — CREAR USUARIO (desde el panel)
═══════════════════════════════════════════════════════════════════════════

  Antes esto era "registro.php" y cualquier visitante lo podía usar
  desde la pantalla de login. Ya no: Zynco es una herramienta interna,
  así que ahora este archivo:
    • Requiere sesión iniciada (ver "sesion.php") — solo alguien que ya
      entró a Zynco puede crear cuentas nuevas, desde la sección
      "Usuarios" del panel.
    • Recibe el "rol" desde el formulario (antes siempre quedaba fijo
      en "Cliente"; ahora lo elige quien está creando la cuenta).

  Recibe por POST: nombre, correo, password, rol.
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
$password = $_POST["password"] ?? "";
$rol = trim($_POST["rol"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");


/*----------------------------------------------------------------------
    VALIDACIONES BÁSICAS
----------------------------------------------------------------------*/

if ($id === "" || $nombre === "" || $correo === "" || $password === "" || $rol === "") {
    echo json_encode(["exito" => false, "mensaje" => "Completa ID, nombre, correo, contraseña y rol."]);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["exito" => false, "mensaje" => "El correo no es válido."]);
    exit;
}

if (strlen($password) < 4) {
    echo json_encode(["exito" => false, "mensaje" => "La contraseña debe tener al menos 4 caracteres."]);
    exit;
}

/*----------------------------------------------------------------------
    ¿YA EXISTE ESE CORREO? (la columna "email" es UNIQUE en la BD, pero
    es mejor revisarlo antes y devolver un mensaje claro)
----------------------------------------------------------------------*/

$verificarId = $mysqli->prepare("SELECT id FROM usuarios WHERE id = ?");
$verificarId->bind_param("s", $id);
$verificarId->execute();
$verificarId->store_result();
if ($verificarId->num_rows > 0) {
    echo json_encode(["exito" => false, "mensaje" => "Ese ID / documento ya está registrado."]);
    $verificarId->close();
    exit;
}
$verificarId->close();

$verificar = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
$verificar->bind_param("s", $correo);
$verificar->execute();
$verificar->store_result();

if ($verificar->num_rows > 0) {
    echo json_encode(["exito" => false, "mensaje" => "Ese correo ya está registrado."]);
    $verificar->close();
    exit;
}
$verificar->close();

/*----------------------------------------------------------------------
    GUARDAR EL USUARIO NUEVO (consulta preparada: evita inyección SQL)
----------------------------------------------------------------------*/

$passwordCifrada = password_hash($password, PASSWORD_DEFAULT);

$insertar = $mysqli->prepare(
    "INSERT INTO usuarios (id, nombre, email, password, rol, telefono, direccion)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$insertar->bind_param("sssssss", $id, $nombre, $correo, $passwordCifrada, $rol, $telefono, $direccion);

if ($insertar->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Usuario creado correctamente."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo guardar el usuario. Intenta de nuevo."]);
}

$insertar->close();
$mysqli->close();