<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? Helpers\Helper::e($pageTitle) . ' — ' : '' ?><?= Helpers\Helper::e($settings['church_name'] ?? 'EMS') ?> Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= Helpers\Helper::asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body class="ems-body">
<?php $B = fn(string $p = '') => Helpers\Helper::base($p); ?>

<!-- Sidebar Backdrop (mobile) -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- ── Sidebar ────────────────────────────────────────────────── -->
<nav class="ems-sidebar" id="sidebar">

    <!-- Brand -->
    <a class="sidebar-brand" href="<?= $B('admin/dashboard') ?>">
        <?php if (!empty($settings['church_logo'])): ?>
            <img src="<?= Helpers\Helper::e($settings['church_logo']) ?>" class="sidebar-logo" alt="Logo">
        <?php else: ?>
            <div class="sidebar-icon-brand"><i class="bi bi-calendar-event-fill"></i></div>
        <?php endif; ?>
        <div class="sidebar-brand-text">
            <span class="brand-name"><?= Helpers\Helper::e($settings['church_name'] ?? 'EMS') ?></span>
            <span class="brand-sub">Event Management</span>
        </div>
    </a>

    <!-- Nav -->
    <ul class="sidebar-nav">
        <li class="nav-section-label">Overview</li>

        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/dashboard') || str_ends_with(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH), '/admin') ? 'active' : '' ?>">
            <a href="<?= $B('admin/dashboard') ?>">
                <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
            </a>
        </li>

        <li class="nav-section-label">Events</li>

        <li class="nav-item <?= preg_match('#/admin/events(?!/|$)|/admin/events$#', $_SERVER['REQUEST_URI']) ? 'active' : '' ?>">
            <a href="<?= $B('admin/events') ?>">
                <i class="bi bi-calendar3 nav-icon"></i> All Events
            </a>
        </li>
        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/events/create') ? 'active' : '' ?>">
            <a href="<?= $B('admin/events/create') ?>">
                <i class="bi bi-plus-circle nav-icon"></i> New Event
            </a>
        </li>

        <li class="nav-section-label">Attendees</li>

        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/registrations') ? 'active' : '' ?>">
            <a href="<?= $B('admin/registrations') ?>">
                <i class="bi bi-people-fill nav-icon"></i> Registrations
            </a>
        </li>
        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/checkin') ? 'active' : '' ?>">
            <a href="<?= $B('admin/checkin') ?>">
                <i class="bi bi-qr-code-scan nav-icon"></i> QR Check-In
            </a>
        </li>

        <li class="nav-section-label">Analytics</li>

        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/reports') ? 'active' : '' ?>">
            <a href="<?= $B('admin/reports') ?>">
                <i class="bi bi-bar-chart-line-fill nav-icon"></i> Reports
            </a>
        </li>

        <li class="nav-section-label">System</li>

        <?php if (($admin['role'] ?? '') === 'superadmin'): ?>
        <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/settings') ? 'active' : '' ?>">
            <a href="<?= $B('admin/settings') ?>">
                <i class="bi bi-gear-fill nav-icon"></i> Settings
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="<?= $B() ?>" target="_blank">
                <i class="bi bi-globe2 nav-icon"></i> View Website
            </a>
        </li>
    </ul>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <a href="<?= $B('admin/logout') ?>" class="logout-btn">
            <i class="bi bi-box-arrow-left" style="font-size:16px"></i>
            <span>Sign Out</span>
        </a>
    </div>
</nav>

<!-- ── Main ───────────────────────────────────────────────────── -->
<div class="ems-main" id="mainContent">

    <!-- Topbar -->
    <header class="ems-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <!-- Breadcrumb placeholder — views can override via $pageTitle -->
            <div class="d-none d-md-block">
                <span style="font-size:13px;font-weight:600;color:var(--text-primary)"><?= Helpers\Helper::e($pageTitle ?? 'Dashboard') ?></span>
            </div>
        </div>

        <div class="topbar-right">
            <!-- View site -->
            <a href="<?= $B() ?>" target="_blank" class="topbar-icon-btn" title="View public site">
                <i class="bi bi-arrow-up-right-square"></i>
            </a>

            <div class="topbar-divider"></div>

            <!-- User dropdown -->
            <div class="dropdown">
                <button class="user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-offset="0,8">
                    <div class="avatar"><?= Helpers\Helper::avatarInitials($admin['name'] ?? 'A') ?></div>
                    <div class="d-none d-md-block text-start">
                        <div class="user-name"><?= Helpers\Helper::e($admin['name'] ?? '') ?></div>
                        <div class="user-role"><?= Helpers\Helper::e(ucfirst($admin['role'] ?? '')) ?></div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px">
                    <li>
                        <div class="px-3 py-2">
                            <div style="font-size:13px;font-weight:600"><?= Helpers\Helper::e($admin['name'] ?? '') ?></div>
                            <div style="font-size:12px;color:var(--text-muted)"><?= Helpers\Helper::e($admin['email'] ?? '') ?></div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= $B('admin/settings') ?>"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><a class="dropdown-item text-danger" href="<?= $B('admin/logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php foreach (['success' => ['check-circle-fill','success'], 'error' => ['x-circle-fill','danger'], 'warning' => ['exclamation-triangle-fill','warning'], 'info' => ['info-circle-fill','info']] as $type => [$icon, $bsType]): ?>
        <?php if (!empty($flash[$type])): ?>
            <div class="mx-4 mt-4">
                <div class="alert alert-<?= $bsType ?> alert-dismissible d-flex align-items-start gap-2 fade show mb-0" role="alert" style="border-radius:var(--r-md);border:none;box-shadow:var(--shadow-sm)">
                    <i class="bi bi-<?= $icon ?> mt-1 flex-shrink-0"></i>
                    <div><?= implode('<br>', $flash[$type]) ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Content -->
    <main class="ems-content">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="ems-footer">
        <span>&copy; <?= date('Y') ?> <?= Helpers\Helper::e($settings['church_name'] ?? 'EMS') ?></span>
        <span>Event Management System</span>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="<?= Helpers\Helper::asset('js/admin.js') ?>"></script>
<?php if (isset($extraJs)): echo $extraJs; endif; ?>
</body>
</html>
