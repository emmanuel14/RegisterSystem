<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? Helpers\Helper::e($pageTitle) . ' — ' : '' ?><?= Helpers\Helper::e($settings['church_name'] ?? 'Event Registration') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= Helpers\Helper::asset('css/public.css') ?>" rel="stylesheet">
</head>
<body>

<?php $B = fn(string $p = '') => Helpers\Helper::base($p); ?>

<nav class="navbar navbar-expand-lg pub-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $B() ?>">
            <?php if (!empty($settings['church_logo'])): ?>
                <img src="<?= Helpers\Helper::e($settings['church_logo']) ?>" height="36" alt="Logo">
            <?php else: ?>
                <i class="bi bi-calendar-event-fill" style="font-size:1.5rem;color:var(--pub-primary)"></i>
            <?php endif; ?>
            <strong><?= Helpers\Helper::e($settings['church_name'] ?? 'Event Registration') ?></strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#pubNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="pubNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= $B() ?>">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $B('admin/login') ?>">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<main>
    <?= $content ?>
</main>

<footer class="pub-footer">
    <div class="container text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> <?= Helpers\Helper::e($settings['church_name'] ?? '') ?> &mdash; Powered by EMS</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</body>
</html>
