<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — CONEXIÓN A LA BASE DE DATOS
═══════════════════════════════════════════════════════════════════════════

  Este archivo abre UNA conexión a MySQL (usando mysqli) y la deja lista
  en la variable $mysqli para que la usen "login.php", "registro.php",
  etc. con "require 'conectar.php';".

  Usa exactamente el nombre de base de datos y tablas que creaste en
  "zinco_db.sql" (base de datos "zynco_db", tabla "usuarios").

  Si tu XAMPP/WAMP/Laragon tiene usuario o contraseña distintos a los de
  abajo (root / sin contraseña, que es lo normal por defecto), cámbialos
  aquí.
═══════════════════════════════════════════════════════════════════════════*/

$host = "localhost";
$usuario_bd = "root";
$password_bd = "";
$nombre_bd = "zynco_db";

// mysqli con manejo de errores por excepciones: si algo falla más abajo
// (por ejemplo un INSERT con datos raros), en vez de que PHP se quede
// callado, lanza un error que sí podemos capturar y responder al front.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($host, $usuario_bd, $password_bd, $nombre_bd);
    $mysqli->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Si ni siquiera se pudo conectar (XAMPP apagado, base de datos no
    // creada, credenciales mal, etc.), se responde en JSON para que el
    // front (app.js) pueda mostrar un mensaje claro en vez de quedarse
    // "colgado" esperando una respuesta que nunca llega bien formada.
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudo conectar a la base de datos. ¿Está MySQL encendido y existe 'zynco_db'?",
    ]);
    exit;
}
