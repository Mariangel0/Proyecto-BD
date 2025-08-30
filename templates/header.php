<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$usuario   = $_SESSION['user'] ?? null;
$iniciales = $usuario ? strtoupper(substr($usuario, 0, 2)) : '';
?>
<header class="professional-header py-3">
  <div class="container-fluid header-content">
    <div class="d-flex justify-content-between align-items-center">

      <div class="brand-section">
        <div class="brand-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="brand-text">
          <h1 class="text-white">Sistema de Auditoría de Base de Datos</h1>
          <p class="subtitle text-white">MSBR Auditoría - Evaluación de Seguridad y Rendimiento</p>
        </div>
      </div>

      <?php if ($usuario): ?>
        <div class="user-section">
          <div class="user-welcome">
            <div class="user-avatar"><?= htmlspecialchars($iniciales) ?></div>
            <span>Bienvenido, <strong><?= htmlspecialchars($usuario) ?></strong></span>
          </div>
          <a href="/proyecto/logout" class="logout-btn" title="Cerrar sesión">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </div>
      <?php endif; ?>

    </div>
  </div>
</header>
