<?php
session_start();
// Usar conexión de Railway en producción, local en desarrollo
if (getenv('RAILWAY_ENVIRONMENT')) {
  require_once __DIR__ . '/conexion_railway.php';
} else {
  require_once __DIR__ . '/conexion.php';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Gestionar Catálogo - Vinyl Lab</title>
  <!-- Favicon -->
  <link rel="icon" href="data:image/svg+xml,
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'>
<text y='0.9em' font-size='400'>💿</text>
</svg>">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Estilos -->
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
        <!-- ✅ CORREGIDO -->
        <img src="https://tienda-vinilo-daw.vercel.app/imagenes/VinylLab.png" class="header-logo me-2">
        <h1 class="header-title">Vinyl Lab</h1>
      </div>

      <div class="d-flex align-items-center gap-2">
        <!-- ✅ CORREGIDO -->
        <a href="add_vinilos.php" class="btn-login-custom">Añadir vinilo</a>
        <a href="gestionar_resenas.php" class="btn-login-custom">Gestionar reseñas</a>
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
      <!-- ✅ CORREGIDO -->
      <img src="https://tienda-vinilo-daw.vercel.app/imagenes/VinylLab.png" class="sidebar-logo">
    </div>
  </div>

  <!-- CONTENIDO -->
  <main class="container py-5" style="margin-top:130px;">
    <div class="card shadow-lg mx-auto p-4"
      style="max-width:1200px; background-color:rgba(255,243,230,0.97); border-radius:16px;">

      <h2 class="text-center mb-4" style="font-family:'Bebas Neue'; color:#5a2c0d;">
        Gestión del catálogo
      </h2>

      <!-- BUSCADOR EN TIEMPO REAL -->
      <input
        type="text"
        id="buscar"
        class="form-control form-control-lg mb-4"
        placeholder="Buscar vinilo por nombre..."
        autocomplete="off">

      <!-- TABLA -->
      <div class="table-responsive">
        <table class="table align-middle text-center">
          <thead style="background-color:#3d2714; color:white;">
            <tr>
              <th>Imagen</th>
              <th>Nombre</th>
              <th>Precio</th>
              <th>Visible</th>
              <th>Acciones</th>
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
    const input = document.getElementById('buscar');
    const resultado = document.getElementById('resultado');

    function buscarVinilos(valor = '') {
      // ✅ CORREGIDO: Ruta relativa
      fetch('buscar_vinilos.php?buscar=' + encodeURIComponent(valor))
        .then(res => res.text())
        .then(data => {
          resultado.innerHTML = data;
        })
        .catch(err => {
          console.error('Error:', err);
          resultado.innerHTML = '<tr><td colspan="5" class="text-danger">Error al cargar los vinilos</td></tr>';
        });
    }

    // Carga inicial
    buscarVinilos();

    // Evento en tiempo real
    input.addEventListener('keyup', () => {
      buscarVinilos(input.value);
    });
  </script>
</body>
</html>