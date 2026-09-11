/**
 * Shared behaviour across all public pages: populates placeholder site copy
 * from SITE_CONFIG, renders the data-driven sections, and wires simple UI bits.
 */
document.addEventListener('DOMContentLoaded', () => {
  applySiteText();
  applySiteLinks();
  applyMailtoLinks();
  renderNav();
  renderHeroStats();
  renderHeroVisual();
  renderFeatures();
  renderCodeBlock();
  renderGettingStartedSteps();
  renderProcess();
  renderWhyUs();
  renderStats();
  renderDemoSteps();
  wireMobileNav();
  wireYear();
});

/** Fills any element with [data-site="dot.path.into.SITE_CONFIG"]. */
function applySiteText() {
  document.querySelectorAll('[data-site]').forEach((el) => {
    const key = el.getAttribute('data-site');
    const value = key.split('.').reduce((obj, k) => (obj ? obj[k] : undefined), SITE_CONFIG);
    if (value !== undefined) {
      el.textContent = value;
    }
  });
}

/** Fills any element with [data-site-href="dot.path"] as its href attribute. */
function applySiteLinks() {
  document.querySelectorAll('[data-site-href]').forEach((el) => {
    const key = el.getAttribute('data-site-href');
    const value = key.split('.').reduce((obj, k) => (obj ? obj[k] : undefined), SITE_CONFIG);
    if (value) {
      el.setAttribute('href', value);
    }
  });
}

/** Sets href="mailto:<value>" on any element with [data-site-mailto="dot.path"]. */
function applyMailtoLinks() {
  document.querySelectorAll('[data-site-mailto]').forEach((el) => {
    const key = el.getAttribute('data-site-mailto');
    const value = key.split('.').reduce((obj, k) => (obj ? obj[k] : undefined), SITE_CONFIG);
    if (value) {
      el.setAttribute('href', `mailto:${value}`);
    }
  });
}

/** Renders SITE_CONFIG.nav links into [data-nav-links] (wraps in <li> when the container is a <ul>). */
function renderNav() {
  document.querySelectorAll('[data-nav-links]').forEach((el) => {
    const isList = el.tagName === 'UL' || el.tagName === 'OL';
    el.innerHTML = SITE_CONFIG.nav
      .map((item) => {
        const link = `<a href="${item.href}">${item.label}</a>`;
        return isList ? `<li>${link}</li>` : link;
      })
      .join('');
  });
}

/** Renders SITE_CONFIG.heroStats into [data-hero-stats]. */
function renderHeroStats() {
  document.querySelectorAll('[data-hero-stats]').forEach((el) => {
    el.innerHTML = SITE_CONFIG.heroStats
      .map(
        (s) => `
        <div>
          <div class="stat-num">${s.value}</div>
          <div class="stat-lbl">${s.label}</div>
        </div>`
      )
      .join('');
  });
}

/** Renders SITE_CONFIG.heroVisual into [data-hero-visual]. */
function renderHeroVisual() {
  document.querySelectorAll('[data-hero-visual]').forEach((el) => {
    const v = SITE_CONFIG.heroVisual;
    const nodes = v.nodes
      .map((n) => `<div class="hero-visual-node"><strong>${n.title}</strong><span>${n.caption}</span></div>`)
      .join('');
    el.innerHTML = `
      <div class="hero-visual-head">
        <span class="eyebrow">${v.label}</span>
        <span class="live-dot">Live</span>
      </div>
      <div class="hero-visual-grid">
        ${nodes}
        <div class="hero-visual-center">
          <strong>${v.centerTitle}</strong>
          <span>${v.centerCaption}</span>
        </div>
      </div>`;
  });
}

