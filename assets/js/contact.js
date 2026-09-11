/**
 * Wires the example contact form (#contact-form) to backend/api/contact.php.
 * Uses the shared helpers in assets/js/forms.js. This is the pattern to copy
 * for any new form: clear errors, POST as JSON, map field errors back onto
 * inputs, and show a success state.
 */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contact-form');
  if (!form) return;

  // The alert and success elements are siblings of the form, not children —
  // query from the document, not the form.
  const alertEl = document.querySelector('[data-form-alert]');
  const submitBtn = form.querySelector('[type="submit"]');
  const successEl = document.querySelector('[data-form-success]');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearAllErrors(form);
    hideAlert(alertEl);

    const payload = {
      full_name: form.full_name.value.trim(),
      email: form.email.value.trim(),
      phone: form.phone.value.trim(),
      message: form.message.value.trim(),
      website: form.website ? form.website.value : '', // honeypot
    };

    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending…';

    try {
      const { ok, data } = await postJSON(`${SITE_CONFIG.apiBase}/contact.php`, payload);

      if (ok && data.success) {
        form.hidden = true;
        if (successEl) successEl.classList.add('show');
      } else if (data.errors) {
        Object.entries(data.errors).forEach(([field, message]) => fieldError(field, message));
        showAlert(alertEl, data.message || 'Please correct the highlighted fields.', 'error');
      } else {
        showAlert(alertEl, data.message || 'Something went wrong. Please try again.', 'error');
      }
    } catch (err) {
      showAlert(alertEl, 'Network error. Please check your connection and try again.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Send Message';
    }
  });
});
