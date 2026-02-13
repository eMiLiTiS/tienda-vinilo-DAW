<?php
require_once __DIR__ . '/conexion.php';

$vinilo_id = isset($_GET['vinilo_id']) ? intval($_GET['vinilo_id']) : 0;
$ciudad = isset($_GET['ciudad']) ? trim($_GET['ciudad']) : '';

// Construir query con filtros opcionales
$sql = "
    SELECT 
      r.id,
      r.nombre,
      r.ciudad,
      r.comentario,
      r.fecha,
      COALESCE(v.nombre, CONCAT('Vinilo #', r.vinilo_id)) AS vinilo_nombre
    FROM resenas r
    LEFT JOIN vinilos v ON r.vinilo_id = v.id
    WHERE 1=1
";

$params = [];
$types = "";

if ($vinilo_id > 0) {
    $sql .= " AND r.vinilo_id = ?";
    $params[] = $vinilo_id;
    $types .= "i";
}

if ($ciudad !== '') {
    $like_ciudad = "%$ciudad%";
    $sql .= " AND r.ciudad LIKE ?";
    $params[] = $like_ciudad;
    $types .= "s";
}

$sql .= " ORDER BY r.fecha DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0):
?>
    <tr>
        <td colspan="6" style="padding:30px; color:#888; font-style:italic;">
            <i class="bi bi-chat-square-dots" style="font-size:1.5rem; display:block; margin-bottom:8px;"></i>
            No se encontraron opiniones con estos filtros.
        </td>
    </tr>
<?php
else:
    while ($r = $result->fetch_assoc()):
    ?>
        <tr>
            <td style="font-weight:600;">
                <?= htmlspecialchars($r['vinilo_nombre']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['nombre']) ?>
            </td>

            <td>
                <span class="badge" style="background-color:#5b3c20;">
                    <i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($r['ciudad']) ?>
                </span>
            </td>

            <td style="max-width:300px; text-align:left;">
                <?= htmlspecialchars($r['comentario']) ?>
            </td>

            <td style="white-space:nowrap; font-size:0.85rem; color:#666;">
                <?= date('d/m/Y H:i', strtotime($r['fecha'])) ?>
            </td>

            <td>
                <!-- ✅ CORREGIDO: Ruta relativa -->
                <a href="eliminar_resena.php?id=<?= (int)$r['id'] ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('¿Eliminar esta opinión?')">
                    <i class="bi bi-trash me-1"></i>Eliminar
                </a>
            </td>
        </tr>
<?php
    endwhile;
endif;

$stmt->close();
$conn->close();
?>
