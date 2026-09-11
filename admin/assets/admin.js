document.addEventListener('DOMContentLoaded', () => {
  wireTableSearch();
  wireDetailModals();
  wireDeleteConfirm();
});

function wireTableSearch() {
  const input = document.querySelector('.search-input');
  const table = document.querySelector('.data-table');
  if (!input || !table) return;
  const rows = Array.from(table.querySelectorAll('tbody tr'));

  input.addEventListener('input', () => {
    const q = input.value.trim().toLowerCase();
    rows.forEach((row) => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(q) ? '' : 'none';
    });
  });
}

function wireDetailModals() {
  const overlay = document.getElementById('detail-modal');
  if (!overlay) return;
  const body = overlay.querySelector('.modal-box');

  document.querySelectorAll('[data-view-detail]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const template = document.getElementById(trigger.dataset.viewDetail);
      if (!template) return;
      body.innerHTML = template.innerHTML;
      overlay.classList.add('open');
    });
  });

  overlay.addEventListener('click', (e) => {
    if (e.target === overlay || e.target.closest('[data-close-modal]')) {
      overlay.classList.remove('open');
    }
  });
}

function wireDeleteConfirm() {
  document.querySelectorAll('.delete-form').forEach((form) => {
    form.addEventListener('submit', (e) => {
      if (!confirm('Are you sure you want to delete this record? This cannot be undone.')) {
        e.preventDefault();
      }
    });
  });
}
