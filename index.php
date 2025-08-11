<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Auditoría - Base de Datos</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="./frontend/css/styles.css">
</head>
<body>

  <?php include './frontend/components/header.php'; ?>

  <div class="main-container">

    <div class="content-wrapper">
      <div class="progress-section">
        <div class="progress-info">
          <div class="progress-label">
            <i class="bi bi-bar-chart-line"></i>
            Progreso de la Auditoría
          </div>
          <span id="progress-text" class="badge bg-primary">0 de 2 completadas</span>
        </div>
        <div class="progress">
          <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
      </div>

      <form id="audit-form" action="./php/process_audit.php" method="POST">

        <div class="section-card">
          <div class="section-header">
            <i class="bi bi-database-gear"></i>
            A1. Mantenimiento y Disponibilidad de la BBDD
          </div>
          
          <table class="table table-responsive mb-0">
            <tbody>
              <tr class="question-row">
                <td class="question-cell" style="width: 80px;">
                  <span class="task-id">A1.1</span>
                </td>
                <td class="question-cell">
                  <div class="question-text">
                    ¿Se realizan verificaciones periódicas para garantizar el correcto funcionamiento de las bases de datos?
                  </div>
                </td>
                <td class="question-cell text-center">
                  <div class="radio-group">
                    <div class="radio-option">
                      <input type="radio" name="a1_1" value="yes" id="a1_1_yes">
                      <label for="a1_1_yes" class="radio-label yes">Sí</label>
                    </div>
                    <div class="radio-option">
                      <input type="radio" name="a1_1" value="no" id="a1_1_no">
                      <label for="a1_1_no" class="radio-label no">No</label>
                    </div>
                    <div class="radio-option">
                      <input type="radio" name="a1_1" value="na" id="a1_1_na">
                      <label for="a1_1_na" class="radio-label na">N/A</label>
                    </div>
                  </div>
                </td>
              </tr>
              
              <tr class="question-row">
                <td class="question-cell">
                  <span class="task-id">A1.2</span>
                </td>
                <td class="question-cell">
                  <div class="question-text">
                    ¿Se optimizan los índices y consultas para mejorar el rendimiento de la base de datos?
                  </div>
                </td>
                <td class="question-cell text-center">
                  <div class="radio-group">
                    <div class="radio-option">
                      <input type="radio" name="a1_2" value="yes" id="a1_2_yes">
                      <label for="a1_2_yes" class="radio-label yes">Sí</label>
                    </div>
                    <div class="radio-option">
                      <input type="radio" name="a1_2" value="no" id="a1_2_no">
                      <label for="a1_2_no" class="radio-label no">No</label>
                    </div>
                    <div class="radio-option">
                      <input type="radio" name="a1_2" value="na" id="a1_2_na">
                      <label for="a1_2_na" class="radio-label na">N/A</label>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

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