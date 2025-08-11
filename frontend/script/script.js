// frontend/script/script.js
const API_URL = 'backend/database/get_audit.php';

class AuditSystem {
  constructor() {
    this.form = document.getElementById('audit-form');
    this.calculateBtn = document.getElementById('btn-calculate');
    this.resultCard = document.getElementById('result-card');
    this.progressBar = document.getElementById('progress-bar');
    this.progressText = document.getElementById('progress-text');
    this.sectionsContainer = document.getElementById('sections-container');
    this.init();
  }

  async init() {
    try {
      await this.loadQuestions();
      this.bindEvents();
      this.updateProgress();
    } catch (e) {
      console.error(e);
      this.showError('No se pudieron cargar las preguntas. Verifica el backend.');
    }
  }

  async loadQuestions() {
    const res = await fetch(API_URL, { cache: 'no-store' });
    if (!res.ok) throw new Error('Bad response from get_audit.php');
    const data = await res.json();

    this.sectionsContainer.innerHTML = '';

    data.forEach((sec) => {
      const sectionCard = document.createElement('div');
      sectionCard.className = 'section-card';

      const header = document.createElement('div');
      header.className = 'section-header';
      header.innerHTML = `<i class="bi bi-database-gear"></i> ${sec.code}. ${sec.title}`;
      sectionCard.appendChild(header);

      const tableWrapper = document.createElement('div');
      tableWrapper.className = 'table-responsive';

      const table = document.createElement('table');
      table.className = 'table align-middle mb-0';
      table.innerHTML = `
        <thead class="table-light">
          <tr>
            <th style="width:80px;">#</th>
            <th>Pregunta</th>
            <th class="text-center" style="width:70px;">Sí</th>
            <th class="text-center" style="width:70px;">No</th>
            <th class="text-center" style="width:70px;">N/A</th>
            <th class="text-center">Integridad</th>
            <th class="text-center">Confidencialidad</th>
            <th class="text-center">Disponibilidad</th>
            <th class="text-center">Norma</th>
          </tr>
        </thead>
        <tbody></tbody>
      `;
      const tbody = table.querySelector('tbody');

      sec.activities.forEach((act) => {
        act.questions.forEach((q) => {
          const name = this.normalizeCodeToName(q.code);
          const tr = document.createElement('tr');
          tr.className = 'question-row';
          tr.innerHTML = `
            <td class="question-cell"><span class="task-id">${q.code}</span></td>
            <td class="question-cell"><div class="question-text">${q.text}</div></td>
            <td class="text-center"><input type="radio" name="${name}" value="yes" id="${name}_yes"></td>
            <td class="text-center"><input type="radio" name="${name}" value="no"  id="${name}_no"></td>
            <td class="text-center"><input type="radio" name="${name}" value="na"  id="${name}_na"></td>
            <td class="text-center">${q.riesgos?.I ?? ''}</td>
            <td class="text-center">${q.riesgos?.C ?? ''}</td>
            <td class="text-center">${q.riesgos?.D ?? ''}</td>
            <td class="text-center">${q.normas || ''}</td>
          `;
          tbody.appendChild(tr);
        });
      });

      tableWrapper.appendChild(table);
      sectionCard.appendChild(tableWrapper);
      this.sectionsContainer.appendChild(sectionCard);
    });
  }

  normalizeCodeToName(code) {
    return String(code).toLowerCase().replace(/\./g, '_').replace(/\s+/g, '');
  }

  bindEvents() {
    this.form.addEventListener('change', (ev) => {
      const t = ev.target;
      if (t && t.matches('input[type="radio"]')) {
        this.paintRow(t);
        this.updateProgress();
      }
    });
    this.calculateBtn.addEventListener('click', () => this.calculateScore());
  }

  getTotalQuestions() {
    const names = new Set();
    this.form.querySelectorAll('input[type="radio"]').forEach((r) => names.add(r.name));
    return names.size;
  }

  getAnsweredQuestions() {
    const names = new Set();
    this.form.querySelectorAll('input[type="radio"]:checked').forEach((r) => names.add(r.name));
    return names.size;
  }

  updateProgress() {
    const total = this.getTotalQuestions();
    const answered = this.getAnsweredQuestions();
    const pct = total > 0 ? Math.round((answered / total) * 100) : 0;
    this.progressBar.style.width = `${pct}%`;
    this.progressText.textContent = `${answered} de ${total} completadas`;
    this.calculateBtn.disabled = answered < total || total === 0;
  }

  paintRow(radioEl) {
    const tr = radioEl.closest('tr');
    if (!tr) return;
    tr.classList.remove('table-success', 'table-danger', 'table-secondary');
    if (radioEl.value === 'yes') tr.classList.add('table-success');
    else if (radioEl.value === 'no') tr.classList.add('table-danger');
    else if (radioEl.value === 'na') tr.classList.add('table-secondary');
  }

  calculateScore() {
    const groups = this.groupAnswers();
    let yes = 0, no = 0;
    Object.values(groups).forEach((v) => { if (v === 'yes') yes++; else if (v === 'no') no++; });
    const totalEvaluado = yes + no;
    if (totalEvaluado === 0) return this.showError('Responde al menos una pregunta (Sí o No) para calcular el puntaje.');
    const pct = Math.round((yes / totalEvaluado) * 100);
    this.displayResult(pct, yes, totalEvaluado);
  }

  groupAnswers() {
    const map = {};
    this.form.querySelectorAll('input[type="radio"]:checked').forEach((r) => { map[r.name] = r.value; });
    return map;
  }

  displayResult(percentage, yesCount, totalAnswered) {
    const scoreNumber = document.getElementById('score-number');
    const scoreLabel = document.getElementById('score-label');
    const scoreIcon = document.getElementById('score-icon');
    const scoreDetails = document.getElementById('score-details');

    this.resultCard.className = 'result-card show';

    let category, icon, message;
    if (percentage >= 80) { category = 'score-excellent'; icon = 'bi-shield-check'; message = 'Excelente nivel de seguridad'; }
    else if (percentage >= 60) { category = 'score-good'; icon = 'bi-shield-exclamation'; message = 'Buen nivel, con áreas de mejora'; }
    else { category = 'score-poor'; icon = 'bi-shield-x'; message = 'Requiere atención inmediata'; }

    this.resultCard.classList.add(category);
    scoreNumber.textContent = `${percentage}%`;
    scoreLabel.textContent = message;
    scoreIcon.innerHTML = `<i class="bi ${icon}"></i>`;

    scoreDetails.innerHTML = `
      <div class="row g-3 mt-2">
        <div class="col-md-4 text-center"><div class="fw-bold">Respuestas Positivas</div><div class="fs-4">${yesCount}</div></div>
        <div class="col-md-4 text-center"><div class="fw-bold">Total Evaluado</div><div class="fs-4">${totalAnswered}</div></div>
        <div class="col-md-4 text-center"><div class="fw-bold">Puntaje Final</div><div class="fs-4">${percentage}%</div></div>
      </div>`;
    this.resultCard.style.display = 'block';
    this.resultCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  showError(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
    alertDiv.innerHTML = `<i class="bi bi-exclamation-triangle"></i> ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    this.calculateBtn.parentNode.insertBefore(alertDiv, this.calculateBtn.nextSibling);
    setTimeout(() => alertDiv.remove(), 5000);
  }
}

document.addEventListener('DOMContentLoaded', () => new AuditSystem());
