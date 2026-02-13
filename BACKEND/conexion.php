<?php
// conexion.php (wrapper robusto)

// 1) Si existe conexion_railway.php, intentamos usarlo SIEMPRE en producción.
// (En local también podría existir, pero no pasa nada si no hay env vars: abajo controlamos fallback)
$railwayFile = __DIR__ . '/conexion_railway.php';
$localFile   = __DIR__ . '/conexion_local.php';

// Si estamos en Railway / producción, normalmente NO existe conexion_local.php.
$hasRailway = file_exists($railwayFile);
$hasLocal   = file_exists($localFile);

// Heurística: si hay alguna env var típica de Railway/DB, asumimos producción
$looksProd = (getenv('RAILWAY_ENVIRONMENT') || getenv('RAILWAY_PROJECT_ID') || getenv('MYSQL_URL') || getenv('DATABASE_URL') || getenv('MYSQLHOST'));

// Preferimos Railway cuando:
// - estamos en prod, o
// - no existe el local, o
// - existe railway y queremos forzar usarlo
if ($hasRailway && ($looksProd || !$hasLocal)) {
    require_once $railwayFile;
    return;
}

// Fallback a local (solo si existe)
if ($hasLocal) {
    require_once $localFile;
    return;
}

http_response_code(500);
die("No se encontró conexion_local.php ni se pudo cargar conexion_railway.php.");
