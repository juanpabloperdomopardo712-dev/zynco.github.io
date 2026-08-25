<?php
/* Devuelve las cifras de la pestaña Inicio (tarjetas resumen); el navegador nunca accede directamente a MySQL. */
header("Content-Type: application/json; charset=utf-8");
session_start();
require "conectar.php";
require "sesion.php";

try {
    $resumen = $mysqli->query("
        SELECT
            (SELECT COUNT(*) FROM vehiculos) AS total_vehiculos,
            (SELECT COUNT(*) FROM reservas
                WHERE LOWER(estado) <> 'cancelada'
                  AND fecha_inicio <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                  AND fecha_fin >= CURDATE()) AS reservas_activas_semana,
            (SELECT COUNT(*) FROM clientes) AS total_clientes,
            (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE fecha_pago >= DATE_FORMAT(CURDATE(), '%Y-%m-01')) AS ingresos_mes
    ")->fetch_assoc();

    echo json_encode([
        'exito' => true,
        'resumen' => [
            'totalVehiculos' => (int) $resumen['total_vehiculos'],
            'reservasActivasSemana' => (int) $resumen['reservas_activas_semana'],
            'totalClientes' => (int) $resumen['total_clientes'],
            'ingresosMes' => (float) $resumen['ingresos_mes'],
        ],
    ]);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudieron consultar los datos de inicio.']);
}
$mysqli->close();
