<?php
$host        = getenv('MYSQLHOST') ?: 'localhost';
$usuario     = getenv('MYSQLUSER') ?: 'root';
$contrasena  = getenv('MYSQLPASSWORD') ?: '';
$base_datos  = getenv('MYSQLDATABASE') ?: 'login_vinyl';
$puerto      = (getenv('MYSQLPORT') ?: 3306);

$conn = new mysqli($host, $usuario, $contrasena, $base_datos, $puerto);

if ($conn->connect_error) {
    $esProduccion = getenv('MYSQLHOST') !== false;
    if ($esProduccion) {
        die("Error de conexión. Contacte al administrador.");
    } else {
        die("Error de conexión: " . $conn->connect_error);
    }
}

$conn->set_charset("utf8mb4");
