<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — LISTAR USUARIOS
═══════════════════════════════════════════════════════════════════════════

  Trae todas las cuentas de la tabla "usuarios" (id, nombre, email,
  documento, teléfono, dirección, rol — la contraseña NUNCA se manda al
  navegador, ni siquiera cifrada, no hace falta para mostrar la tabla).
  app.js lo llama cada vez que se entra a la sección "Usuarios".

  Requiere sesión iniciada (ver "sesion.php").
═══════════════════════════════════════════════════════════════════════════*/

header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

$resultado = $mysqli->query("SELECT id, nombre, email, rol, telefono, direccion FROM usuarios ORDER BY nombre ASC");

$usuarios = [];
while ($fila = $resultado->fetch_assoc()) {
    $usuarios[] = $fila;
}

echo json_encode(["exito" => true, "usuarios" => $usuarios]);

$mysqli->close();