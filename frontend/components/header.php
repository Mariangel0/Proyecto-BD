<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$usuario = $_SESSION['user'] ?? 'Invitado';
$iniciales = strtoupper(substr($usuario, 0, 2));
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Header</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="./frontend/css/header.css" />
</head>

<body>
  <header class="professional-header py-3">
    <div class="container-fluid header-content">
      <div class="d-flex justify-content-between align-items-center">

        <!-- Sección de marca -->
        <div class="brand-section">
          <div class="brand-icon">
            <i class="bi bi-shield-lock-fill"></i>
          </div>
          <div class="brand-text">
            <h1 class="text-white">Sistema de Auditoría de Base de Datos</h1>
            <p class="subtitle text-white">MSBR Auditoría - Evaluación de Seguridad y Rendimiento</p>
          </div>
        </div>

        <!-- Sección de usuario -->
        <?php if (!empty($_SESSION['user'])): ?>
          <div class="user-section">
            <div class="user-welcome">
              <div class="user-avatar">
                <?= htmlspecialchars($iniciales) ?>
              </div>
              <span>Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong></span>
            </div>

            <a href="logout.php" class="logout-btn" title="Cerrar sesión">
              <i class="bi bi-box-arrow-right"></i>
            </a>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </header>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
</header>