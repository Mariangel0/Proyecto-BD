<?php
session_start();
if (isset($_SESSION['user'])) {
  header("Location: index.php");
  exit;
}
$hasError = isset($_GET['error']) && $_GET['error'] === '1';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistema de Auditoría</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./frontend/css/login.css">
</head>

<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="brand-icon"><i class="bi bi-shield-lock"></i></div>
        <h1 class="login-title">Sistema de Auditoría</h1>
        <p class="login-subtitle">Evaluación de Seguridad y Rendimiento</p>
      </div>

      <form class="login-form" method="POST" id="loginForm" action="backend/database/login.php">
        <div class="form-group">
          <label for="username" class="form-label"><i class="bi bi-person"></i> Usuario</label>
          <input type="text" class="form-control" id="username" name="username" required autocomplete="username"
            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label for="password" class="form-label"><i class="bi bi-lock"></i> Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
        </div>

        <button type="submit" class="login-btn"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</button>

        <div id="error-message" class="error-message <?php echo $hasError ? '' : 'hidden'; ?>">
          <i class="bi bi-exclamation-triangle"></i>
          <span>Usuario o contraseña incorrectos</span>
        </div>
      </form>

      <div class="test-credentials">
        <h6>
          <i class="bi bi-info-circle"></i>
          Credenciales de Prueba
        </h6>
        <small>Usuario de demo creado en BD: <strong>admin</strong> / <strong>admin123</strong></small>
      </div>


    </div>
  </div>
  <script src="./frontend/script/login.js"></script>
</body>

</html>