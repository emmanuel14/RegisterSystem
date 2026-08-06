<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'System Settings';
$groupLabels = ['general'=>'General', 'email'=>'Email / SMTP', 'registration'=>'Registration'];
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Settings</h2>
        <p class="page-subtitle">System configuration</p>
    </div>
</div>

<form method="POST" action="<?= Helpers\Helper::base('admin/settings') ?>">
    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $e($csrf) ?>">

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <?php $first = true; foreach ($groups as $groupKey => $_): ?>
        <li class="nav-item">
            <button class="nav-link <?= $first ? 'active' : '' ?>"
                    data-bs-toggle="tab" data-bs-target="#tab-<?= $groupKey ?>" type="button">
                <?= $e($groupLabels[$groupKey] ?? ucfirst($groupKey)) ?>
            </button>
        </li>
        <?php $first = false; endforeach; ?>
    </ul>

    <div class="tab-content">
        <?php $first = true; foreach ($groups as $groupKey => $items): ?>
        <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="tab-<?= $groupKey ?>">
            <div class="card ems-card">
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($items as $item): ?>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold"><?= $e($item['label']) ?></label>
                            <?php if ($item['type'] === 'textarea'): ?>
                                <textarea name="<?= $e($item['key']) ?>" class="form-control" rows="3"><?= $e($item['value'] ?? '') ?></textarea>
                            <?php elseif ($item['type'] === 'boolean'): ?>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox"
                                           name="<?= $e($item['key']) ?>" id="<?= $e($item['key']) ?>"
                                           value="1" <?= !empty($item['value']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="<?= $e($item['key']) ?>">Enabled</label>
                                </div>
                            <?php elseif ($item['type'] === 'color'): ?>
                                <div class="input-group">
                                    <input type="color" name="<?= $e($item['key']) ?>" class="form-control form-control-color"
                                           value="<?= $e($item['value'] ?? '#000000') ?>">
                                    <input type="text" class="form-control" value="<?= $e($item['value'] ?? '') ?>"
                                           oninput="document.querySelector('[name=\'<?= $item['key'] ?>\'][type=color]').value=this.value">
                                </div>
                            <?php elseif ($item['type'] === 'number'): ?>
                                <input type="number" name="<?= $e($item['key']) ?>" class="form-control"
                                       value="<?= $e($item['value'] ?? '') ?>">
                            <?php elseif ($item['type'] === 'email'): ?>
                                <input type="email" name="<?= $e($item['key']) ?>" class="form-control"
                                       value="<?= $e($item['value'] ?? '') ?>">
                            <?php else: ?>
                                <input type="<?= $item['type'] === 'password' ? 'password' : 'text' ?>"
                                       name="<?= $e($item['key']) ?>" class="form-control"
                                       value="<?= $e($item['value'] ?? '') ?>">
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php $first = false; endforeach; ?>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Save Settings
        </button>
    </div>
</form>
