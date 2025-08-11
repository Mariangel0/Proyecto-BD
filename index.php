<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sistema de Auditoría - Base de Datos</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

  <link rel="stylesheet" href="./frontend/css/styles.css"/>
</head>
<body>

  <?php include './frontend/components/header.php'; ?>

  <div class="main-container">
    <div class="content-wrapper">

      <!-- Progreso -->
      <div class="progress-section">
        <div class="progress-info">
          <div class="progress-label">
            <i class="bi bi-bar-chart-line"></i>
            Progreso de la Auditoría
          </div>
          <span id="progress-text" class="badge bg-primary">0 de 0 completadas</span>
        </div>
        <div class="progress">
          <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
      </div>

      <form id="audit-form" action="#" method="POST">
        <div class="mb-3">
          <label for="company-input" class="form-label">
            <i class="bi bi-building"></i> Nombre de la Empresa *
          </label>
          <input 
            type="text" 
            class="form-control" 
            id="company-input" 
            placeholder="Ingresa el nombre de la empresa"
            required>
        </div>
        <div id="sections-container"></div>

        <div class="action-section">
          <button type="button" id="btn-calculate" class="btn btn-primary btn-calculate">
            <i class="bi bi-calculator"></i> Calcular Puntaje de Auditoría
          </button>
        </div>

        <div id="result-card" class="result-card" style="display: none;">
          <div id="score-icon" class="score-icon"></div>
          <div id="score-number" class="score-number">0%</div>
          <div id="score-label" class="score-label">Calculando...</div>
          <div id="score-details" class="mt-3"></div>
        </div>

      </form>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script src="./frontend/script/script.js"></script>
</body>
</html>
