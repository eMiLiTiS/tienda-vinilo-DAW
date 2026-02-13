<?php
session_start();
require_once __DIR__ . '/conexion.php';

// ✅ Validar ID
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo "<script>
        alert('Error: ID de reseña no válido.');
        window.location.href = 'gestionar_resenas.php';
    </script>";
    exit;
}

// ✅ Eliminar con prepared statement
$stmt = $conn->prepare("DELETE FROM resenas WHERE id = ?");

if (!$stmt) {
    error_log("Error preparando delete de reseña: " . $conn->error);
    echo "<script>
        alert('Error del sistema.');
        window.location.href = 'gestionar_resenas.php';
    </script>";
    exit;
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    error_log("Error eliminando reseña: " . $stmt->error);
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Error al eliminar la reseña.');
        window.location.href = 'gestionar_resenas.php';
    </script>";
    exit;
}

$stmt->close();
$conn->close();

// ✅ CORREGIDO: Ruta relativa
header("Location: gestionar_resenas.php");
exit;
?>
