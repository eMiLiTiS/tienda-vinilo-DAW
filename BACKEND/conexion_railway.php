<?php
/**
 * CONEXIÓN A BASE DE DATOS - RAILWAY
 * Este archivo usa variables de entorno de Railway
 */

// ✅ Obtener credenciales de variables de entorno (Railway)
$host = getenv('MYSQLHOST') ?: 'localhost';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$port = (int)(getenv('MYSQLPORT') ?: 3306);

// ✅ Crear conexión
$conn = new mysqli($host, $user, $pass, $db, $port);

// ✅ Verificar conexión
if ($conn->connect_error) {
    error_log("Error de conexión MySQL: " . $conn->connect_error);
    die("Error de conexión a la base de datos.");
}

// ✅ Configurar charset
$conn->set_charset("utf8mb4");

// ✅ Funciones helper (las mismas que tienes en conexion.php local)
function ejecutarConsulta($sql) {
    global $conn;
    $resultado = $conn->query($sql);
    if (!$resultado) {
        error_log("Error en consulta SQL: " . $conn->error);
        return false;
    }
    return $resultado;
}

function limpiarHTML($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function limpiarInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

// ✅ Funciones de sesión
function iniciarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }
}

function estaAutenticado() {
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
}

function requiereAutenticacion() {
    if (!estaAutenticado()) {
        header("Location: https://vinyl-labs.vercel.app/login.html");
        exit;
    }
}
?>