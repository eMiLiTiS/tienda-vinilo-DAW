<?php
session_start();
// Usar conexión de Railway en producción, local en desarrollo
if (getenv('RAILWAY_ENVIRONMENT')) {
    require_once __DIR__ . '/conexion_railway.php';
} else {
    require_once __DIR__ . '/conexion.php';
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Método no permitido");
}

// ✅ Recoger y validar datos
$nombre = trim($_POST['nombre'] ?? '');
$artista = trim($_POST['artista'] ?? ''); // ✅ AÑADIDO: Campo artista
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$anio = intval($_POST['anio'] ?? 0);

// Validaciones
$errores = [];

if (empty($nombre) || strlen($nombre) < 2) {
    $errores[] = "El nombre debe tener al menos 2 caracteres.";
}

if (strlen($nombre) > 200) {
    $errores[] = "El nombre no puede exceder 200 caracteres.";
}

if (strlen($artista) > 150) {
    $errores[] = "El nombre del artista no puede exceder 150 caracteres.";
}

if (empty($descripcion) || strlen($descripcion) < 10) {
    $errores[] = "La descripción debe tener al menos 10 caracteres.";
}

if ($precio <= 0 || $precio > 999999.99) {
    $errores[] = "El precio debe estar entre 0.01 y 999,999.99 €";
}

if ($anio < 1900 || $anio > date('Y') + 1) {
    $errores[] = "El año debe estar entre 1900 y " . (date('Y') + 1);
}

if (!empty($errores)) {
    echo "<script>
        alert('Errores de validación:\\n" . implode("\\n", $errores) . "');
        window.history.back();
    </script>";
    exit;
}

// ✅ Validar imagen
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    die("<script>
        alert('Error: No se ha subido ninguna imagen o ha ocurrido un error.');
        window.history.back();
    </script>");
}

$archivo = $_FILES['imagen'];
$maxSize = 5 * 1024 * 1024; // 5 MB

if ($archivo['size'] > $maxSize) {
    die("<script>
        alert('Error: La imagen no puede superar 5 MB.');
        window.history.back();
    </script>");
}

// ✅ Validar tipo MIME
$allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $archivo['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedMimes)) {
    die("<script>
        alert('Error: Solo se permiten imágenes (JPG, PNG, GIF, WEBP).');
        window.history.back();
    </script>");
}

// ✅ Validar extensión
$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExts)) {
    die("<script>
        alert('Error: Extensión de archivo no permitida.');
        window.history.back();
    </script>");
}

// ✅ Crear directorio si no existe
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        die("<script>
            alert('Error: No se pudo crear el directorio de uploads.');
            window.history.back();
        </script>");
    }
}

// ✅ Generar nombre seguro
$safeName = uniqid('vinilo_', true) . '.' . $ext;
$destination = $uploadDir . $safeName;
$rutaDB = 'uploads/' . $safeName;

// ✅ Mover archivo
if (!move_uploaded_file($archivo['tmp_name'], $destination)) {
    die("<script>
        alert('Error: No se pudo guardar la imagen en el servidor.');
        window.history.back();
    </script>");
}

// ✅ Insertar en BD con prepared statement (SEGURO)
$stmt = $conn->prepare("
    INSERT INTO vinilos (nombre, artista, descripcion, precio, anio, imagen, visible)
    VALUES (?, ?, ?, ?, ?, ?, 1)
");

if (!$stmt) {
    unlink($destination); // Borrar imagen si falla
    error_log("Error preparando statement: " . $conn->error);
    die("<script>
        alert('Error del sistema. Por favor, inténtalo de nuevo.');
        window.history.back();
    </script>");
}

// ✅ CORREGIDO: Ahora incluye artista (6 parámetros)
$stmt->bind_param("sssdis", $nombre, $artista, $descripcion, $precio, $anio, $rutaDB);

if (!$stmt->execute()) {
    unlink($destination); // Borrar imagen si falla
    error_log("Error ejecutando insert: " . $stmt->error);
    $stmt->close();
    $conn->close();
    die("<script>
        alert('Error al guardar en la base de datos.');
        window.history.back();
    </script>");
}

$stmt->close();
$conn->close();

// ✅ CORREGIDO: Ruta relativa
echo "<script>
    alert('¡Vinilo agregado exitosamente!');
    window.location.href = 'gestionar_catalogo.php';
</script>";
exit;
?>