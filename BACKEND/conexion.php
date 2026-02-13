<?php
// Wrapper de conexión: local vs Railway

$enRailway = (getenv('MYSQLHOST') || getenv('MYSQLUSER') || getenv('MYSQLDATABASE') || getenv('MYSQLPORT'));

if ($enRailway) {
    require_once __DIR__ . '/conexion_railway.php';
} else {
    require_once __DIR__ . '/conexion_local.php';
}
