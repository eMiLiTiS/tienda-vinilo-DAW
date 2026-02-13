<?php
session_start();
require_once __DIR__ . '/conexion.php';

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
$sql = "SELECT imagen FROM vinilos WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Error preparando consulta de eliminación: " . $conn->error);
    echo "<script>
        alert('Error del sistema.');
        window.location.href = 'gestionar_catalogo.php';
    </script>";
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Error: Vinilo no encontrado.');
        window.location.href = 'gestionar_catalogo.php';
    </script>";
    exit;
}

$vinilo = $result->fetch_assoc();
$stmt->close();

// ✅ Eliminar imagen físicamente (con validación de ruta)
$rutaImagen = __DIR__ . '/' . $vinilo['imagen'];
$directorioPermitido = realpath(__DIR__ . '/uploads');
$rutaRealImagen = realpath($rutaImagen);

// Verificar que la ruta está dentro del directorio permitido (seguridad)
if ($rutaRealImagen && 
    file_exists($rutaRealImagen) && 
    strpos($rutaRealImagen, $directorioPermitido) === 0) {
    
    if (!unlink($rutaRealImagen)) {
        error_log("No se pudo eliminar la imagen: " . $rutaRealImagen);
    }
} else {
    error_log("Ruta de imagen inválida o fuera del directorio permitido: " . $rutaImagen);
}

// ✅ SEGURO: Eliminar de BD con prepared statement
$sql = "DELETE FROM vinilos WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Error preparando delete: " . $conn->error);
    echo "<script>
        alert('Error al eliminar el vinilo.');
        window.location.href = 'gestionar_catalogo.php';
    </script>";
    exit;
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    error_log("Error ejecutando delete: " . $stmt->error);
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Error al eliminar el vinilo de la base de datos.');
        window.location.href = 'gestionar_catalogo.php';
    </script>";
    exit;
}

$stmt->close();
$conn->close();

// ✅ CORREGIDO: Ruta relativa
echo "<script>
    alert('¡Vinilo eliminado exitosamente!');
    window.location.href = 'gestionar_catalogo.php';
</script>";
exit;
?>