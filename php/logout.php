<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — CERRAR SESIÓN
═══════════════════════════════════════════════════════════════════════════

  Destruye la sesión de PHP que "login.php" había guardado. app.js llama
  a este archivo (fetch) cada vez que se pulsa cualquiera de los tres
  botones de "Cerrar sesión" del sistema.
═══════════════════════════════════════════════════════════════════════════*/

header("Content-Type: application/json; charset=utf-8");
session_start();

$_SESSION = [];
session_destroy();

echo json_encode(["exito" => true]);
