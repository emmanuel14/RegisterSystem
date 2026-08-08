<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Settings';
$B = fn(string $p = '') => Helpers\Helper::base($p);
$groupMeta = [
    'general'      => ['label'=>'General',      'icon'=>'bi-gear-fill',        'desc'=>'Basic organisation and display settings'],
    'email'        => ['label'=>'Email / SMTP',  'icon'=>'bi-envelope-fill',    'desc'=>'Outgoing email configuration'],
    'registration' => ['label'=>'Registration',  'icon'=>'bi-person-plus-fill', 'desc'=>'Registration code and confirmation settings'],
];
$activeGroup = array_key_first($groups);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">Configure your EMS platform</p>
    </div>
</div>

<div class="row g-4">
    <!-- Sidebar nav -->
    <div class="col-lg-3">
        <div class="ems-card">
            <div class="card-body" style="padding:8px">
                <?php foreach ($groups as $gKey => $_): ?>
                <?php $meta = $groupMeta[$gKey] ?? ['label'=>ucfirst($gKey),'icon'=>'bi-gear','desc'=>'']; ?>
                <button onclick="showTab('<?= $gKey ?>')" id="tab-btn-<?= $gKey ?>"
                    class="w-100 text-start d-flex align-items-center gap-3 border-0 bg-transparent p-3 mb-1"
                    style="border-radius:var(--r-md);transition:all var(--transition);cursor:pointer;<?= $gKey===$activeGroup?'background:var(--ems-primary-light);color:var(--ems-primary);':'' ?>">
                    <div style="width:34px;height:34px;border-radius:8px;background:<?= $gKey===$activeGroup?'var(--ems-primary)':'#f1f5f9' ?>;display:flex;align-items:center;justify-content:center;font-size:15px;color:<?= $gKey===$activeGroup?'#fff':'var(--text-muted)' ?>;flex-shrink:0">
                        <i class="bi <?= $meta['icon'] ?>"></i>
                    </div>
                    <div style="text-align:left">
                        <div style="font-size:13.5px;font-weight:700;color:<?= $gKey===$activeGroup?'var(--ems-primary)':'var(--text-primary)' ?>"><?= $e($meta['label']) ?></div>
                        <div style="font-size:11.5px;color:var(--text-muted)"><?= $e($meta['desc']) ?></div>
                    </div>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Form panels -->
    <div class="col-lg-9">
        <form method="POST" action="<?= $B('admin/settings') ?>">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $e($csrf) ?>">

            <?php foreach ($groups as $gKey => $items): ?>
            <?php $meta = $groupMeta[$gKey] ?? ['label'=>ucfirst($gKey),'icon'=>'bi-gear','desc'=>'']; ?>
            <div id="tab-panel-<?= $gKey ?>" style="<?= $gKey!==$activeGroup?'display:none':'' ?>">
                <div class="ems-card mb-4">
                    <div class="card-header">
                        <div>
                            <div class="card-title"><?= $e($meta['label']) ?> Settings</div>
                            <div class="card-subtitle"><?= $e($meta['desc']) ?></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <?php foreach ($items as $item): ?>
                            <div class="col-sm-6">
                                <label class="form-label"><?= $e($item['label']) ?></label>
                                <?php if ($item['type'] === 'textarea'): ?>
                                    <textarea name="<?= $e($item['key']) ?>" class="form-control" rows="3"><?= $e($item['value'] ?? '') ?></textarea>
                                <?php elseif ($item['type'] === 'boolean'): ?>
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               name="<?= $e($item['key']) ?>" id="sw_<?= $e($item['key']) ?>"
                                               value="1" <?= !empty($item['value'])?'checked':'' ?>>
                                        <label class="form-check-label" for="sw_<?= $e($item['key']) ?>">
                                            <?= !empty($item['value']) ? 'Enabled' : 'Disabled' ?>
                                        </label>
                                    </div>
                                <?php elseif ($item['type'] === 'color'): ?>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="color" name="<?= $e($item['key']) ?>" id="color_<?= $e($item['key']) ?>"
                                               value="<?= $e($item['value'] ?? '#000000') ?>"
                                               style="width:44px;height:44px;border-radius:8px;border:1.5px solid var(--border);padding:2px;cursor:pointer">
                                        <input type="text" class="form-control" id="colorText_<?= $e($item['key']) ?>"
                                               value="<?= $e($item['value'] ?? '') ?>"
                                               oninput="document.getElementById('color_<?= $item['key'] ?>').value=this.value">
                                    </div>
                                    <script>
                                    document.getElementById('color_<?= $item['key'] ?>').addEventListener('input', function(){
                                        document.getElementById('colorText_<?= $item['key'] ?>').value = this.value;
                                    });
                                    </script>
                                <?php elseif ($item['type'] === 'number'): ?>
                                    <input type="number" name="<?= $e($item['key']) ?>" class="form-control" value="<?= $e($item['value'] ?? '') ?>">
                                <?php elseif ($item['type'] === 'email'): ?>
                                    <input type="email" name="<?= $e($item['key']) ?>" class="form-control" value="<?= $e($item['value'] ?? '') ?>">
                                <?php else: ?>
                                    <input type="text" name="<?= $e($item['key']) ?>" class="form-control" value="<?= $e($item['value'] ?? '') ?>">
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Save Bar -->
            <div style="background:#fff;border:1px solid var(--border);border-radius:var(--r-lg);padding:18px 22px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow-sm)">
                <span style="font-size:13.5px;color:var(--text-secondary)">
                    <i class="bi bi-info-circle me-1" style="color:var(--ems-gold)"></i>
                    Changes take effect immediately after saving.
                </span>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(key) {
    document.querySelectorAll('[id^="tab-panel-"]').forEach(p => p.style.display = 'none');
    document.querySelectorAll('[id^="tab-btn-"]').forEach(b => {
        b.style.background = 'transparent';
        b.querySelector('div[style*="font-size:13.5px"]').style.color = 'var(--text-primary)';
        const icon = b.querySelector('div[style*="width:34px"]');
        icon.style.background = '#f1f5f9';
        icon.style.color = 'var(--text-muted)';
    });
    const panel = document.getElementById('tab-panel-' + key);
    const btn   = document.getElementById('tab-btn-' + key);
    if (panel) panel.style.display = '';
    if (btn) {
        btn.style.background = 'var(--ems-primary-light)';
        btn.querySelector('div[style*="font-size:13.5px"]').style.color = 'var(--ems-primary)';
        const icon = btn.querySelector('div[style*="width:34px"]');
        icon.style.background = 'var(--ems-primary)';
        icon.style.color = '#fff';
    }
}
</script>
