/**
 * Small shared form helpers used across public-facing forms.
 */

function fieldError(name, message) {
  const input = document.querySelector(`[name="${name}"]`);
  const errorEl = document.querySelector(`[data-error-for="${name}"]`);
  if (input) input.classList.toggle('invalid', Boolean(message));
  if (errorEl) errorEl.textContent = message || '';
}

function clearAllErrors(form) {
  form.querySelectorAll('.invalid').forEach((el) => el.classList.remove('invalid'));
  form.querySelectorAll('[data-error-for]').forEach((el) => (el.textContent = ''));
}

function showAlert(alertEl, message, type) {
  if (!alertEl) return;
  alertEl.textContent = message;
  alertEl.className = `form-alert show ${type}`;
}

function hideAlert(alertEl) {
  if (!alertEl) return;
  alertEl.className = 'form-alert';
}

async function postJSON(path, payload) {
  const response = await fetch(path, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });
  let data;
  try {
    data = await response.json();
  } catch (e) {
    data = { success: false, message: 'Unexpected server response.' };
  }
  return { ok: response.ok, status: response.status, data };
}
