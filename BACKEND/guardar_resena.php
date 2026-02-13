<?php
session_start();
// Usar conexión de Railway en producción, local en desarrollo
if (getenv('RAILWAY_ENVIRONMENT')) {
    require_once __DIR__ . '/conexion_railway.php';
} else {
    require_once __DIR__ . '/conexion.php';
}


// ✅ Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: catalogo.php');
    exit;
}

// ✅ Recoger y sanear datos
$vinilo_id  = isset($_POST['vinilo_id'])  ? (int)trim($_POST['vinilo_id'])   : 0;
$nombre     = isset($_POST['nombre'])     ? trim($_POST['nombre'])           : '';
$ciudad     = isset($_POST['ciudad'])     ? trim($_POST['ciudad'])           : '';
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario'])       : '';

// ✅ Validaciones básicas
if ($vinilo_id <= 0 || $nombre === '' || $ciudad === '' || $comentario === '') {
    echo "<script>
        alert('Error: Todos los campos son obligatorios.');
        window.history.back();
    </script>";
    exit;
}

// Validaciones adicionales
if (strlen($nombre) < 2 || strlen($nombre) > 100) {
    echo "<script>
        alert('Error: El nombre debe tener entre 2 y 100 caracteres.');
        window.history.back();
    </script>";
    exit;
}

if (strlen($ciudad) < 2 || strlen($ciudad) > 100) {
    echo "<script>
        alert('Error: La ciudad debe tener entre 2 y 100 caracteres.');
        window.history.back();
    </script>";
    exit;
}

if (strlen($comentario) < 10 || strlen($comentario) > 1000) {
    echo "<script>
        alert('Error: El comentario debe tener entre 10 y 1000 caracteres.');
        window.history.back();
    </script>";
    exit;
}

// ✅ Verificar que el vinilo existe y es visible
$stmt = $conn->prepare("SELECT id FROM vinilos WHERE id = ? AND visible = 1");
$stmt->bind_param('i', $vinilo_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Error: El vinilo no existe o no está disponible.');
        window.location.href = 'catalogo.php';
    </script>";
    exit;
}
$stmt->close();

// ✅ Insertar la reseña en la BD
$insert = $conn->prepare(
    "INSERT INTO resenas (vinilo_id, nombre, ciudad, comentario) VALUES (?, ?, ?, ?)"
);

if (!$insert) {
    error_log("Error preparando insert de reseña: " . $conn->error);
    echo "<script>
        alert('Error del sistema. Por favor, inténtalo de nuevo.');
        window.history.back();
    </script>";
    exit;
}

$insert->bind_param('isss', $vinilo_id, $nombre, $ciudad, $comentario);

if ($insert->execute()) {
    $insert->close();
    $conn->close();
    
    // ✅ CORREGIDO: Ruta relativa
    echo "<script>
        alert('¡Gracias! Tu reseña ha sido enviada correctamente.');
        window.location.href = 'catalogo.php?resena=ok';
    </script>";
    exit;
} else {
    error_log("Error insertando reseña: " . $insert->error);
    $insert->close();
    $conn->close();
    
    echo "<script>
        alert('Error al guardar la reseña. Por favor, inténtalo de nuevo.');
        window.history.back();
    </script>";
    exit;
}
?>
