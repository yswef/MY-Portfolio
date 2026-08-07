function initThemeToggle() {
  const toggle = document.getElementById('themeSwitch');
  let saved = null;
  try { saved = localStorage.getItem('theme'); } catch (e) {}
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  const dark = saved ? saved === 'dark' : prefersDark;
  document.body.classList.toggle('dark-theme', dark);
  if (toggle) {
    toggle.checked = dark;
    toggle.addEventListener('change', () => {
      document.body.classList.toggle('dark-theme', toggle.checked);
      try { localStorage.setItem('theme', toggle.checked ? 'dark' : 'light'); } catch (e) {}
    });
  }
}

function initNavbarScroll() {
  const nav = document.querySelector('.navbar');
  if (!nav) return;
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 30);
  }, { passive: true });
}

function initMobileDrawer() {
  const burger = document.getElementById('navBurger');
  const drawer = document.getElementById('mobileDrawer');
  const closeBtn = document.getElementById('closeDrawer');
  if (!burger || !drawer) return;
  burger.addEventListener('click', () => drawer.classList.add('open'));
  if (closeBtn) closeBtn.addEventListener('click', () => drawer.classList.remove('open'));
  drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', () => drawer.classList.remove('open')));
}

function initRevealOnScroll() {
  const items = document.querySelectorAll('.reveal');
  if (!items.length || !window.IntersectionObserver) {
    items.forEach(el => el.classList.add('in'));
    return;
  }
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in');
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });
  items.forEach(el => obs.observe(el));
}

function initCounters() {
  const counters = document.querySelectorAll('[data-counter]');
  counters.forEach(el => {
    const target = parseInt(el.dataset.counter, 10) || 0;
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 40));
    const tick = () => {
      current = Math.min(target, current + step);
      el.textContent = current;
      if (current < target) requestAnimationFrame(tick);
    };
    tick();
  });
}

function initCursorGlow() {
  if (window.matchMedia('(pointer: coarse)').matches) return; // تجاهل اللمس
  const glow = document.createElement('div');
  glow.className = 'cursor-glow';
  document.body.appendChild(glow);
  document.addEventListener('mousemove', (e) => {
    glow.style.left = e.clientX + 'px';
    glow.style.top = e.clientY + 'px';
  });
}

let currentLang = 'ar';

async function loadTranslations(lang) {
  try {
    const res = await fetch(`translations/${lang}.json`);
    if (!res.ok) return lang !== 'en' ? loadTranslations('en') : null;
    return await res.json();
  } catch (e) {
    console.error('translation load failed', e);
    return null;
  }
}

function applyTranslations(dict) {
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (dict[key] !== undefined) el.innerHTML = dict[key];
  });
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.getAttribute('data-i18n-placeholder');
    if (dict[key] !== undefined) el.setAttribute('placeholder', dict[key]);
  });
}

async function loadProjects(lang) {
  const grid = document.getElementById('projectsGrid');
  if (!grid) return;
  try {
    const res = await fetch(`api/projects.php?lang=${encodeURIComponent(lang)}`);
    const data = await res.json();
    const projects = data.projects || [];
    if (!projects.length) {
      grid.innerHTML = '<p class="projects-empty" data-i18n="projects_empty"></p>';
      return;
    }
    grid.innerHTML = projects.map(p => `
      <div class="project-card reveal in">
        ${p.image ? `<div class="thumb-wrap"><img src="${p.image}" alt="${escapeHtml(p.title)}" loading="lazy"></div>` : ''}
        <div class="project-card-body">
          <h3>${escapeHtml(p.title)}</h3>
          <p>${escapeHtml(p.description)}</p>
          <div class="project-links">
            ${p.github_url ? `<a href="${escapeAttr(p.github_url)}" target="_blank" rel="noopener noreferrer">GitHub</a>` : ''}
            ${p.download_url ? `<a href="${escapeAttr(p.download_url)}" target="_blank" rel="noopener noreferrer">Download</a>` : ''}
          </div>
        </div>
      </div>
    `).join('');
  } catch (e) {
    console.error('projects load failed', e);
  }
}

function escapeHtml(str) {
  const d = document.createElement('div');
  d.textContent = str || '';
  return d.innerHTML;
}
function escapeAttr(str) {
  return (str || '').replace(/"/g, '&quot;');
}

async function loadBio(lang) {
  const introEl = document.getElementById('bioIntro');
  const dreamEl = document.getElementById('bioDream');
  if (!introEl && !dreamEl) return;
  try {
    const res = await fetch(`api/bio.php?lang=${encodeURIComponent(lang)}`);
    const data = await res.json();
    if (introEl && data.intro) introEl.textContent = data.intro;
    if (dreamEl && data.dream) dreamEl.textContent = data.dream;
  } catch (e) {
    console.error('bio load failed', e);
  }
}

function initLanguageSwitcher() {
  const select = document.getElementById('langSelect');
  if (!select) return;

  let saved = null;
  try { saved = localStorage.getItem('language'); } catch (e) {}
  const preferred = saved || (navigator.language.startsWith('ar') ? 'ar' : 'en');
  select.value = preferred;

  async function setLang(lang) {
    currentLang = lang;
    const dict = await loadTranslations(lang);
    if (dict) applyTranslations(dict);
    document.documentElement.setAttribute('lang', lang);
    document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
    document.body.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
    loadProjects(lang);
    loadBio(lang);
  }

  setLang(preferred);
  select.addEventListener('change', () => {
    try { localStorage.setItem('language', select.value); } catch (e) {}
    setLang(select.value);
  });
}

function initContactForm() {
  const form = document.getElementById('contactForm');
  if (!form) return;
  const msgBox = document.getElementById('formMsg');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    try {
      const res = await fetch('api/contact.php', { method: 'POST', body: new FormData(form) });
      const data = await res.json();
      msgBox.className = 'form-msg show ' + (data.ok ? 'ok' : 'err');
      msgBox.textContent = data.ok
        ? (msgBox.dataset.ok || 'تم إرسال رسالتك بنجاح!')
        : (msgBox.dataset.err || 'حدث خطأ، حاول مرة أخرى.');
      if (data.ok) form.reset();
    } catch (err) {
      msgBox.className = 'form-msg show err';
      msgBox.textContent = msgBox.dataset.err || 'حدث خطأ، حاول مرة أخرى.';
    } finally {
      submitBtn.disabled = false;
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const features = [
    initThemeToggle,
    initNavbarScroll,
    initMobileDrawer,
    initRevealOnScroll,
    initCounters,
    initCursorGlow,
    initLanguageSwitcher,
    initContactForm
  ];
  features.forEach(fn => {
    try { fn(); } catch (err) { console.error(`[${fn.name}] failed:`, err); }
  });
});
