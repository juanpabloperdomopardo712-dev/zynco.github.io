<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — EXIGIR SESIÓN INICIADA
═══════════════════════════════════════════════════════════════════════════

  Los archivos de "Usuarios" (listar / crear / editar / eliminar) NO
  deben poder usarse por cualquiera que adivine la URL: solo por
  alguien que ya inició sesión en Zynco (como Vehículos, Reservas,
  etc., esta sección vive DENTRO del panel, después del login).

  Este archivo se agrega con "require 'sesion.php';" DESPUÉS de haber
  llamado session_start() y "conectar.php". Si no hay una sesión activa
  (guardada por login.php en $_SESSION['usuario']), responde en JSON
  con un error 401 y corta la ejecución con exit — así el archivo que
  lo incluyó nunca llega a tocar la base de datos.
═══════════════════════════════════════════════════════════════════════════*/

if (!isset($_SESSION["usuario"])) {
    http_response_code(401);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "exito" => false,
        "mensaje" => "Tu sesión expiró o no has iniciado sesión. Vuelve a entrar.",
    ]);
    exit;
}
