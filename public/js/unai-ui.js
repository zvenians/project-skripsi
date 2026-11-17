/* Shared progressive enhancements; no existing Blade markup is replaced. */
(function (root) {
  'use strict';
  function initUnaiUI(Vue, gsap) {
    const { createApp } = Vue || {};
    const reduced = root.matchMedia && root.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const facilityTools = document.getElementById('facility-tools');
    if (facilityTools && createApp) {
      let categories = [];
      try { categories = JSON.parse(facilityTools.dataset.categories || '[]'); } catch (_) { categories = []; }
      const cards = Array.from(document.querySelectorAll('[data-facility-card]'));
      createApp({
        data() { return { query: new URLSearchParams(location.search).get('q') || '', category: 'all', categories, visible: cards.length }; },
        methods: {
          filter() {
            const query = this.query.trim().toLocaleLowerCase('id');
            let count = 0;
            cards.forEach(card => {
              const text = (card.dataset.search || '').toLocaleLowerCase('id');
              const category = card.dataset.category || '';
              const show = (!query || text.includes(query)) && (this.category === 'all' || category === this.category);
              card.hidden = !show;
              if (show) count++;
            });
            this.visible = count;
            document.getElementById('facility-empty')?.toggleAttribute('hidden', count > 0);
          }
        },
        watch: { query() { this.filter(); }, category() { this.filter(); } },
        mounted() { this.filter(); }
      }).mount('#facility-tools');
    }
    const quickFilter = document.querySelector('#dashboardQuickFilter');
    if (quickFilter) {
      const search = () => {
        const query = quickFilter.value.trim().toLocaleLowerCase('id');
        document.querySelectorAll('main .table tbody tr').forEach(row => {
          row.hidden = !!query && !row.textContent.toLocaleLowerCase('id').includes(query);
        });
      };
      quickFilter.addEventListener('input', search);
      quickFilter.closest('form')?.addEventListener('submit', event => event.preventDefault());
    }
    const observerAvailable = 'IntersectionObserver' in root;
    if (!reduced && gsap && observerAvailable) {
      const elements = document.querySelectorAll('[data-motion]');
      const observer = new IntersectionObserver(entries => {
        for (const entry of entries) {
          if (!entry.isIntersecting) continue;
          const el = entry.target;
          observer.unobserve(el);
          gsap.fromTo(el, { opacity: 0, y: 22 }, { opacity: 1, y: 0, duration: .68, delay: Math.min(Number(el.dataset.motionDelay) || 0, .5), ease: 'power2.out', clearProps: 'all' });
        }
      }, { threshold: .1, rootMargin: '0px 0px -25px 0px' });
      elements.forEach(el => observer.observe(el));
    }
    document.querySelectorAll('form[data-disable-on-submit]').forEach(form => form.addEventListener('submit', () => {
      const button = form.querySelector('button[type="submit"]');
      if (!button || !form.checkValidity()) return;
      button.disabled = true;
      button.setAttribute('aria-busy', 'true');
    }));
    document.querySelectorAll('.navbar-collapse .nav-link').forEach(link => link.addEventListener('click', () => {
      const collapse = link.closest('.navbar-collapse');
      if (collapse && collapse.classList.contains('show') && root.bootstrap?.Collapse) {
        root.bootstrap.Collapse.getOrCreateInstance(collapse).hide();
      }
    }));
    document.querySelectorAll('main table').forEach(table => {
      if (table.closest('.table-responsive')) return;
      const wrap = document.createElement('div');
      wrap.className = 'table-responsive';
      table.parentNode.insertBefore(wrap, table);
      wrap.appendChild(table);
    });
  }
  root.initUnaiUI = initUnaiUI;
})(window);
