<?php
session_start();
// Usar conexión de Railway en producción, local en desarrollo
if (getenv('RAILWAY_ENVIRONMENT')) {
    require_once __DIR__ . '/conexion_railway.php';
} else {
    require_once __DIR__ . '/conexion.php';
}


// ✅ Validar ID
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo "<script>
        alert('Error: ID de vinilo no válido.');
        window.location.href = 'gestionar_catalogo.php';
    </script>";
    exit;
}

// ✅ SEGURO: Usar prepared statement
$sql = "UPDATE vinilos SET visible = NOT visible WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Error preparando update de visibilidad: " . $conn->error);
    echo "<script>
        alert('Error del sistema.');
        window.location.href = 'gestionar_catalogo.php';
    </script>";
    exit;
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    error_log("Error ejecutando update de visibilidad: " . $stmt->error);
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Error al cambiar la visibilidad.');
        window.location.href = 'gestionar_catalogo.php';
    </script>";
    exit;
}

// Verificar si se actualizó alguna fila
if ($stmt->affected_rows === 0) {
    error_log("No se encontró el vinilo con ID: " . $id);
}

$stmt->close();
$conn->close();

// ✅ CORREGIDO: Ruta relativa (sin echo/script, redirect directo)
header("Location: gestionar_catalogo.php");
exit;
?>