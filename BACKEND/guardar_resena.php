<?php
// ===============================
// guardar_resena.php (API JSON)
// ===============================

// Sesión (si la necesitas en el futuro)
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
session_start();

// ---------- CORS ----------
$allowed_origins = [
    'https://tienda-vinilo-daw.vercel.app',
    'http://localhost:5173',
    'http://127.0.0.1:5500',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin && in_array($origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Credentials: true');
} else {
    if ($origin) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Origin no permitido']);
        exit;
    }
}

header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json; charset=utf-8');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// ---------- Conexión ----------
// Usa tu wrapper ya creado (elige local/railway)
require_once __DIR__ . '/conexion.php';

// ---------- Leer datos (FormData o JSON) ----------
$vinilo_id = 0;
$nombre = '';
$ciudad = '';
$comentario = '';
$valoracion = null;

// 1) Primero intenta FormData (porque tu formulario envía FormData)
if (!empty($_POST)) {
    $vinilo_id  = isset($_POST['vinilo_id']) ? (int)$_POST['vinilo_id'] : 0;
    $nombre     = trim($_POST['nombre'] ?? '');
    $ciudad     = trim($_POST['ciudad'] ?? '');
    $comentario = trim($_POST['comentario'] ?? '');
    $valoracion = isset($_POST['valoracion']) ? (int)$_POST['valoracion'] : null;
} else {
    // 2) Si no hay POST, intenta JSON SIEMPRE (robusto)
    $raw = file_get_contents('php://input');
    $inputJson = json_decode($raw, true);

    if (is_array($inputJson)) {
        $vinilo_id  = (int)($inputJson['vinilo_id'] ?? 0);
        $nombre     = trim((string)($inputJson['nombre'] ?? ''));
        $ciudad     = trim((string)($inputJson['ciudad'] ?? ''));
        $comentario = trim((string)($inputJson['comentario'] ?? ''));
        $valoracion = isset($inputJson['valoracion']) ? (int)$inputJson['valoracion'] : null;
    }
}


// ---------- Validaciones ----------
if ($vinilo_id <= 0 || $nombre === '' || $ciudad === '' || $comentario === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

if (strlen($nombre) < 2 || strlen($nombre) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El nombre debe tener entre 2 y 100 caracteres.']);
    exit;
}

if (strlen($ciudad) < 2 || strlen($ciudad) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La ciudad debe tener entre 2 y 100 caracteres.']);
    exit;
}

if (strlen($comentario) < 10 || strlen($comentario) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El comentario debe tener entre 10 y 1000 caracteres.']);
    exit;
}

if ($valoracion !== null && ($valoracion < 1 || $valoracion > 5)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La valoración debe estar entre 1 y 5.']);
    exit;
}

// ---------- Verificar vinilo existente y visible ----------
$stmt = $conn->prepare("SELECT id FROM vinilos WHERE id = ? AND visible = 1");
$stmt->bind_param('i', $vinilo_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'El vinilo no existe o no está disponible.']);
    exit;
}
$stmt->close();

// ---------- Insertar reseña ----------
$insert = $conn->prepare("INSERT INTO resenas (vinilo_id, nombre, ciudad, comentario, valoracion) VALUES (?, ?, ?, ?, ?)");
if (!$insert) {
    error_log("Error preparando insert de reseña: " . $conn->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del sistema.']);
    exit;
}

$insert->bind_param('isssi', $vinilo_id, $nombre, $ciudad, $comentario, $valoracion);

if ($insert->execute()) {
    $insert->close();
    echo json_encode(['success' => true, 'message' => 'Reseña guardada correctamente.']);
    exit;
}

error_log("Error insertando reseña: " . $insert->error);
$insert->close();
http_response_code(500);
echo json_encode(['success' => false, 'message' => 'Error al guardar la reseña.']);
exit;
