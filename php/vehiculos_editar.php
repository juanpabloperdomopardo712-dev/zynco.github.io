<?php
/*═══════════════════════════════════════════════════════════════════════════
                    ZYNCO — EDITAR VEHÍCULO
═══════════════════════════════════════════════════════════════════════════

  Recibe por POST: id, placa, marca, modelo, anio, color, tipo, precio.
  No modifica "estado" ni "imagen" (no hay campos para eso en el modal
  de edición).

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
$placa = trim($_POST["placa"] ?? "");
$marca = trim($_POST["marca"] ?? "");
$modelo = trim($_POST["modelo"] ?? "");
$anio = !empty($_POST["anio"]) ? (int) $_POST["anio"] : 0;
$color = trim($_POST["color"] ?? "");
$tipo = trim($_POST["tipo"] ?? "");
$precio_dia = !empty($_POST["precio"]) ? (float) $_POST["precio"] : 0;
$imagenNueva = null;

if (isset($_FILES["imagen_file"]) && $_FILES["imagen_file"]["error"] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES["imagen_file"]["error"] !== UPLOAD_ERR_OK || $_FILES["imagen_file"]["size"] > 5 * 1024 * 1024) {
        echo json_encode(["exito" => false, "mensaje" => "La imagen no pudo subirse o supera 5 MB."]);
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
    $imagenNueva = "img/autos/" . $nombreArchivo;
}

if ($id <= 0 || $marca === "" || $modelo === "") {
    echo json_encode(["exito" => false, "mensaje" => "Faltan datos para editar el vehículo."]);
    exit;
}

/*----------------------------------------------------------------------
    ¿YA EXISTE OTRO VEHÍCULO CON ESA MISMA PLACA?
----------------------------------------------------------------------*/
if ($placa !== "") {
    $verificar = $mysqli->prepare("SELECT id FROM vehiculos WHERE placa = ? AND id != ?");
    $verificar->bind_param("si", $placa, $id);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        echo json_encode(["exito" => false, "mensaje" => "Ya existe otro vehículo registrado con esa placa."]);
        $verificar->close();
        exit;
    }
    $verificar->close();
}

/*----------------------------------------------------------------------
    ACTUALIZAR EL VEHÍCULO
----------------------------------------------------------------------*/
if ($imagenNueva !== null) {
    $actualizar = $mysqli->prepare(
        "UPDATE vehiculos SET placa = ?, marca = ?, modelo = ?, anio = ?, color = ?, tipo = ?, precio_dia = ?, imagen = ? WHERE id = ?"
    );
    $actualizar->bind_param("sssissdsi", $placa, $marca, $modelo, $anio, $color, $tipo, $precio_dia, $imagenNueva, $id);
} else {
    $actualizar = $mysqli->prepare(
        "UPDATE vehiculos SET placa = ?, marca = ?, modelo = ?, anio = ?, color = ?, tipo = ?, precio_dia = ? WHERE id = ?"
    );
    $actualizar->bind_param("sssissdi", $placa, $marca, $modelo, $anio, $color, $tipo, $precio_dia, $id);
}

if ($actualizar->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Vehículo actualizado correctamente."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo actualizar el vehículo."]);
}

$actualizar->close();
$mysqli->close();
