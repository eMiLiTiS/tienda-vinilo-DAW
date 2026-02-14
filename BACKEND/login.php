<?php

// Configurar la sesión ANTES de session_start()
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', '1'); // Solo HTTPS
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_lifetime', '86400'); // 24 horas

session_start();

// CORS headers
$allowed_origins = [
    'https://tienda-vinilo-daw.vercel.app',
    'http://localhost:3000',
    'http://localhost:5173',
    'http://127.0.0.1:5500'
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowed_origins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Credentials: true');
} else {
    if ($origin) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Origin no permitido']);
        exit();
    }
}




header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json');

// Manejar peticiones OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir archivo de conexión
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);

    $usuario = isset($input['usuario']) ? trim($input['usuario']) : '';
    $contrasena = isset($input['contrasena']) ? trim($input['contrasena']) : '';

    if (empty($usuario) || empty($contrasena)) {
        echo json_encode([
            'success' => false,
            'message' => 'Usuario y contraseña son requeridos'
        ]);
        exit();
    }

    try {
        // CORRECCIÓN: Usar 'nombre' y 'pass' según la estructura de tu tabla
        $stmt = $conn->prepare("SELECT id, nombre, pass FROM usuarios WHERE nombre = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $row = $resultado->fetch_assoc();

            // Verificar contraseña
            // Primero intentamos con password_verify (para contraseñas hasheadas)
            // Si falla, comparamos directamente (para contraseñas en texto plano)
            $password_valida = hash_equals($row['pass'], $contrasena);

            if ($password_valida) {
                $upd = $conn->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
                $upd->bind_param("i", $row['id']);
                $upd->execute();
                $upd->close();

                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);

                // Guardar datos en la sesión
                $_SESSION['usuario'] = $row['nombre'];
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['login_time'] = time();

                echo json_encode([
                    'success' => true,
                    'message' => 'Login exitoso',
                    'usuario' => $row['nombre']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuario o contraseña incorrectos'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos'
            ]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}

$conn->close();
