/**
 * Mindlytics Learning OS — Command Palette + shell helpers
 */
(function () {
  function qs(sel, root) { return (root || document).querySelector(sel); }
  function qsa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }

  window.MindlyticsLOS = {
    openPalette() {
      const el = qs('[data-los-palette]');
      if (!el) return;
      el.style.display = 'flex';
      const input = qs('[data-los-palette-input]', el);
      if (input) {
        input.value = '';
        input.focus();
        filterPalette('');
      }
      document.body.style.overflow = 'hidden';
    },
    closePalette() {
      const el = qs('[data-los-palette]');
      if (!el) return;
      el.style.display = 'none';
      document.body.style.overflow = '';
    },
    togglePalette() {
      const el = qs('[data-los-palette]');
      if (!el) return;
      if (el.style.display === 'flex') this.closePalette();
      else this.openPalette();
    },
  };

  function filterPalette(q) {
    const items = qsa('[data-los-palette-item]');
    const needle = (q || '').trim().toLowerCase();
    let visible = 0;
    items.forEach((item) => {
      const hay = (item.getAttribute('data-search') || item.textContent || '').toLowerCase();
      const show = !needle || hay.includes(needle);
      item.style.display = show ? 'flex' : 'none';
      item.classList.remove('is-active');
      if (show) visible++;
    });
    const empty = qs('[data-los-palette-empty]');
    if (empty) empty.style.display = visible ? 'none' : 'block';
    const first = items.find((i) => i.style.display !== 'none');
    if (first) first.classList.add('is-active');
  }

  document.addEventListener('keydown', (e) => {
    const isMac = navigator.platform.toUpperCase().includes('MAC');
    if ((isMac ? e.metaKey : e.ctrlKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      window.MindlyticsLOS.togglePalette();
    }
    if (e.key === 'Escape') window.MindlyticsLOS.closePalette();
  });

  document.addEventListener('DOMContentLoaded', () => {
    qsa('[data-los-open-palette]').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        window.MindlyticsLOS.openPalette();
      });
    });
    const backdrop = qs('[data-los-palette]');
    if (backdrop) {
      backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) window.MindlyticsLOS.closePalette();
      });
    }
    const input = qs('[data-los-palette-input]');
    if (input) {
      input.addEventListener('input', () => filterPalette(input.value));
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          const active = qs('[data-los-palette-item].is-active');
          if (active) active.click();
        }
      });
    }
  });
})();
