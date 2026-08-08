/* ============================================================
   EMS Admin JS v2.0
   ============================================================ */
(function () {
  'use strict';

  // ── Sidebar Toggle ──────────────────────────────────────────
  const sidebar   = document.getElementById('sidebar');
  const toggle    = document.getElementById('sidebarToggle');
  const main      = document.getElementById('mainContent');
  const backdrop  = document.getElementById('sidebarBackdrop');

  function isMobile() { return window.innerWidth < 992; }

  function openSidebar() {
    sidebar.classList.add('open');
    backdrop && backdrop.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    backdrop && backdrop.classList.remove('show');
    document.body.style.overflow = '';
  }

  if (toggle) {
    toggle.addEventListener('click', function () {
      if (isMobile()) {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
      } else {
        // Desktop collapse
        const isCollapsed = sidebar.classList.contains('collapsed');
        sidebar.classList.toggle('collapsed');
        if (main) main.style.marginLeft = isCollapsed ? '' : '0';
        sidebar.style.width = isCollapsed ? '' : '0';
        sidebar.style.overflow = isCollapsed ? '' : 'hidden';
      }
    });
  }

  backdrop && backdrop.addEventListener('click', closeSidebar);
  window.addEventListener('resize', function () {
    if (!isMobile()) {
      closeSidebar();
      if (sidebar) { sidebar.style.width = ''; sidebar.style.overflow = ''; }
      if (main) main.style.marginLeft = '';
    }
  });

  // ── Auto dismiss success alerts ─────────────────────────────
  document.querySelectorAll('.alert-success, .alert-info').forEach(function (el) {
    setTimeout(function () {
      try { bootstrap.Alert.getOrCreateInstance(el)?.close(); } catch(e) {}
    }, 4000);
  });

  // ── Tooltip init ────────────────────────────────────────────
  document.querySelectorAll('[title]:not([data-bs-toggle])').forEach(function (el) {
    try { new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top' }); } catch(e) {}
  });

  // ── Form dirty protection ───────────────────────────────────
  let formDirty = false;
  document.querySelectorAll('form:not([data-no-dirty]) input, form:not([data-no-dirty]) select, form:not([data-no-dirty]) textarea').forEach(function (el) {
    el.addEventListener('change', function () { formDirty = true; });
  });
  window.addEventListener('beforeunload', function (e) {
    if (formDirty) { e.preventDefault(); e.returnValue = ''; }
  });
  document.querySelectorAll('form').forEach(function (f) {
    f.addEventListener('submit', function () { formDirty = false; });
  });

  // ── SweetAlert2 theme override ──────────────────────────────
  if (window.Swal) {
    const SwalStyled = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-primary me-2',
        cancelButton:  'btn btn-outline-secondary',
        popup:         'shadow-lg',
      },
      buttonsStyling: false,
      borderRadius: '14px',
    });
    window.SwalStyled = SwalStyled;
  }

})();
