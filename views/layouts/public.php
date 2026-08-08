<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? Helpers\Helper::e($pageTitle) . ' — ' : '' ?><?= Helpers\Helper::e($settings['church_name'] ?? 'Event Registration') ?></title>
    <meta name="description" content="Register for events at <?= Helpers\Helper::e($settings['church_name'] ?? '') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= Helpers\Helper::asset('css/public.css') ?>" rel="stylesheet">
</head>
<body>
<?php $B = fn(string $p = '') => Helpers\Helper::base($p); ?>

<!-- ── Navbar ─────────────────────────────────────────────────── -->
<nav class="pub-navbar navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="<?= $B() ?>">
            <?php if (!empty($settings['church_logo'])): ?>
                <img src="<?= Helpers\Helper::e($settings['church_logo']) ?>" height="34" alt="Logo" style="border-radius:8px">
            <?php else: ?>
                <div class="navbar-brand-icon"><i class="bi bi-calendar-event-fill"></i></div>
            <?php endif; ?>
            <?= Helpers\Helper::e($settings['church_name'] ?? 'Events') ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#pubNav" aria-label="Toggle menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="pubNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= $_SERVER['REQUEST_URI'] === $B() || $_SERVER['REQUEST_URI'] === $B('events') ? 'active' : '' ?>" href="<?= $B() ?>">
                        <i class="bi bi-calendar3 me-1"></i>Events
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="nav-link navbar-cta-btn" href="<?= $B('admin/login') ?>">
                        <i class="bi bi-lock me-1"></i>Admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Content ────────────────────────────────────────────────── -->
<main>
    <?= $content ?>
</main>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="pub-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a href="<?= $B() ?>" class="pub-footer-brand">
                    <div class="navbar-brand-icon" style="width:32px;height:32px;font-size:15px"><i class="bi bi-calendar-event-fill"></i></div>
                    <?= Helpers\Helper::e($settings['church_name'] ?? 'EMS') ?>
                </a>
                <p class="pub-footer-desc">A professional event management and registration platform for church programmes and conferences.</p>
            </div>
            <div class="col-6 col-lg-2 offset-lg-2">
                <div class="pub-footer-title">Quick Links</div>
                <a href="<?= $B() ?>" class="pub-footer-link">All Events</a>
                <a href="<?= $B('admin/login') ?>" class="pub-footer-link">Admin Portal</a>
            </div>
            <div class="col-6 col-lg-2">
                <div class="pub-footer-title">Support</div>
                <a href="<?= $B() ?>" class="pub-footer-link">Help Centre</a>
                <a href="<?= $B() ?>" class="pub-footer-link">Contact Us</a>
            </div>
        </div>
        <hr class="pub-footer-divider">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="pub-footer-bottom">&copy; <?= date('Y') ?> <?= Helpers\Helper::e($settings['church_name'] ?? 'EMS') ?>. All rights reserved.</span>
            <span class="pub-footer-bottom">Powered by EMS Platform</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</body>
</html>
