<?php
/**
 * VINYL LAB - Configuración
 * Para estructura: backend/ y frontend/
 */

// ============================================
// DETECCIÓN DE ENTORNO
// ============================================

define('ES_PRODUCCION', getenv("MYSQLHOST") !== false);

define('ES_HTTPS', 
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    $_SERVER['SERVER_PORT'] == 443 ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

// ============================================
// RUTAS
// ============================================

define('PROTOCOLO', ES_HTTPS ? 'https://' : 'http://');
define('HOST', $_SERVER['HTTP_HOST']);

// ⚠️ CAMBIA ESTO según tu carpeta
// Si tu proyecto está en C:\xampp\htdocs\vinilos\
define('ROOT_PATH', '/vinilos/');

// Si tu proyecto está en C:\xampp\htdocs\tienda-vinilo-daw\
// define('ROOT_PATH', '/tienda-vinilo-daw/');

define('BASE_URL', PROTOCOLO . HOST . ROOT_PATH);

// URLs principales
define('URL_BACKEND', BASE_URL . 'backend/');
define('URL_FRONTEND', BASE_URL . 'frontend/');
define('URL_UPLOADS', URL_BACKEND . 'uploads/');
define('URL_IMAGENES', URL_FRONTEND . 'imagenes/');

// ============================================
// BASE DE DATOS
// ============================================

if (ES_PRODUCCION) {
    // PRODUCCIÓN (Railway, Heroku, etc.)
    define('DB_HOST', getenv("MYSQLHOST"));
    define('DB_USER', getenv("MYSQLUSER"));
    define('DB_PASS', getenv("MYSQLPASSWORD"));
    define('DB_NAME', getenv("MYSQLDATABASE"));
    define('DB_PORT', (int) getenv("MYSQLPORT") ?: 3306);
} else {
    // DESARROLLO LOCAL (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'login_vinyl');  // ✅ Tu nombre de BD
    define('DB_PORT', 3306);
}

// ============================================
// CONFIGURACIÓN APP
// ============================================

define('APP_NAME', 'Vinyl Lab');
define('APP_TAGLINE', 'El sonido del pasado, con la calidez del presente');

define('SESSION_NAME', 'vinyl_lab_session');
define('SESSION_LIFETIME', 7200); // 2 horas

define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_MIME_TYPES', [
    'image/jpeg',
    'image/jpg', 
    'image/png',
    'image/gif',
    'image/webp'
]);

// ============================================
// DEBUG
// ============================================

if (!ES_PRODUCCION) {
    define('DEBUG_MODE', true);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    define('DEBUG_MODE', false);
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================
// FUNCIONES HELPER
// ============================================

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function redirect_to_login() {
    redirect(URL_FRONTEND . 'login.html');
}

function redirect_to_index() {
    redirect(URL_BACKEND . 'index.php');
}

?>