<?php
session_start();

// Conexión
if (getenv('RAILWAY_ENVIRONMENT')) {
  require_once __DIR__ . '/conexion_railway.php';
} else {
  require_once __DIR__ . '/conexion.php';
}

$buscar = $_GET['buscar'] ?? '';
$like = "%$buscar%";

$sql = "SELECT * FROM vinilos WHERE nombre LIKE ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo '<tr>
          <td colspan="5" class="text-center text-muted py-4">
            <i class="bi bi-search"></i><br>
            No se encontraron vinilos
          </td>
        </tr>';
} else {
  while ($v = $result->fetch_assoc()) {
    echo '<tr>';

    // IMAGEN (usando ver_imagen.php)
    echo '<td>
            <img src="ver_imagen.php?f=' . urlencode(basename($v['imagen'])) . '"
                 alt="Portada"
                 style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
          </td>';

    // NOMBRE
    echo '<td style="font-weight:600;">' . htmlspecialchars($v['nombre']) . '</td>';

    // PRECIO
    echo '<td>' . number_format((float)$v['precio'], 2, ',', '.') . ' €</td>';

    // VISIBLE
    $badgeClass = $v['visible'] ? 'bg-success' : 'bg-secondary';
    $badgeText  = $v['visible'] ? 'Visible' : 'Oculto';
    echo '<td><span class="badge ' . $badgeClass . '">' . $badgeText . '</span></td>';

    // ACCIONES
    $toggleText = $v['visible'] ? 'Ocultar' : 'Mostrar';
    echo '<td class="d-flex gap-2 justify-content-center">
            <a href="toggle_vinilo.php?id=' . (int)$v['id'] . '"
               class="btn btn-sm"
               style="background-color:#c48a3a;color:white;">
              ' . $toggleText . '
            </a>

            <a href="eliminar_vinilo.php?id=' . (int)$v['id'] . '"
               class="btn btn-danger btn-sm"
               onclick="return confirm(\'¿Eliminar este vinilo?\')">
              Eliminar
            </a>
          </td>';

    echo '</tr>';
  }
}

$stmt->close();
$conn->close();
