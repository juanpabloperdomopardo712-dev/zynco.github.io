<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — CREAR VEHÍCULO (CORREGIDO)
═══════════════════════════════════════════════════════════════════════════

  Recibe por POST: placa, marca, modelo, anio, color, tipo, precio_dia, estado, imagen.
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

$placa = trim($_POST["placa"] ?? "");
$marca = trim($_POST["marca"] ?? "");
$modelo = trim($_POST["modelo"] ?? "");
$anio = !empty($_POST["anio"]) ? (int) $_POST["anio"] : 0;
$color = trim($_POST["color"] ?? "");
$tipo = trim($_POST["tipo"] ?? "");
$precio_dia = !empty($_POST["precio"]) ? (float) $_POST["precio"] : 0;
$estado = "Disponible";
$imagen = "img/autos/default.jpg";

if (isset($_FILES["imagen_file"]) && $_FILES["imagen_file"]["error"] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES["imagen_file"]["error"] !== UPLOAD_ERR_OK) {
        echo json_encode(["exito" => false, "mensaje" => "No se pudo subir la imagen del vehículo."]);
        exit;
    }
    if ($_FILES["imagen_file"]["size"] > 5 * 1024 * 1024) {
        echo json_encode(["exito" => false, "mensaje" => "La imagen no puede superar 5 MB."]);
        exit;
    }
    $permitidos = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp", "image/gif" => "gif"];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES["imagen_file"]["tmp_name"]);
    if (!isset($permitidos[$mime])) {
        echo json_encode(["exito" => false, "mensaje" => "Formato de imagen no permitido. Usa JPG, PNG, WEBP o GIF."]);
        exit;
    }
    $dir = dirname(__DIR__) . "/img/autos/";
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $nombreArchivo = "vehiculo_" . bin2hex(random_bytes(8)) . "." . $permitidos[$mime];
    if (!move_uploaded_file($_FILES["imagen_file"]["tmp_name"], $dir . $nombreArchivo)) {
        echo json_encode(["exito" => false, "mensaje" => "No se pudo guardar la imagen del vehículo."]);
        exit;
    }
    $imagen = "img/autos/" . $nombreArchivo;
}

/*----------------------------------------------------------------------
    ¿YA EXISTE UNA PLACA IGUAL? (Solo se valida si se ingresó placa)
----------------------------------------------------------------------*/
if ($placa !== "") {
    $verificar = $mysqli->prepare("SELECT id FROM vehiculos WHERE placa = ?");
    $verificar->bind_param("s", $placa);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        echo json_encode(["exito" => false, "mensaje" => "Ya existe un vehículo registrado con esa placa."]);
        $verificar->close();
        exit;
    }
    $verificar->close();
}

/*----------------------------------------------------------------------
    INSERTAR EL NUEVO VEHÍCULO
----------------------------------------------------------------------*/
$insertar = $mysqli->prepare(
    "INSERT INTO vehiculos (placa, marca, modelo, anio, color, tipo, precio_dia, estado, imagen)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$insertar->bind_param("sssisdsss", $placa, $marca, $modelo, $anio, $color, $tipo, $precio_dia, $estado, $imagen);

if ($insertar->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Vehículo registrado correctamente."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo guardar el vehículo. Intenta de nuevo."]);
}

$insertar->close();
$mysqli->close();
?>