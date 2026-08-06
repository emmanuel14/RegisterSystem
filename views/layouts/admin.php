<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? Helpers\Helper::e($pageTitle) . ' — ' : '' ?><?= Helpers\Helper::e($settings['church_name'] ?? 'EMS') ?> Admin</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="<?= Helpers\Helper::asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body class="ems-body">

<?php $B = fn(string $p = '') => Helpers\Helper::base($p); ?>

<!-- ── Sidebar ────────────────────────────────────────────────── -->
<nav class="ems-sidebar" id="sidebar">
    <div class="sidebar-brand">
        <?php if (!empty($settings['church_logo'])): ?>
            <img src="<?= Helpers\Helper::e($settings['church_logo']) ?>" alt="Logo" class="sidebar-logo">
        <?php else: ?>
            <div class="sidebar-icon-brand">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
        <?php endif; ?>
        <div class="sidebar-brand-text">
            <span class="brand-name"><?= Helpers\Helper::e($settings['church_name'] ?? 'EMS') ?></span>
            <span class="brand-sub">Event Manager</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-section-label">Main</li>

        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/dashboard') || str_ends_with($_SERVER['REQUEST_URI'], '/admin') ? 'active' : '' ?>">
            <a href="<?= $B('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>

        <li class="nav-section-label">Events</li>

        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/events') ? 'active' : '' ?>">
            <a href="<?= $B('admin/events') ?>"><i class="bi bi-calendar3"></i> All Events</a>
        </li>
        <li class="nav-item">
            <a href="<?= $B('admin/events/create') ?>"><i class="bi bi-plus-circle"></i> New Event</a>
        </li>

        <li class="nav-section-label">Attendees</li>

        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/registrations') ? 'active' : '' ?>">
            <a href="<?= $B('admin/registrations') ?>"><i class="bi bi-people-fill"></i> Registrations</a>
        </li>
        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/checkin') ? 'active' : '' ?>">
            <a href="<?= $B('admin/checkin') ?>"><i class="bi bi-qr-code-scan"></i> QR Check-In</a>
        </li>

        <li class="nav-section-label">Insights</li>

        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/reports') ? 'active' : '' ?>">
            <a href="<?= $B('admin/reports') ?>"><i class="bi bi-bar-chart-fill"></i> Reports</a>
        </li>

        <li class="nav-section-label">System</li>

        <?php if (($admin['role'] ?? '') === 'superadmin'): ?>
        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/settings') ? 'active' : '' ?>">
            <a href="<?= $B('admin/settings') ?>"><i class="bi bi-gear-fill"></i> Settings</a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="<?= $B('admin/logout') ?>"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </li>
    </ul>
</nav>

<!-- ── Main Content ───────────────────────────────────────────── -->
<div class="ems-main" id="main-content">

    <!-- Top Bar -->
    <header class="ems-topbar">
        <button class="btn btn-link sidebar-toggle" id="sidebarToggle" type="button">
            <i class="bi bi-list fs-5"></i>
        </button>

        <div class="topbar-right">
            <a href="<?= $B() ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-eye"></i> View Site
            </a>
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <div class="avatar-circle"><?= Helpers\Helper::avatarInitials($admin['name'] ?? 'A') ?></div>
                    <span class="d-none d-md-inline"><?= Helpers\Helper::e($admin['name'] ?? '') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text text-muted small"><?= Helpers\Helper::e($admin['email'] ?? '') ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= $B('admin/logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php foreach (['success', 'error', 'warning', 'info'] as $type): ?>
        <?php if (!empty($flash[$type])): ?>
            <?php $bsType = $type === 'error' ? 'danger' : $type; ?>
            <div class="alert alert-<?= $bsType ?> alert-dismissible fade show mx-3 mt-3 mb-0" role="alert">
                <i class="bi bi-<?= $type === 'success' ? 'check-circle' : ($type === 'error' ? 'x-circle' : ($type === 'warning' ? 'exclamation-triangle' : 'info-circle')) ?> me-2"></i>
                <?= implode('<br>', $flash[$type]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Page Content -->
    <main class="ems-content">
        <?= $content ?>
    </main>

    <footer class="ems-footer">
        <span>&copy; <?= date('Y') ?> <?= Helpers\Helper::e($settings['church_name'] ?? 'EMS') ?> &mdash; Event Management System</span>
    </footer>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<!-- Admin JS -->
<script src="<?= Helpers\Helper::asset('js/admin.js') ?>"></script>
<?php if (isset($extraJs)): echo $extraJs; endif; ?>
</body>
</html>
