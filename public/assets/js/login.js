document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  if (!form) return;

  const submitBtn = form.querySelector('button[type="submit"]');
  let errorBox = document.getElementById('error-message');

  const showError = (msg) => {
    if (!errorBox) return;
    const span = errorBox.querySelector('span') || errorBox;
    span.textContent = msg;
    errorBox.classList.remove('hidden');
  };
  const hideError = () => errorBox?.classList.add('hidden');

  const params = new URLSearchParams(location.search);
  if (params.get('error') === '1') {
    showError('Usuario o contraseña incorrectos');
    history.replaceState({}, '', location.pathname);
  }

  form.addEventListener('submit', (e) => {
    const u = form.username.value.trim();
    const p = form.password.value.trim();
    if (!u || !p) {
      e.preventDefault();
      showError('Por favor complete todos los campos');
      return;
    }
    hideError();
    if (submitBtn) {
      submitBtn.dataset.originalText = submitBtn.textContent;
      submitBtn.textContent = 'Ingresando...';
      submitBtn.disabled = true;
    }
  });

  form.querySelectorAll('input').forEach(inp => {
    inp.addEventListener('input', hideError);
  });
});
