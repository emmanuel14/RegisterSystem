<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Event Check-In';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <?php if (!$reg): ?>
                <i class="bi bi-x-circle-fill text-danger" style="font-size:4rem"></i>
                <h2 class="mt-3">Invalid QR Code</h2>
                <p class="text-muted">This registration code is not recognised.</p>
            <?php else: ?>
                <div class="card border-0 shadow">
                    <div class="card-body p-5">
                        <?php if ($reg['checked_in_at']): ?>
                            <i class="bi bi-check-circle-fill text-success" style="font-size:4rem"></i>
                            <h2 class="mt-3 text-success">Already Checked In</h2>
                            <p class="text-muted">This attendee was checked in on<br>
                            <strong><?= Helpers\Helper::formatDateTime($reg['checked_in_at']) ?></strong></p>
                        <?php else: ?>
                            <i class="bi bi-person-check-fill text-primary" style="font-size:4rem"></i>
                            <h2 class="mt-3">Ready to Check In</h2>
                        <?php endif; ?>

                        <hr>
                        <div class="text-start">
                            <dl class="info-list info-list--2col">
                                <dt>Name</dt><dd><?= $e($reg['first_name'] . ' ' . $reg['last_name']) ?></dd>
                                <dt>Code</dt><dd><code><?= $e($reg['registration_code']) ?></code></dd>
                                <dt>Event</dt><dd><?= $e($reg['event_title']) ?></dd>
                                <dt>Church</dt><dd><?= $e($reg['church_name'] ?: '—') ?></dd>
                            </dl>
                        </div>
                        <div class="text-muted small mt-3">
                            Please present this to an event official for check-in.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
