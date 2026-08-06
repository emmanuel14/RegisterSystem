<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Registration Confirmed';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- Success Banner -->
            <div class="pub-success-banner text-center mb-4">
                <div class="pub-success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h1 class="pub-success-title">Registration Successful!</h1>
                <p class="pub-success-sub">You are now registered for <strong><?= $e($reg['event_title']) ?></strong></p>
            </div>

            <!-- Registration Card -->
            <div class="card border-0 shadow mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h5 class="fw-bold mb-3">Registration Details</h5>
                            <dl class="info-list">
                                <dt>Registration Number</dt>
                                <dd><code class="fs-6 fw-bold text-primary"><?= $e($reg['registration_code']) ?></code></dd>
                                <dt>Name</dt>
                                <dd><?= $e($reg['first_name'] . ' ' . $reg['last_name']) ?></dd>
                                <dt>Email</dt>
                                <dd><?= $e($reg['email']) ?></dd>
                                <dt>Event</dt>
                                <dd><?= $e($reg['event_title']) ?></dd>
                                <dt>Date</dt>
                                <dd><?= Helpers\Helper::formatDate($reg['start_date']) ?></dd>
                                <?php if ($reg['venue']): ?>
                                <dt>Venue</dt>
                                <dd><?= $e($reg['venue']) ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                        <div class="col-md-5 text-center mt-4 mt-md-0">
                            <?php if ($qrUrl): ?>
                                <img src="<?= $e($qrUrl) ?>" alt="QR Code" class="img-fluid pub-qr-img mb-2">
                                <div class="small text-muted">Scan at event entrance</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row g-3 mb-4">
                <?php if ($qrUrl): ?>
                <div class="col-sm-6">
                    <a href="<?= Helpers\Helper::base('registration/download-qr/' . $e($code)) ?>" class="btn pub-btn-primary w-100">
                        <i class="bi bi-download me-2"></i> Download QR Code
                    </a>
                </div>
                <?php endif; ?>
                <div class="col-sm-6">
                    <button onclick="window.print()" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-printer me-2"></i> Print Pass
                    </button>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info d-flex gap-3">
                <i class="bi bi-envelope-fill fs-4 flex-shrink-0 mt-1"></i>
                <div>
                    <strong>Confirmation Email Sent</strong><br>
                    A confirmation email with your QR code has been sent to <strong><?= $e($reg['email']) ?></strong>.
                    Please check your spam folder if you don't see it.
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="/" class="btn btn-link text-muted">← Back to Events</a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    nav, footer, .btn, .alert { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>
