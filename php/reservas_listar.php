<?php
/**
 * ZYNCO - Listado de Reservas
 * Endpoint consumido por:
 *     fetch("php/reservas_listar.php")
 *
 * Devuelve JSON con las reservas reales de la base de datos,
 * incluyendo cliente, vehículo y si la reserva ya tiene un pago.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    require_once __DIR__ . '/conexion.php';

    /*
     * Compatibilidad con las variables de conexión habituales.
     * El proyecto puede exponer la conexión como $conexion, $conn o $mysqli.
     */
    if (isset($conexion) && $conexion instanceof mysqli) {
        $db = $conexion;
    } elseif (isset($conn) && $conn instanceof mysqli) {
        $db = $conn;
    } elseif (isset($mysqli) && $mysqli instanceof mysqli) {
        $db = $mysqli;
    } else {
        throw new RuntimeException('No se encontró una conexión mysqli válida en conexion.php.');
    }

    if ($db->connect_errno) {
        throw new RuntimeException('Error de conexión con la base de datos: ' . $db->connect_error);
    }

    $db->set_charset('utf8mb4');

    /*
     * La interfaz actual espera:
     * id, cliente_nombre, cliente_documento, marca, modelo,
     * fecha_inicio, fecha_fin, costo_total, estado y tiene_pago.
     *
     * Se usa LEFT JOIN para no perder reservas que todavía no tienen pago.
     * COALESCE permite mostrar 0 cuando no existe pago asociado.
     */
    $sql = "
        SELECT
            r.id,
            r.fecha_creacion,
            r.fecha_inicio,
            r.fecha_fin,
            r.costo_total,
            r.estado,

            c.id AS cliente_id,
            COALESCE(c.nombre, 'Cliente no encontrado') AS cliente_nombre,
            c.email AS cliente_email,
            c.telefono AS cliente_telefono,
            c.direccion AS cliente_direccion,

            v.id AS vehiculo_pk,
            r.vehiculo_id,
            v.placa AS vehiculo_placa,
            COALESCE(v.marca, '') AS marca,
            COALESCE(v.modelo, '') AS modelo,
            v.anio AS vehiculo_anio,
            v.color AS vehiculo_color,
            v.tipo AS vehiculo_tipo,
            v.precio_dia AS vehiculo_precio_dia,
            v.imagen AS vehiculo_imagen,

            u.id AS usuario_id,
            u.nombre AS usuario_nombre,
            u.email AS usuario_email,
            u.rol AS usuario_rol,

            CASE
                WHEN COUNT(p.id) > 0 THEN 1
                ELSE 0
            END AS tiene_pago
        FROM reservas AS r
        LEFT JOIN clientes AS c
            ON c.id = r.cliente_id
        LEFT JOIN vehiculos AS v
            ON v.id = r.vehiculo_id
        LEFT JOIN usuarios AS u
            ON u.id = r.usuario_id
        LEFT JOIN pagos AS p
            ON p.reserva_id = r.id
        GROUP BY
            r.id,
            r.fecha_creacion,
            r.fecha_inicio,
            r.fecha_fin,
            r.costo_total,
            r.estado,
            c.id,
            c.nombre,
            c.email,
            c.telefono,
            c.direccion,
            v.id,
            r.vehiculo_id,
            v.placa,
            v.marca,
            v.modelo,
            v.anio,
            v.color,
            v.tipo,
            v.precio_dia,
            v.imagen,
            u.id,
            u.nombre,
            u.email,
            u.rol
        ORDER BY r.id DESC
    ";

    $resultado = $db->query($sql);

    if (!$resultado) {
        throw new RuntimeException('Error al consultar las reservas: ' . $db->error);
    }

    $reservas = [];

    while ($fila = $resultado->fetch_assoc()) {
        $reservas[] = [
            'id' => (int) $fila['id'],
            'cliente_id' => (string) $fila['cliente_id'],
            'cliente_documento' => (string) $fila['cliente_id'],
            'cliente_nombre' => (string) $fila['cliente_nombre'],
            'vehiculo_id' => (string) $fila['vehiculo_id'],
            'marca' => (string) $fila['marca'],
            'modelo' => (string) $fila['modelo'],
            'fecha_inicio' => $fila['fecha_inicio'],
            'fecha_fin' => $fila['fecha_fin'],
            'costo_total' => (float) $fila['costo_total'],
            'estado' => (string) $fila['estado'],
            'tiene_pago' => (bool) $fila['tiene_pago'],

            'fecha_creacion' => $fila['fecha_creacion'],

            'cliente' => [
                'id' => (string) $fila['cliente_id'],
                'nombre' => (string) $fila['cliente_nombre'],
                'email' => $fila['cliente_email'],
                'telefono' => $fila['cliente_telefono'],
                'direccion' => $fila['cliente_direccion'],
            ],

            'vehiculo' => [
                'placa' => $fila['vehiculo_placa'],
                'marca' => (string) $fila['marca'],
                'modelo' => (string) $fila['modelo'],
                'anio' => $fila['vehiculo_anio'] !== null ? (int) $fila['vehiculo_anio'] : null,
                'color' => $fila['vehiculo_color'],
                'tipo' => $fila['vehiculo_tipo'],
                'precio_dia' => $fila['vehiculo_precio_dia'] !== null ? (float) $fila['vehiculo_precio_dia'] : null,
                'imagen' => $fila['vehiculo_imagen'],
            ],

            'usuario' => [
                'id' => $fila['usuario_id'],
                'nombre' => $fila['usuario_nombre'],
                'email' => $fila['usuario_email'],
                'rol' => $fila['usuario_rol'],
            ],
        ];
    }

    $resultado->free();

    echo json_encode([
        'exito' => true,
        'reservas' => $reservas
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage(),
        'reservas' => []
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
?>
