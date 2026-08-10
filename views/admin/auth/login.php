<?php $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-subtitle">Sign in to <?= $e($settings['church_name'] ?? 'EMS') ?> admin panel</p>
        </div>

        <!-- Alerts -->
        <?php if (!empty($flash['error'])): ?>
            <div class="mb-4 p-3 rounded-3 d-flex align-items-start gap-2" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
                <i class="bi bi-exclamation-circle-fill mt-1 flex-shrink-0"></i>
                <span style="font-size:13.5px"><?= implode('<br>', array_map($e, (array)$flash['error'])) ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($flash['success'])): ?>
            <div class="mb-4 p-3 rounded-3 d-flex align-items-start gap-2" style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46">
                <i class="bi bi-check-circle-fill mt-1 flex-shrink-0"></i>
                <span style="font-size:13.5px"><?= implode('<br>', array_map($e, (array)$flash['success'])) ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($flash['warning'])): ?>
            <div class="mb-4 p-3 rounded-3 d-flex align-items-start gap-2" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e">
                <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                <span style="font-size:13.5px"><?= implode('<br>', array_map($e, (array)$flash['warning'])) ?></span>
            </div>
        <?php endif; ?>s

        <!-- Form -->
        <form method="POST" action="<?= Helpers\Helper::base('admin/login') ?>" novalidate>
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $e($csrf) ?>">

            <div class="mb-4">
                <label class="form-label" style="font-size:13px;font-weight:600;color:#374151">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                           placeholder="admin@yourdomain.com"
                           value="<?= $e($_POST['email'] ?? '') ?>"
                           required autofocus autocomplete="email"
                           style="border-left:none">
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label" style="font-size:13px;font-weight:600;color:#374151">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passField" class="form-control"
                           placeholder="Enter your password" required autocomplete="current-password"
                           style="border-left:none;border-right:none">
                    <button type="button" class="input-group-text" id="togglePass" style="cursor:pointer;background:#f8fafc">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg" style="border-radius:10px;font-size:15px;font-weight:700;padding:13px">
                <i class="bi bi-arrow-right-circle me-2"></i>Sign In
            </button>
        </form>

        <div class="auth-divider"></div>
        <p style="text-align:center;font-size:12.5px;color:#94a3b8;margin:0">
            <i class="bi bi-shield-lock me-1"></i>
            Secured with end-to-end encryption
        </p>
    </div>
</div>

<script>
document.getElementById('togglePass').addEventListener('click', function () {
    const p = document.getElementById('passField');
    const i = document.getElementById('eyeIcon');
    if (p.type === 'password') { p.type = 'text';     i.className = 'bi bi-eye-slash'; }
    else                       { p.type = 'password'; i.className = 'bi bi-eye'; }
});
</script>
