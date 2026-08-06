/* ============================================================
   EMS – Admin JavaScript
   ============================================================ */

(function () {
  'use strict';

  // ── Sidebar Toggle ──────────────────────────────────────────
  const sidebar  = document.getElementById('sidebar');
  const toggle   = document.getElementById('sidebarToggle');
  const main     = document.getElementById('main-content');

  // Create backdrop for mobile
  const backdrop = document.createElement('div');
  backdrop.className = 'sidebar-backdrop';
  document.body.appendChild(backdrop);

  function isMobile () { return window.innerWidth < 992; }

  function openSidebar () {
    sidebar.classList.add('open');
    backdrop.classList.add('show');
  }

  function closeSidebar () {
    sidebar.classList.remove('open');
    backdrop.classList.remove('show');
  }

  function toggleSidebar () {
    if (isMobile()) {
      sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    } else {
      // Desktop: collapse/expand
      const collapsed = sidebar.style.width === '0px' || sidebar.classList.contains('collapsed');
      if (collapsed) {
        sidebar.classList.remove('collapsed');
        main.style.marginLeft = 'var(--ems-sidebar-w)';
        sidebar.style.width   = '';
      } else {
        sidebar.classList.add('collapsed');
        main.style.marginLeft = '0';
        sidebar.style.width   = '0';
      }
    }
  }

  if (toggle)   toggle.addEventListener('click', toggleSidebar);
  if (backdrop) backdrop.addEventListener('click', closeSidebar);

  window.addEventListener('resize', function () {
    if (!isMobile()) {
      closeSidebar();
      sidebar.style.width   = '';
      main.style.marginLeft = '';
      sidebar.classList.remove('collapsed');
    }
  });

  // ── Auto-dismiss alerts ─────────────────────────────────────
  document.querySelectorAll('.alert').forEach(function (el) {
    if (el.classList.contains('alert-success') || el.classList.contains('alert-info')) {
      setTimeout(function () {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        if (bsAlert) bsAlert.close();
      }, 5000);
    }
  });

  // ── Form dirty tracking ─────────────────────────────────────
  let formDirty = false;

  document.querySelectorAll('form input, form select, form textarea').forEach(function (el) {
    el.addEventListener('change', function () { formDirty = true; });
  });

  window.addEventListener('beforeunload', function (e) {
    if (formDirty) {
      e.preventDefault();
      e.returnValue = '';
    }
  });

  // Clear dirty on submit
  document.querySelectorAll('form').forEach(function (f) {
    f.addEventListener('submit', function () { formDirty = false; });
  });

  // ── Tooltip init ────────────────────────────────────────────
  document.querySelectorAll('[title]').forEach(function (el) {
    new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top' });
  });

  // ── Confirm delete via data attrs ───────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', async function (e) {
      e.preventDefault();
      const msg = this.dataset.confirm || 'Are you sure?';
      const res = await Swal.fire({
        icon: 'warning',
        title: 'Confirm',
        text: msg,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, proceed',
      });
      if (res.isConfirmed) {
        const href   = this.getAttribute('href');
        const form   = this.closest('form');
        if (href)  window.location.href = href;
        if (form)  form.submit();
      }
    });
  });

  // ── Slug generator helper (global) ─────────────────────────
  window.emsSlugify = function (text) {
    return text.toLowerCase()
      .replace(/[^\w\s-]/g, '')
      .replace(/[\s_]+/g, '-')
      .replace(/^-+|-+$/, '');
  };

})();
