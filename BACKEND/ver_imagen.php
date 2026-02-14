<?php
$f = $_GET['f'] ?? '';
$f = basename($f); // seguridad básica

// uploads está en /app/uploads
$base = __DIR__ . '/../uploads/';
$path = $base . $f;

if (!is_file($path)) {
    http_response_code(404);
    exit('Not found');
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mime = match ($ext) {
    'jpg', 'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    default => 'application/octet-stream'
};

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
