<?php
session_start();
// Usar conexión de Railway en producción, local en desarrollo
if (getenv('RAILWAY_ENVIRONMENT')) {
  require_once __DIR__ . '/conexion_railway.php';
} else {
  require_once __DIR__ . '/conexion.php';
}


// Obtener lista de vinilos para el filtro
$vinilos_query = $conn->query("SELECT id, nombre FROM vinilos ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Gestionar Reseñas - Vinyl Lab</title>
  <!-- Favicon -->
  <link rel="icon" href="data:image/svg+xml,
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'>
<text y='0.9em' font-size='400'>💿</text>
</svg>">

  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
</head>

<body style="
  background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/wood_pattern.png');
  background-attachment: fixed;
">

  <!-- HEADER -->
  <header class="main-header">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="header-left d-flex align-items-center">
        <img src="../FRONTEND/imagenes/VinylLab.png" class="header-logo me-2">
        <h1 class="header-title">Vinyl Lab</h1>
      </div>

      <div class="d-flex align-items-center gap-2">
        <a href="gestionar_catalogo.php" class="btn-login-custom">Gestionar vinilos</a>
        <a href="index.php" class="btn-login-custom">Inicio</a>
        <a href="logout.php" class="btn-login-custom">Cerrar sesión</a>

        <button class="btn btn-hamburguesa" type="button"
          data-bs-toggle="offcanvas" data-bs-target="#menuLateral"
          id="btnHamburguesa">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </div>
  </header>

  <!-- MENÚ LATERAL -->
  <div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="menuLateral">
    <div class="offcanvas-header">
      <img src="../FRONTEND/imagenes/VinylLab.png" class="sidebar-logo">
    </div>
  </div>

  <!-- CONTENIDO -->
  <main class="container py-5" style="margin-top:130px;">
    <div class="card shadow-lg mx-auto p-4"
      style="max-width:1400px; background-color:rgba(255,243,230,0.97); border-radius:16px;">

      <h2 class="text-center mb-4" style="font-family:'Bebas Neue'; color:#5a2c0d;">
        <i class="bi bi-chat-square-quote me-2"></i>
        Gestión de Reseñas
      </h2>

      <!-- FILTROS -->
      <div class="row mb-4">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Filtrar por vinilo:</label>
          <select id="filtroVinilo" class="form-select">
            <option value="">Todos los vinilos</option>
            <?php while ($v = $vinilos_query->fetch_assoc()): ?>
              <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['nombre']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Filtrar por ciudad:</label>
          <input
            type="text"
            id="filtroCiudad"
            class="form-control"
            placeholder="Escribe una ciudad..."
            autocomplete="off">
        </div>
      </div>

      <!-- TABLA -->
      <div class="table-responsive">
        <table class="table align-middle">
          <thead style="background-color:#3d2714; color:white;">
            <tr>
              <th>Vinilo</th>
              <th>Usuario</th>
              <th>Ciudad</th>
              <th>Comentario</th>
              <th>Fecha</th>
              <th style="width:120px;">Acciones</th>
            </tr>
          </thead>
          <tbody id="resultado">
            <!-- Resultados AJAX -->
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- BÚSQUEDA EN TIEMPO REAL -->
  <script>
    const filtroVinilo = document.getElementById('filtroVinilo');
    const filtroCiudad = document.getElementById('filtroCiudad');
    const resultado = document.getElementById('resultado');

    function buscarResenas() {
      const vinilo_id = filtroVinilo.value;
      const ciudad = filtroCiudad.value;

      const params = new URLSearchParams();
      if (vinilo_id) params.append('vinilo_id', vinilo_id);
      if (ciudad) params.append('ciudad', ciudad);

      fetch('buscar_resenas.php?' + params.toString())
        .then(res => res.text())
        .then(data => {
          resultado.innerHTML = data;
        })
        .catch(err => {
          console.error('Error:', err);
          resultado.innerHTML = '<tr><td colspan="6" class="text-danger text-center">Error al cargar las reseñas</td></tr>';
        });
    }

    // Carga inicial
    buscarResenas();

    // Eventos en tiempo real
    filtroVinilo.addEventListener('change', buscarResenas);
    filtroCiudad.addEventListener('keyup', buscarResenas);
  </script>

</body>

</html>