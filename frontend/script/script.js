// frontend/script/script.js

class AuditSystem {
  constructor() {
    this.form = document.getElementById('audit-form');
    this.calculateBtn = document.getElementById('btn-calculate');
    this.resultCard = document.getElementById('result-card');
    this.progressBar = document.getElementById('progress-bar');
    this.progressText = document.getElementById('progress-text');

    this.bindEvents();
    this.updateProgress();
  }

  bindEvents() {
    // Recalcular progreso y pintar fila cada vez que cambie una respuesta
    this.form.addEventListener('change', (ev) => {
      if (ev.target && ev.target.matches('input[type="radio"]')) {
        this.paintRow(ev.target);
        this.updateProgress();
      }
    });

    // Calcular puntaje
    this.calculateBtn.addEventListener('click', () => this.calculateScore());
  }

  // Obtiene el total de preguntas por nombre de grupo (A1_1, A1_2, ...)
  getTotalQuestions() {
    const names = new Set();
    this.form.querySelectorAll('input[type="radio"]').forEach(r => names.add(r.name));
    return names.size;
  }

  // Obtiene cuántas preguntas tienen alguna opción marcada
  getAnsweredQuestions() {
    const names = new Set();
    this.form.querySelectorAll('input[type="radio"]:checked').forEach(r => names.add(r.name));
    return names.size;
  }

  // Actualiza la barra de progreso y el texto
  updateProgress() {
    const total = this.getTotalQuestions();
    const answered = this.getAnsweredQuestions();
    const pct = total > 0 ? Math.round((answered / total) * 100) : 0;

    this.progressBar.style.width = `${pct}%`;
    this.progressText.textContent = `${answered} de ${total} completadas`;

    // Habilita calcular solo si todas las preguntas están respondidas
    this.calculateBtn.disabled = answered < total || total === 0;
  }

  // Colorea la fila con clases de Bootstrap según la respuesta
  paintRow(radioEl) {
    const tr = radioEl.closest('tr');
    if (!tr) return;

    tr.classList.remove('table-success', 'table-danger', 'table-secondary');

    const value = radioEl.value;
    if (value === 'yes') tr.classList.add('table-success');
    else if (value === 'no') tr.classList.add('table-danger');
    else if (value === 'na') tr.classList.add('table-secondary');
  }

  // Calcula porcentaje (sí / (sí + no)) — NA no cuenta
  calculateScore() {
    const groups = this.groupAnswers();
    let yes = 0, no = 0;

    Object.values(groups).forEach(val => {
      if (val === 'yes') yes++;
      else if (val === 'no') no++;
      // 'na' no suma al denominador
    });

    const totalEvaluado = yes + no;
    if (totalEvaluado === 0) {
      this.showError('Responde al menos una pregunta (Sí o No) para calcular el puntaje.');
      return;
    }

    const pct = Math.round((yes / totalEvaluado) * 100);
    this.displayResult(pct, yes, totalEvaluado);
  }

  // Devuelve un mapa { nombrePregunta: valorSeleccionado }
  groupAnswers() {
    const map = {};
    this.form.querySelectorAll('input[type="radio"]:checked').forEach(r => {
      map[r.name] = r.value; // yes | no | na
    });
    return map;
  }

  // Muestra la tarjeta de resultado con estilo según el porcentaje
  displayResult(percentage, yesCount, totalAnswered) {
    const scoreNumber = document.getElementById('score-number');
    const scoreLabel = document.getElementById('score-label');
    const scoreIcon = document.getElementById('score-icon');
    const scoreDetails = document.getElementById('score-details');

    // Reset clases
    this.resultCard.className = 'result-card show';

    let category, icon, message;
    if (percentage >= 80) {
      category = 'score-excellent';
      icon = 'bi-shield-check';
      message = 'Excelente nivel de seguridad';
    } else if (percentage >= 60) {
      category = 'score-good';
      icon = 'bi-shield-exclamation';
      message = 'Buen nivel, con áreas de mejora';
    } else {
      category = 'score-poor';
      icon = 'bi-shield-x';
      message = 'Requiere atención inmediata';
    }

    this.resultCard.classList.add(category);
    scoreNumber.textContent = `${percentage}%`;
    scoreLabel.textContent = message;
    scoreIcon.innerHTML = `<i class="bi ${icon}"></i>`;

    scoreDetails.innerHTML = `
      <div class="row g-3 mt-2">
        <div class="col-md-4 text-center">
          <div class="fw-bold">Respuestas Positivas</div>
          <div class="fs-4">${yesCount}</div>
        </div>
        <div class="col-md-4 text-center">
          <div class="fw-bold">Total Evaluado</div>
          <div class="fs-4">${totalAnswered}</div>
        </div>
        <div class="col-md-4 text-center">
          <div class="fw-bold">Puntaje Final</div>
          <div class="fs-4">${percentage}%</div>
        </div>
      </div>
    `;

    this.resultCard.style.display = 'block';
    this.resultCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  showError(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
    alertDiv.innerHTML = `
      <i class="bi bi-exclamation-triangle"></i>
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    this.calculateBtn.parentNode.insertBefore(alertDiv, this.calculateBtn.nextSibling);
    setTimeout(() => alertDiv.remove(), 5000);
  }
}

// Iniciar cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => new AuditSystem());