/** Renders SITE_CONFIG.features as top-accent-bordered cards into [data-feature-grid]. */
function renderFeatures() {
  document.querySelectorAll('[data-feature-grid]').forEach((el) => {
    el.innerHTML = SITE_CONFIG.features
      .map(
        (f) => `
        <div class="accent-card">
          <div class="icon-badge">${f.icon}</div>
          <h3>${f.title}</h3>
          <p>${f.description}</p>
          <div>${f.tags.map((t) => `<span class="tag-pill">${t}</span>`).join('')}</div>
          <a href="#demo" class="card-link">${f.linkLabel} →</a>
        </div>`
      )
      .join('');
  });
}

/** Renders SITE_CONFIG.gettingStarted.commands as terminal lines into [data-code-block]. */
function renderCodeBlock() {
  document.querySelectorAll('[data-code-block]').forEach((el) => {
    el.innerHTML = SITE_CONFIG.gettingStarted.commands
      .map((cmd) => `<div class="code-line"><span class="code-prompt">$</span>${cmd}</div>`)
      .join('');
  });
}

/** Renders SITE_CONFIG.gettingStarted.steps into [data-getting-started-steps]. */
function renderGettingStartedSteps() {
  document.querySelectorAll('[data-getting-started-steps]').forEach((el) => {
    el.innerHTML = SITE_CONFIG.gettingStarted.steps
      .map(
        (s, i) => `
        <div class="checklist-row">
          <span class="check-icon">${i + 1}</span>
          <div><strong>${s.title}</strong><span>${s.description}</span></div>
        </div>`
      )
      .join('');
  });
}

/** Renders SITE_CONFIG.process into [data-process-list]. */
function renderProcess() {
  document.querySelectorAll('[data-process-list]').forEach((el) => {
    el.innerHTML = SITE_CONFIG.process
      .map(
        (p, i) => `
        <div class="process-step">
          <div class="step-num">${i + 1}</div>
          <h3>${p.title}</h3>
          <p>${p.description}</p>
        </div>`
      )
      .join('');
  });
}

/** Renders SITE_CONFIG.whyUs cards into [data-whyus-grid]. */
function renderWhyUs() {
  document.querySelectorAll('[data-whyus-grid]').forEach((el) => {
    el.innerHTML = SITE_CONFIG.whyUs
      .map(
        (f) => `
        <div class="card">
          <div class="icon-badge">${f.icon}</div>
          <h3>${f.title}</h3>
          <p>${f.description}</p>
        </div>`
      )
      .join('');
  });
}

/** Renders SITE_CONFIG.stats into [data-stats-grid]. */
function renderStats() {
  document.querySelectorAll('[data-stats-grid]').forEach((el) => {
    el.innerHTML = SITE_CONFIG.stats
      .map(
        (s) => `
        <div class="stat-block">
          <div class="stat-figure gradient-text">${s.figure}</div>
          <div class="stat-caption">${s.caption}</div>
        </div>`
      )
      .join('');
  });
}

/** Renders SITE_CONFIG.demo.steps into [data-demo-steps]. */
function renderDemoSteps() {
  document.querySelectorAll('[data-demo-steps]').forEach((el) => {
    el.innerHTML = SITE_CONFIG.demo.steps
      .map(
        (s) => `
        <div class="summary-row">
          <span class="icon">${s.icon}</span>
          <div><strong>${s.title}</strong><span class="muted">${s.description}</span></div>
        </div>`
      )
      .join('');
  });
}

function wireMobileNav() {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.main-nav');
  const header = document.querySelector('.site-header');
  if (!toggle || !nav || !header) return;

  function setOpen(open) {
    nav.classList.toggle('open', open);
    toggle.textContent = open ? '✕' : '☰';
    toggle.setAttribute('aria-expanded', String(open));
  }

  toggle.addEventListener('click', () => setOpen(!nav.classList.contains('open')));
  nav.addEventListener('click', (e) => {
    if (e.target.tagName === 'A') setOpen(false);
  });
  document.addEventListener('click', (e) => {
    if (nav.classList.contains('open') && !header.contains(e.target)) setOpen(false);
  });
}

function wireYear() {
  document.querySelectorAll('[data-year]').forEach((el) => {
    el.textContent = new Date().getFullYear();
  });
}
