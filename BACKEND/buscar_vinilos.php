<?php
session_start();
// Usar conexión de Railway en producción, local en desarrollo
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
  while ($v = $result->fetch_assoc()):
?>
    <tr>
      <td>
        <img src="<?= htmlspecialchars($v['imagen']) ?>"
          style="width:70px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.2);"
          onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESin imagen%3C/text%3E%3C/svg%3E'">
      </td>

      <td style="font-weight:600;">
        <?= htmlspecialchars($v['nombre']) ?>
      </td>

      <td>
        <?= number_format($v['precio'], 2, ',', '.') ?> €
      </td>

      <td>
        <span class="badge <?= $v['visible'] ? 'bg-success' : 'bg-secondary' ?>">
          <?= $v['visible'] ? 'Visible' : 'Oculto' ?>
        </span>
      </td>

      <td class="d-flex gap-2 justify-content-center">
        <!-- ✅ CORREGIDO: Ruta relativa -->
        <a href="toggle_vinilo.php?id=<?= $v['id'] ?>"
          class="btn btn-sm"
          style="background-color:#c48a3a;color:white;">
          <?= $v['visible'] ? 'Ocultar' : 'Mostrar' ?>
        </a>

        <!-- ✅ CORREGIDO: Ruta relativa -->
        <a href="eliminar_vinilo.php?id=<?= $v['id'] ?>"
          class="btn btn-danger btn-sm"
          onclick="return confirm('¿Eliminar este vinilo?')">
          Eliminar
        </a>
      </td>
    </tr>
<?php
  endwhile;
}

$stmt->close();
$conn->close();
?>