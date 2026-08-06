<?php $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="bi bi-calendar-event-fill"></i>
        </div>
        <h1 class="auth-title">Admin Login</h1>
        <p class="auth-subtitle"><?= $e($settings['church_name'] ?? 'Event Management System') ?></p>

        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-danger small">
                <i class="bi bi-x-circle me-1"></i>
                <?= implode('<br>', array_map($e, $flash['error'])) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success small">
                <i class="bi bi-check-circle me-1"></i>
                <?= implode('<br>', array_map($e, $flash['success'])) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($flash['warning'])): ?>
            <div class="alert alert-warning small">
                <?= implode('<br>', array_map($e, $flash['warning'])) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= Helpers\Helper::base('admin/login') ?>" novalidate>
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $e($csrf) ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                           placeholder="admin@domain.com"
                           value="<?= $e($_POST['email'] ?? '') ?>"
                           required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passField" class="form-control"
                           placeholder="••••••••" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePass"
                            title="Show/hide password">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('togglePass').addEventListener('click', function () {
    const p = document.getElementById('passField');
    const i = document.getElementById('eyeIcon');
    if (p.type === 'password') {
        p.type = 'text';
        i.className = 'bi bi-eye-slash';
    } else {
        p.type = 'password';
        i.className = 'bi bi-eye';
    }
});
</script>
