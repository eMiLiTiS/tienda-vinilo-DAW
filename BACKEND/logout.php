<?php
session_start();

// ✅ Destruir sesión completamente
$_SESSION = array();

// ✅ Eliminar cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ✅ Destruir sesión
session_destroy();

// ✅ CORREGIDO: Ruta relativa
header("Location: index.php");
exit;
?>