<?php
/* Compatibilidad para bases de datos creadas antes de registrar quién procesó el pago. */
function asegurarTrazabilidadPagos(mysqli $mysqli): void {
    $columna = $mysqli->query("SHOW COLUMNS FROM pagos LIKE 'usuario_id'");
    if ($columna->num_rows === 0) {
        $mysqli->query("ALTER TABLE pagos ADD COLUMN usuario_id INT NULL AFTER reserva_id");
        $mysqli->query("ALTER TABLE pagos ADD INDEX idx_pagos_usuario_id (usuario_id)");
    }
}
