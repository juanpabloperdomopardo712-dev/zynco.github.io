<?php
/* Devuelve las cifras de la pestaña Reportes; el navegador nunca accede directamente a MySQL. */
header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

try {
    $resumen = $mysqli->query("
        SELECT
            (SELECT COUNT(*) FROM vehiculos) AS total_vehiculos,
            (SELECT COUNT(*) FROM reservas WHERE fecha_creacion >= DATE_FORMAT(CURDATE(), '%Y-%m-01')) AS reservas_mes,
            (SELECT COUNT(*) FROM clientes) AS total_clientes,
            (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE fecha_pago >= DATE_FORMAT(CURDATE(), '%Y-%m-01')) AS ingresos_mes,
            (SELECT COUNT(*) FROM vehiculos WHERE LOWER(estado) = 'disponible') AS vehiculos_disponibles,
            (SELECT COUNT(*) FROM vehiculos WHERE LOWER(estado) = 'entregado') AS vehiculos_alquilados,
            (SELECT COUNT(*) FROM vehiculos WHERE LOWER(estado) = 'mantenimiento') AS vehiculos_mantenimiento,
            (SELECT COUNT(*) FROM vehiculos WHERE LOWER(estado) = 'reservado') AS vehiculos_en_reserva,
            (SELECT COUNT(*) FROM reservas WHERE LOWER(estado) = 'pendiente') AS reservas_pendientes,
            (SELECT COUNT(*) FROM reservas r WHERE NOT EXISTS (SELECT 1 FROM pagos p WHERE p.reserva_id = r.id)) AS pagos_pendientes
    ")->fetch_assoc();

    $desde = date('Y-m-01', strtotime('-5 months'));
    $actividad = [];
    $resultadoActividad = $mysqli->query("
        SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') AS periodo, COUNT(*) AS reservas
        FROM reservas WHERE fecha_creacion >= '$desde'
        GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m') ORDER BY periodo ASC
    ");
    while ($fila = $resultadoActividad->fetch_assoc()) {
        $actividad[] = ['periodo' => $fila['periodo'], 'reservas' => (int) $fila['reservas']];
    }

    echo json_encode([
        'exito' => true,
        'resumen' => [
            'totalVehiculos' => (int) $resumen['total_vehiculos'],
            'reservasMes' => (int) $resumen['reservas_mes'],
            'totalClientes' => (int) $resumen['total_clientes'],
            'ingresosMes' => (float) $resumen['ingresos_mes'],
            'vehiculosDisponibles' => (int) $resumen['vehiculos_disponibles'],
            'vehiculosAlquilados' => (int) $resumen['vehiculos_alquilados'],
            'vehiculosMantenimiento' => (int) $resumen['vehiculos_mantenimiento'],
            'vehiculosEnReserva' => (int) $resumen['vehiculos_en_reserva'],
            'reservasPendientes' => (int) $resumen['reservas_pendientes'],
            // Reserva sin registro asociado en pagos = pago pendiente.
            'pagosPendientes' => (int) $resumen['pagos_pendientes'],
        ],
        'actividadMensual' => $actividad,
    ]);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudieron consultar los reportes.']);
}
$mysqli->close();
