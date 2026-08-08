<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Registration Confirmed';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<!-- Hero -->
<section class="success-hero">
    <div class="container" style="position:relative">
        <div class="success-icon-wrap">
            <i class="bi bi-check-lg"></i>
        </div>
        <h1 class="success-title">You're Registered!</h1>
        <p class="success-subtitle">Your registration for <strong style="color:rgba(255,255,255,.9)"><?= $e($reg['event_title']) ?></strong> is confirmed.</p>
    </div>
</section>

<div style="background:var(--body-bg);padding:40px 0 64px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <!-- Pass Card -->
                <div class="reg-pass-card mb-4">
                    <div class="reg-pass-header">
                        <div class="reg-pass-header-left">
                            <h3><?= $e($settings['church_name'] ?? 'Event Pass') ?></h3>
                            <p>Official Registration Confirmation</p>
                        </div>
                        <div class="reg-pass-status">✓ Confirmed</div>
                    </div>

                    <div class="reg-pass-body">
                        <!-- Registration Code -->
                        <div class="reg-code-display">
                            <div class="label">Registration Number</div>
                            <div class="code"><?= $e($reg['registration_code']) ?></div>
                        </div>

                        <div class="row g-4 align-items-center">
                            <!-- Details -->
                            <div class="col-md-7">
                                <div style="display:flex;flex-direction:column;gap:0">
                                    <?php
                                    $rows = [
                                        ['bi-person-fill', 'Attendee', $reg['first_name'] . ' ' . $reg['last_name']],
                                        ['bi-envelope-fill', 'Email', $reg['email']],
                                        ['bi-telephone-fill', 'Phone', $reg['phone']],
                                        ['bi-calendar3', 'Event', $reg['event_title']],
                                        ['bi-clock', 'Date', Helpers\Helper::formatDate($reg['start_date'])],
                                    ];
                                    if ($reg['venue']) $rows[] = ['bi-geo-alt-fill','Venue',$reg['venue']];
                                    if ($reg['church_name']) $rows[] = ['bi-building','Church',$reg['church_name']];
                                    foreach ($rows as [$icon, $label, $value]):
                                    ?>
                                    <div style="display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #f0f4f8">
                                        <div style="width:32px;height:32px;border-radius:8px;background:var(--pub-primary-light);display:flex;align-items:center;justify-content:center;color:var(--pub-primary);font-size:14px;flex-shrink:0">
                                            <i class="bi <?= $icon ?>"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted)"><?= $label ?></div>
                                            <div style="font-size:13.5px;font-weight:600;color:var(--text-primary);margin-top:1px"><?= $e($value) ?></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- QR Code -->
                            <?php if ($qrUrl): ?>
                            <div class="col-md-5">
                                <div class="qr-section">
                                    <img src="<?= $e($qrUrl) ?>" alt="QR Code">
                                    <div class="qr-caption">Scan at event entrance for check-in</div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row g-3 mb-4">
                    <?php if ($qrUrl): ?>
                    <div class="col-sm-6">
                        <a href="<?= $B('registration/download-qr/' . $e($code)) ?>" class="pub-btn pub-btn-primary w-100 justify-content-center" style="border-radius:10px;padding:13px">
                            <i class="bi bi-download"></i> Download QR Code
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="col-sm-6">
                        <button onclick="window.print()" class="pub-btn pub-btn-outline-dark w-100 justify-content-center" style="border-radius:10px;padding:13px">
                            <i class="bi bi-printer"></i> Print Pass
                        </button>
                    </div>
                </div>

                <!-- Info Box -->
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:18px 20px;display:flex;gap:14px;align-items:flex-start">
                    <i class="bi bi-envelope-check-fill flex-shrink-0 mt-1" style="font-size:20px;color:#3b82f6"></i>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#1e40af;margin-bottom:3px">Check your email</div>
                        <div style="font-size:13.5px;color:#1e40af;opacity:.8">
                            A confirmation email with your QR code has been sent to <strong><?= $e($reg['email']) ?></strong>.
                            Check your spam folder if you don't see it.
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= $B() ?>" style="color:var(--text-secondary);text-decoration:none;font-size:13.5px">
                        <i class="bi bi-arrow-left me-1"></i> Browse more events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    nav, footer, .pub-btn, .pub-footer, .col-sm-6:has(button) { display: none !important; }
    .reg-pass-card { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>
